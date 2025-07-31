<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentEvent;
use App\Models\AssessmentTopic;
use App\Models\AssessmentResultSet;
use App\Models\AssessmentAnswer;
use App\Models\Assessment;

class QuizController extends Controller
{    

public function showQuiz($eventCode)
{
    if (session()->has("quiz_questions_$eventCode")) {
        // Re-fetch models by IDs
        $questionIds = session("quiz_questions_$eventCode");
        $questions = AssessmentQuestion::whereIn('QuestionID', $questionIds)
            ->orderByRaw("FIELD(QuestionID, ".implode(',', $questionIds).")")
            ->get();
    } else {
        $event = AssessmentEvent::where('EventCode', $eventCode)->firstOrFail();
        $topicIds = array_map('trim', explode(',', $event->TopicID));
        $questionLimit = (int)$event->QuestionLimit;

        $allQuestions = collect();
        $perTopic = max(1, floor($questionLimit / count($topicIds)));

        foreach ($topicIds as $topicId) {
            $topic = AssessmentTopic::find($topicId);
            if (!$topic || !$topic->QuestionID) continue;

            $ids = array_map('trim', explode(',', $topic->QuestionID));
            shuffle($ids);
            $selected = array_slice($ids, 0, $perTopic);

            $qs = AssessmentQuestion::whereIn('QuestionID', $selected)->get();
            $allQuestions = $allQuestions->merge($qs);
        }

        $needed = $questionLimit - $allQuestions->count();
        if ($needed > 0) {
            $allIds = AssessmentTopic::whereIn('TopicID', $topicIds)
                ->pluck('QuestionID')
                ->flatMap(fn($ids) => array_map('trim', explode(',', $ids)))
                ->unique()
                ->toArray();

            $alreadyPicked = $allQuestions->pluck('QuestionID')->toArray();
            $remainingIds = array_values(array_diff($allIds, $alreadyPicked));
            shuffle($remainingIds);

            $extraIds = array_slice($remainingIds, 0, $needed);
            $extraQuestions = AssessmentQuestion::whereIn('QuestionID', $extraIds)->get();

            $allQuestions = $allQuestions->merge($extraQuestions);
        }

        $allQuestions = $allQuestions->shuffle()->values();

        // Save IDs in session
        session(["quiz_questions_$eventCode" => $allQuestions->pluck('QuestionID')->toArray()]);

        $questions = $allQuestions;
    }

    $savedAnswers = session("quiz_answers_$eventCode", []);

    return view('participants.quizPage', [
        'eventCode' => $eventCode,
        'questions' => $questions,
        'savedAnswers' => $savedAnswers
    ]);
}


public function submitQuiz(Request $request, $eventCode)
{
    $answers = $request->input('answers', []);
    $participantId = auth()->check() ? auth()->id() : 0;

    $questionIds = session("quiz_questions_$eventCode", []);
    if (empty($questionIds)) {
        return redirect()->route('quiz.show', $eventCode)
            ->with('error', 'Session expired, please retake the quiz.');
    }

    $score = 0;
    $total = count($questionIds);

    foreach ($questionIds as $qid) {
        if (!isset($answers[$qid])) continue;

        $selectedOption = $answers[$qid];
        $answerList = AssessmentAnswer::where('QuestionID', $qid)->get();

        $chosenAnswer = null;
        foreach ($answerList as $index => $ans) {
            if (chr(65+$index) === $selectedOption) {
                $chosenAnswer = $ans;
                break;
            }
        }

        if ($chosenAnswer && $chosenAnswer->ExpectedAnswer === 'Y') {
            $score++;
        }
    }

    \DB::transaction(function () use ($participantId, $score, $total, $answers, $questionIds) {
        $assessment = Assessment::create([
            'ParticipantID' => $participantId,
            'TotalScore'    => $score,
            'TotalQuestion' => $total,
            'AdminID'       => 0,
            'DateCreate'    => now(),
            'DateUpdate'    => now(),
        ]);

        foreach ($questionIds as $qid) {
            $answerLetter = $answers[$qid] ?? null;
            $answerId = null;
            if ($answerLetter) {
                $answerList = AssessmentAnswer::where('QuestionID', $qid)->get();
                foreach ($answerList as $index => $ans) {
                    if (chr(65+$index) === $answerLetter) {
                        $answerId = $ans->AnswerID;
                        break;
                    }
                }
            }

            AssessmentResultSet::create([
                'AssessmentID' => $assessment->AssessmentID,
                'QuestionID'   => $qid,
                'AnswerID'     => $answerId,
                'DateCreate'   => now(),
            ]);
        }
    });

    session([
    "quiz_result_$eventCode" => [
        'score' => $score,
        'total' => $total,
    ],
    "quiz_completed_$eventCode" => true
]);

// Remove old questions and answers so that next attempt is fresh
session()->forget("quiz_questions_$eventCode");
session()->forget("quiz_answers_$eventCode");

return redirect()->route('quiz.results', $eventCode);
}


public function saveAnswer(Request $request, $eventCode)
{
    $answers = session("quiz_answers_$eventCode", []);
    $answers[$request->questionId] = $request->value;
    session(["quiz_answers_$eventCode" => $answers]);

    return response()->json(['status' => 'saved']);
}

public function showResults($eventCode)
{
    if (!session("quiz_completed_$eventCode")) {
        return redirect()->route('quiz.show', $eventCode);
    }

    $result = session("quiz_result_$eventCode");
    return view('participants.finalResult', [
        'eventCode' => $eventCode,
        'result' => $result
    ]);
}
}





