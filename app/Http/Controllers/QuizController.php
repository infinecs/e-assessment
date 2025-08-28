<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentEvent;
use App\Models\AssessmentTopic;
use App\Models\AssessmentResultSet;
use App\Models\AssessmentAnswer;
use App\Models\Assessment;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    private const SESSION_TIMEOUT = 7200; // 2 hours in seconds
    private const HEARTBEAT_TIMEOUT = 45; // 45 seconds - more forgiving
    private const TAB_CHECK_INTERVAL = 10; // 10 seconds for tab checking
    
    public function showQuiz($eventCode)
    {
        try {
            // Get event first to validate it exists
            $event = AssessmentEvent::where('EventCode', $eventCode)->firstOrFail();
            
            // Check if participant is logged in
            $email = session('participant_email');
            if (!$email) {
                return redirect()->route('participant.login')
                    ->with('error', 'Please log in to access the quiz.');
            }

            // Check if quiz is already completed
            if (session("quiz_completed_$eventCode")) {
                return redirect()->route('quiz.results', $eventCode);
            }

            // Always check for existing questions first
            if (session()->has("quiz_questions_$eventCode")) {
                $questionIds = session("quiz_questions_$eventCode");
                $questions = AssessmentQuestion::whereIn('QuestionID', $questionIds)
                    ->orderByRaw("FIELD(QuestionID, " . implode(',', array_map('intval', $questionIds)) . ")")
                    ->get();
            } else {
                // Generate new question set
                $questions = $this->generateQuestionSet($event);
                
                if ($questions->isEmpty()) {
                    return redirect()->back()->with('error', 'No questions available for this assessment.');
                }
                
                // Store question IDs in session
                session(["quiz_questions_$eventCode" => $questions->pluck('QuestionID')->toArray()]);
            }

            // Get saved answers from session
            $savedAnswers = session("quiz_answers_$eventCode", []);

            // Handle session initialization
            $isNewSession = $this->initializeQuizSession($eventCode);

            return view('participants.quizPage', [
                'eventCode' => $eventCode,
                'questions' => $questions,
                'savedAnswers' => $savedAnswers,
                'assessment' => $event,
                'errors' => session()->get('errors'),
                'isNewSession' => $isNewSession
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error showing quiz: " . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load quiz. Please try again.');
        }
    }

    private function initializeQuizSession($eventCode)
    {
        $email = session('participant_email');
        $sessionKey = "quiz_session_{$eventCode}_{$email}";
        $startTimeKey = "quiz_start_time_{$eventCode}_{$email}";
        
        // Check if this is a brand new session
        $isNewSession = !session()->has("quiz_started_$eventCode") || request()->has('new_session');
        
        if ($isNewSession) {
            // Clear any existing session data
            session()->forget([
                "quiz_answers_$eventCode",
                "quiz_timer_remaining_$eventCode"
            ]);
            
            // Set new session markers
            session([
                "quiz_started_$eventCode" => true,
                "quiz_session_id_$eventCode" => uniqid('quiz_', true),
                "quiz_start_time_$eventCode" => now()->timestamp
            ]);
            
            // Clear server-side cache for fresh start
            Cache::forget("quiz_active_{$eventCode}_{$email}");
            Cache::forget("quiz_heartbeat_{$eventCode}_{$email}");
            Cache::forget("quiz_timer_{$eventCode}_{$email}");
        }
        
        return $isNewSession;
    }

    private function generateQuestionSet($event)
    {
        $topicIds = array_filter(array_map('trim', explode(',', $event->TopicID ?? '')));
        $questionLimit = (int)$event->QuestionLimit;
        $weightages = $event->TopicWeightages ?? [];

        if (empty($topicIds)) {
            return collect();
        }

        $allQuestions = collect();
        $topicQuestionCounts = [];

        // If weightages are not set or all zero, distribute equally
        $hasWeightage = false;
        foreach ($topicIds as $topicId) {
            if (!empty($weightages[$topicId]) && $weightages[$topicId] > 0) {
                $hasWeightage = true;
                break;
            }
        }

        if ($hasWeightage) {
            $totalAssigned = 0;
            foreach ($topicIds as $topicId) {
                $weight = isset($weightages[$topicId]) ? (int)$weightages[$topicId] : 0;
                $count = ($weight > 0) ? round(($weight / 100) * $questionLimit) : 0;
                $topicQuestionCounts[$topicId] = $count;
                $totalAssigned += $count;
            }
            // Adjust for rounding errors
            if ($totalAssigned !== $questionLimit && count($topicIds) > 0) {
                $diff = $questionLimit - $totalAssigned;
                $sorted = $topicIds;
                usort($sorted, function($a, $b) use ($weightages) {
                    return ($weightages[$b] ?? 0) <=> ($weightages[$a] ?? 0);
                });
                $i = 0;
                while ($diff !== 0 && $i < 1000) {
                    $tid = $sorted[$i % count($sorted)];
                    if ($diff > 0) {
                        $topicQuestionCounts[$tid]++;
                        $diff--;
                    } else if ($diff < 0 && $topicQuestionCounts[$tid] > 0) {
                        $topicQuestionCounts[$tid]--;
                        $diff++;
                    }
                    $i++;
                }
            }
        } else {
            // Distribute equally if no weightage
            $perTopic = intdiv($questionLimit, count($topicIds));
            $remainder = $questionLimit % count($topicIds);
            foreach ($topicIds as $i => $topicId) {
                $topicQuestionCounts[$topicId] = $perTopic + ($i < $remainder ? 1 : 0);
            }
        }

        // Select questions per topic
        foreach ($topicIds as $topicId) {
            $topic = AssessmentTopic::find($topicId);
            if (!$topic || !$topic->QuestionID) continue;
            $ids = array_filter(array_map('trim', explode(',', $topic->QuestionID)));
            if (empty($ids)) continue;
            shuffle($ids);
            $count = $topicQuestionCounts[$topicId] ?? 0;
            $selected = array_slice($ids, 0, $count);
            if (!empty($selected)) {
                $qs = AssessmentQuestion::whereIn('QuestionID', $selected)->get();
                $allQuestions = $allQuestions->merge($qs);
            }
        }

        // If still not enough, fill only from selected topics (unique only, no repeats)
        $needed = $questionLimit - $allQuestions->count();
        if ($needed > 0) {
            // Gather all available question IDs from selected topics
            $allTopicIds = AssessmentTopic::whereIn('TopicID', $topicIds)
                ->whereNotNull('QuestionID')
                ->where('QuestionID', '!=', '')
                ->pluck('QuestionID')
                ->filter()
                ->toArray();
            $allIds = [];
            foreach ($allTopicIds as $questionIdString) {
                $ids = array_filter(array_map('trim', explode(',', $questionIdString)));
                $allIds = array_merge($allIds, $ids);
            }
            $allIds = array_unique($allIds);
            $alreadyPicked = $allQuestions->pluck('QuestionID')->toArray();
            $remainingIds = array_values(array_diff($allIds, $alreadyPicked));
            if (!empty($remainingIds)) {
                shuffle($remainingIds);
                $extraIds = array_slice($remainingIds, 0, $needed);
                $extraQuestions = AssessmentQuestion::whereIn('QuestionID', $extraIds)->get();
                $allQuestions = $allQuestions->merge($extraQuestions);
            }
        }

        // Only use unique questions, and if not enough, return as many as available (no repeats)
        $uniqueQuestions = $allQuestions->unique('QuestionID')->values();
        if ($uniqueQuestions->count() > $questionLimit) {
            return $uniqueQuestions->take($questionLimit);
        } else {
            return $uniqueQuestions;
        }
    }

    public function checkActiveSession(Request $request, $eventCode)
    {
        try {
            $email = session('participant_email');
            if (!$email) {
                return response()->json([
                    'allowed' => false, 
                    'message' => 'No participant session found',
                    'action' => 'redirect_login'
                ]);
            }

            $currentTabId = $request->input('tabId');
            $forceNew = $request->input('force_new', false);
            
            if (!$currentTabId) {
                return response()->json([
                    'allowed' => false, 
                    'message' => 'Invalid tab ID',
                    'action' => 'refresh'
                ]);
            }

            $activeSessionKey = "quiz_active_{$eventCode}_{$email}";
            $heartbeatKey = "quiz_heartbeat_{$eventCode}_{$email}";
            $tabIdKey = "quiz_tab_id_{$eventCode}_{$email}";

            // If force_new is requested, clear existing session
            if ($forceNew) {
                Cache::forget($activeSessionKey);
                Cache::forget($heartbeatKey);
                Cache::forget($tabIdKey);
                
                // Also clear session data for fresh start
                session()->forget([
                    "quiz_started_$eventCode",
                    "quiz_answers_$eventCode"
                ]);
            }

            // Check for existing active session
            $activeSession = Cache::get($activeSessionKey);
            $lastHeartbeat = Cache::get($heartbeatKey);
            $storedTabId = Cache::get($tabIdKey);

            if ($activeSession && $storedTabId !== $currentTabId) {
                // Check if the other session is still alive
                if ($lastHeartbeat && (time() - $lastHeartbeat) < self::HEARTBEAT_TIMEOUT) {
                    return response()->json([
                        'allowed' => false,
                        'message' => 'Quiz is already active in another browser tab or device. Please close other sessions first.',
                        'action' => 'show_takeover_option',
                        'existing_session_age' => time() - $lastHeartbeat
                    ]);
                }
            }

            // Set this session as active
            Cache::put($activeSessionKey, true, self::SESSION_TIMEOUT);
            Cache::put($heartbeatKey, time(), self::SESSION_TIMEOUT);
            Cache::put($tabIdKey, $currentTabId, self::SESSION_TIMEOUT);

            return response()->json([
                'allowed' => true,
                'session_timeout' => self::SESSION_TIMEOUT,
                'heartbeat_interval' => 15
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error checking active session: " . $e->getMessage());
            return response()->json([
                'allowed' => false, 
                'message' => 'Server error occurred',
                'action' => 'retry'
            ], 500);
        }
    }

    public function takeoverSession(Request $request, $eventCode)
    {
        try {
            $email = session('participant_email');
            $currentTabId = $request->input('tabId');
            
            if (!$email || !$currentTabId) {
                return response()->json(['success' => false, 'message' => 'Invalid request']);
            }

            $activeSessionKey = "quiz_active_{$eventCode}_{$email}";
            $heartbeatKey = "quiz_heartbeat_{$eventCode}_{$email}";
            $tabIdKey = "quiz_tab_id_{$eventCode}_{$email}";

            // Force takeover - clear existing session and set new one
            Cache::put($activeSessionKey, true, self::SESSION_TIMEOUT);
            Cache::put($heartbeatKey, time(), self::SESSION_TIMEOUT);
            Cache::put($tabIdKey, $currentTabId, self::SESSION_TIMEOUT);

            return response()->json(['success' => true, 'message' => 'Session taken over successfully']);
            
        } catch (\Exception $e) {
            Log::error("Error taking over session: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    public function heartbeat(Request $request, $eventCode)
    {
        try {
            $email = session('participant_email');
            if (!$email) {
                return response()->json(['active' => false, 'reason' => 'no_session']);
            }

            $currentTabId = $request->input('tabId');
            if (!$currentTabId) {
                return response()->json(['active' => false, 'reason' => 'invalid_tab']);
            }

            $activeSessionKey = "quiz_active_{$eventCode}_{$email}";
            $heartbeatKey = "quiz_heartbeat_{$eventCode}_{$email}";
            $tabIdKey = "quiz_tab_id_{$eventCode}_{$email}";
            
            $storedTabId = Cache::get($tabIdKey);

            if ($storedTabId === $currentTabId) {
                // Update heartbeat and extend session
                Cache::put($heartbeatKey, time(), self::SESSION_TIMEOUT);
                Cache::put($activeSessionKey, true, self::SESSION_TIMEOUT);
                Cache::put($tabIdKey, $currentTabId, self::SESSION_TIMEOUT);
                
                return response()->json([
                    'active' => true,
                    'server_time' => time(),
                    'remaining_time' => self::SESSION_TIMEOUT
                ]);
            }

            return response()->json([
                'active' => false, 
                'reason' => 'session_taken_over',
                'message' => 'Your session has been taken over by another tab or device'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error in heartbeat: " . $e->getMessage());
            return response()->json(['active' => false, 'reason' => 'server_error'], 500);
        }
    }

    public function clearActiveSession(Request $request, $eventCode)
    {
        try {
            $email = session('participant_email');
            if ($email) {
                $activeSessionKey = "quiz_active_{$eventCode}_{$email}";
                $heartbeatKey = "quiz_heartbeat_{$eventCode}_{$email}";
                $tabIdKey = "quiz_tab_id_{$eventCode}_{$email}";
                
                Cache::forget($activeSessionKey);
                Cache::forget($heartbeatKey);
                Cache::forget($tabIdKey);
            }

            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error("Error clearing active session: " . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function saveAnswer(Request $request, $eventCode)
    {
        try {
            $request->validate([
                'questionId' => 'required|integer',
                'value' => 'required|string'
            ]);

            $answers = session("quiz_answers_$eventCode", []);
            $answers[$request->questionId] = $request->value;
            session(["quiz_answers_$eventCode" => $answers]);

            return response()->json(['status' => 'saved', 'timestamp' => now()->timestamp]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function clearAnswers(Request $request, $eventCode)
    {
        session()->forget("quiz_answers_$eventCode");
        return response()->json(['status' => 'cleared']);
    }

    public function submitQuiz(Request $request, $eventCode)
    {
        try {
            $questionIds = session("quiz_questions_$eventCode", []);
            
            if (empty($questionIds)) {
                return redirect()->route('quiz.show', $eventCode)
                    ->with('error', 'Session expired, please retake the quiz.');
            }

            // Validate all questions are answered
            $rules = [];
            $messages = [];
            
            foreach ($questionIds as $qid) {
                $rules["answers.$qid"] = 'required';
                $messages["answers.$qid.required"] = "Question $qid is required.";
            }

            $validator = Validator::make($request->all(), $rules, $messages);
            
            if ($validator->fails()) {
                session(["quiz_answers_$eventCode" => $request->input('answers', [])]);
                return redirect()
                    ->route('quiz.show', $eventCode)
                    ->withErrors($validator)
                    ->withInput();
            }

            // Get participant and event info
            $email = session('participant_email');
            $participantId = 0;
            
            if ($email) {
                $participant = Participant::where('email', $email)->first();
                if ($participant) {
                    $participantId = $participant->id;
                }
            }

            $event = AssessmentEvent::where('EventCode', $eventCode)->first();
            $eventId = $event ? $event->EventID : null;

            // Calculate score
            $answers = $request->input('answers', []);
            $score = 0;
            $total = count($questionIds);
            
            foreach (array_unique($questionIds) as $qid) {
                if (!isset($answers[$qid])) continue;
                
                $selectedOption = $answers[$qid];
                $answerList = AssessmentAnswer::where('QuestionID', $qid)->get();
                
                foreach ($answerList as $index => $ans) {
                    if (chr(65 + $index) === $selectedOption && $ans->ExpectedAnswer === 'Y') {
                        $score++;
                        break;
                    }
                }
            }

            // Save assessment and results
            DB::transaction(function () use ($participantId, $score, $total, $answers, $questionIds, $eventId) {
                $assessment = Assessment::create([
                    'ParticipantID' => $participantId,
                    'TotalScore' => $score,
                    'TotalQuestion' => $total,
                    'EventID' => $eventId,
                    'AdminID' => 0,
                    'DateCreate' => now(),
                    'DateUpdate' => now(),
                ]);

                foreach (array_unique($questionIds) as $qid) {
                    $answerLetter = $answers[$qid] ?? null;
                    $answerId = null;
                    
                    if ($answerLetter) {
                        $answerList = AssessmentAnswer::where('QuestionID', $qid)->get();
                        foreach ($answerList as $index => $ans) {
                            if (chr(65 + $index) === $answerLetter) {
                                $answerId = $ans->AnswerID;
                                break;
                            }
                        }
                    }
                    
                    AssessmentResultSet::create([
                        'AssessmentID' => $assessment->AssessmentID,
                        'QuestionID' => $qid,
                        'AnswerID' => $answerId,
                        'DateCreate' => now(),
                    ]);
                }
            });

            // Store result and mark as completed
            session([
                "quiz_result_$eventCode" => [
                    'score' => $score,
                    'total' => $total,
                ],
                "quiz_completed_$eventCode" => true
            ]);

            // Clean up session data
            session()->forget([
                "quiz_questions_$eventCode",
                "quiz_answers_$eventCode",
                "quiz_started_$eventCode",
                "quiz_session_id_$eventCode"
            ]);

            // Clear server-side session tracking
            $this->clearActiveSession($request, $eventCode);

            return redirect()->route('quiz.results', $eventCode);
            
        } catch (\Exception $e) {
            Log::error("Error submitting quiz: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error submitting quiz. Please try again.');
        }
    }

    public function autoSubmitQuiz(Request $request, $eventCode)
{
    try {
        $questionIds = session("quiz_questions_$eventCode", []);
        
        if (empty($questionIds)) {
            return response()->json(['status' => 'error', 'message' => 'Session expired']);
        }

        $email = session('participant_email');
        $participantId = 0;
        
        if ($email) {
            $participant = Participant::where('email', $email)->first();
            if ($participant) {
                $participantId = $participant->id;
            }
        }

        $event = AssessmentEvent::where('EventCode', $eventCode)->first();
        $eventId = $event ? $event->EventID : null;

        // Check if already submitted to prevent duplicates
        $existingAssessment = Assessment::where('ParticipantID', $participantId)
                                        ->where('EventID', $eventId)
                                        ->first();

        if ($existingAssessment) {
            // If assessment exists, use its data for the result
            session([
                "quiz_result_$eventCode" => [
                    'score' => $existingAssessment->TotalScore,
                    'total' => $existingAssessment->TotalQuestion,
                ],
                "quiz_completed_$eventCode" => true
            ]);
            
            return response()->json(['status' => 'already_submitted']);
        }

        // Get answers - from request or session as fallback
        $answers = $request->input('answers', []);
        if (empty($answers)) {
            $answers = session("quiz_answers_$eventCode", []);
        }

        $score = 0;
        $total = count($questionIds);
        
        // Calculate score for answered questions only
        foreach (array_unique($questionIds) as $qid) {
            if (!isset($answers[$qid])) continue;
            
            $selectedOption = $answers[$qid];
            $answerList = AssessmentAnswer::where('QuestionID', $qid)->get();
            
            foreach ($answerList as $index => $ans) {
                if (chr(65 + $index) === $selectedOption && $ans->ExpectedAnswer === 'Y') {
                    $score++;
                    break;
                }
            }
        }

        // Save assessment and results in transaction
        DB::transaction(function () use ($participantId, $score, $total, $answers, $questionIds, $eventId, $eventCode) {
            $assessment = Assessment::create([
                'ParticipantID' => $participantId,
                'TotalScore' => $score,
                'TotalQuestion' => $total,
                'EventID' => $eventId,
                'AdminID' => 0,
                'DateCreate' => now(),
                'DateUpdate' => now(),
            ]);

            // Save results for all questions (including unanswered ones)
            foreach (array_unique($questionIds) as $qid) {
                $answerLetter = $answers[$qid] ?? null;
                $answerId = null;
                
                if ($answerLetter) {
                    $answerList = AssessmentAnswer::where('QuestionID', $qid)->get();
                    foreach ($answerList as $index => $ans) {
                        if (chr(65 + $index) === $answerLetter) {
                            $answerId = $ans->AnswerID;
                            break;
                        }
                    }
                }
                
                AssessmentResultSet::create([
                    'AssessmentID' => $assessment->AssessmentID,
                    'QuestionID' => $qid,
                    'AnswerID' => $answerId, // Will be null for unanswered questions
                    'DateCreate' => now(),
                ]);
            }

            // Store result for the results page
            session([
                "quiz_result_$eventCode" => [
                    'score' => $score,
                    'total' => $total,
                ],
                "quiz_completed_$eventCode" => true
            ]);
        });

        // Clean up session data
        session()->forget([
            "quiz_questions_$eventCode",
            "quiz_answers_$eventCode",
            "quiz_started_$eventCode",
            "quiz_session_id_$eventCode"
        ]);

        // Clear server-side session tracking
        $this->clearActiveSession($request, $eventCode);

        return response()->json(['status' => 'submitted', 'score' => $score, 'total' => $total]);
        
    } catch (\Exception $e) {
        Log::error("Error auto-submitting quiz: " . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => 'Server error during auto-submit'], 500);
    }
}


   public function showResults($eventCode)
{
    // First check if quiz was completed
    if (!session("quiz_completed_$eventCode")) {
        return redirect()->route('quiz.show', $eventCode)
               ->with('error', 'Please complete the quiz first.');
    }

    $result = session("quiz_result_$eventCode");
    
    // If no result in session, try to get from database
    if (!$result) {
        $email = session('participant_email');
        if ($email) {
            $participant = Participant::where('email', $email)->first();
            if ($participant) {
                $event = AssessmentEvent::where('EventCode', $eventCode)->first();
                if ($event) {
                    $assessment = Assessment::where('ParticipantID', $participant->id)
                                           ->where('EventID', $event->EventID)
                                           ->orderBy('DateCreate', 'desc')
                                           ->first();
                    
                    if ($assessment) {
                        $result = [
                            'score' => $assessment->TotalScore,
                            'total' => $assessment->TotalQuestion,
                        ];
                        
                        // Store in session for consistency
                        session(["quiz_result_$eventCode" => $result]);
                    }
                }
            }
        }
    }
    
    // If still no result, redirect back to quiz
    if (!$result) {
        return redirect()->route('quiz.show', $eventCode)
               ->with('error', 'No quiz results found. Please retake the quiz.');
    }

    return view('participants.finalResult', [
        'eventCode' => $eventCode,
        'result' => $result
    ]);
}
}