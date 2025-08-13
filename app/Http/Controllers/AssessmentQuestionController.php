<?php

namespace App\Http\Controllers;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentQuestionController extends Controller
{
    public function index(Request $request)
    {
        // Build query with filters
        $query = AssessmentQuestion::orderBy('DateCreate', 'desc');

        // Apply server-side search filter
        $search = $request->input('search');
        if ($search) {
            $query->where('QuestionText', 'LIKE', "%{$search}%");
        }

        // Apply topic filter
        $topics = $request->input('topics');
        if ($topics) {
            $topicIds = explode(',', $topics);
            $query->whereIn('DefaultTopic', $topicIds);
        }

        // Get paginated results
        $records = $query->paginate(10);
        
        // Append query parameters to pagination links (exclude ajax parameter)
        $records->appends($request->except('ajax'));
        
        // Handle AJAX requests
        if ($request->ajax() || $request->has('ajax')) {
            $html = '';
            if ($records->total() > 0) {
                foreach ($records as $row) {
                    $html .= '<tr class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:hover:bg-zinc-700/50 dark:border-zinc-600"
                                data-question-id="' . $row->QuestionID . '"
                                data-default-topic="' . htmlspecialchars($row->DefaultTopic ?? '') . '">
                                <td class="w-4 p-3">
                                    <div class="flex items-center">
                                        <input type="checkbox" data-question-id="' . $row->QuestionID . '"
                                            class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                                    </div>
                                </td>
                                <td class="px-2 py-1.5 text-left">
                                    <button type="button" 
                                        class="text-blue-600 hover:text-blue-800 hover:underline text-left question-btn"
                                        data-question-id="' . $row->QuestionID . '"
                                        data-question-text="' . htmlspecialchars($row->QuestionText) . '">
                                        ' . htmlspecialchars($row->QuestionText) . '
                                    </button>
                                </td>
                                <td class="px-2 py-1.5">' . htmlspecialchars($row->DefaultTopic) . '</td>
                                <td class="px-2 py-1.5 text-center">
                                    <div class="relative inline-block dropdown">
                                        <button type="button"
                                            class="dropdown-toggle flex items-center justify-center w-7 h-7 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 focus:ring focus:ring-gray-200 dark:bg-zinc-600 dark:text-gray-100 dark:hover:bg-zinc-500">
                                            <i class="bx bx-dots-vertical text-base"></i>
                                        </button>
                                        <div
                                            class="dropdown-menu hidden absolute right-0 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 dark:bg-zinc-700 z-20">
                                            <div class="p-1 flex flex-col gap-1">
                                                <button type="button"
                                                    class="edit-question-btn w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700"
                                                    data-question-id="' . $row->QuestionID . '">
                                                    <i class="mdi mdi-pencil text-base"></i>
                                                    <span>Edit</span>
                                                </button>
                                                <button type="button"
                                                    class="delete-question-btn w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700"
                                                    data-question-id="' . $row->QuestionID . '">
                                                    <i class="mdi mdi-trash-can text-base"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-1.5">' . \Carbon\Carbon::parse($row->DateCreate)->format('d M Y') . '</td>
                                <td class="px-2 py-1.5">' . \Carbon\Carbon::parse($row->DateUpdate)->format('d M Y') . '</td>
                            </tr>';
                }
            } else {
                $html = '<tr><td colspan="7" class="px-2 py-1.5 text-center">No questions found</td></tr>';
            }
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'total' => $records->total(),
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage()
            ]);
        }
        
        // Get all topics for the edit modal dropdown and filters
        $allTopics = DB::table('assessmenttopic')
            ->select('TopicID', 'TopicName')
            ->orderBy('TopicName')
            ->get();
            
        return view('assessment.question', compact('records', 'allTopics'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'QuestionText' => 'required|string',
                'selected_topic_ids' => 'required|array|min:1',
                'answers' => 'required|array|min:2',
                'answers.*.type' => 'required|in:text,image',
                'answers.*.is_correct' => 'required',
                'answers.*.text' => 'required_without:answers.*.answer_image',
                'answers.*.answer_image' => 'required_if:answers.*.type,image|nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240' // 10MB
            ]);

            $question = new AssessmentQuestion();
            $question->QuestionText = $validatedData['QuestionText'];

            // Handle image upload
            if ($request->hasFile('question_image')) {
                $image = $request->file('question_image');
                if (!$image->isValid()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: Question image upload failed. Please check file size and type.'
                    ], 400);
                }
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/QuestionImage'), $imageName);
                $question->QuestionImage = 'images/QuestionImage/' . $imageName;
            }

            if (isset($validatedData['selected_topic_ids']) && !empty($validatedData['selected_topic_ids'])) {
                $question->DefaultTopic = $validatedData['selected_topic_ids'][0];
            }

            $question->AdminID = 0;
            $question->DateCreate = now();
            $question->DateUpdate = now();
            $question->save();

            foreach ($validatedData['answers'] as $idx => $answerData) {
                $answer = new AssessmentAnswer();
                $answer->QuestionID = $question->QuestionID;
                $answer->AnswerText = $answerData['text'];
                $answer->AnswerType = isset($answerData['type']) && $answerData['type'] === 'image' ? 'I' : 'T';
                // Strictly check for correct answer
                $isCorrect = ($answerData['is_correct'] === true || $answerData['is_correct'] === 'true' || $answerData['is_correct'] === 1 || $answerData['is_correct'] === '1');
                $answer->ExpectedAnswer = $isCorrect ? 'Y' : 'N';
                // Handle answer image upload
                if ($answer->AnswerType === 'I') {
                    if ($request->hasFile('answers.' . $idx . '.answer_image')) {
                        $image = $request->file('answers.' . $idx . '.answer_image');
                        if (!$image->isValid()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Error: Answer image upload failed for answer #' . ($idx+1) . '. Please check file size and type.'
                            ], 400);
                        }
                        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('images/AnswerImage'), $imageName);
                        $answer->AnswerImage = 'images/AnswerImage/' . $imageName;
                    }
                }
                $answer->DateCreate = now();
                $answer->DateUpdate = now();
                $answer->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Question created successfully!',
                'data' => $question
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $question = AssessmentQuestion::findOrFail($id);

            // Delete question image file if exists
            if ($question->QuestionImage && file_exists(public_path($question->QuestionImage))) {
                @unlink(public_path($question->QuestionImage));
            }

            // Get all answers for this question
            $answers = AssessmentAnswer::where('QuestionID', $id)->get();
            foreach ($answers as $answer) {
                if ($answer->AnswerImage && file_exists(public_path($answer->AnswerImage))) {
                    @unlink(public_path($answer->AnswerImage));
                }
            }

            // Delete associated answers
            AssessmentAnswer::where('QuestionID', $id)->delete();

            $question->delete();

            return response()->json([
                'success' => true,
                'message' => 'Question and its answers (and images) deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {
            $questionIds = $request->input('question_ids');

            if (empty($questionIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No questions selected for deletion.'
                ], 400);
            }

            // Delete question and answer images for each question
            $questions = AssessmentQuestion::whereIn('QuestionID', $questionIds)->get();
            foreach ($questions as $question) {
                if ($question->QuestionImage && file_exists(public_path($question->QuestionImage))) {
                    @unlink(public_path($question->QuestionImage));
                }
                $answers = AssessmentAnswer::where('QuestionID', $question->QuestionID)->get();
                foreach ($answers as $answer) {
                    if ($answer->AnswerImage && file_exists(public_path($answer->AnswerImage))) {
                        @unlink(public_path($answer->AnswerImage));
                    }
                }
            }

            // Delete associated answers first
            AssessmentAnswer::whereIn('QuestionID', $questionIds)->delete();

            // Delete questions
            $deletedCount = AssessmentQuestion::whereIn('QuestionID', $questionIds)->delete();

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} questions, their answers, and images."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting questions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $question = AssessmentQuestion::findOrFail($id);
            
            $validatedData = $request->validate([
                'QuestionText' => 'required|string',
                'selected_topic_ids' => 'array' // For selected topics
            ]);
            
            // If specific topics are selected, use the first one as DefaultTopic (only column that exists)
            if (isset($validatedData['selected_topic_ids']) && !empty($validatedData['selected_topic_ids'])) {
                // Use the first selected topic as the default topic (only column that exists in DB)
                $validatedData['DefaultTopic'] = $validatedData['selected_topic_ids'][0];
            }
            
            // Remove selected_topic_ids from validated data as it's not a database field
            unset($validatedData['selected_topic_ids']);
            
            $question->update($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully!',
                'data' => $question
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getQuestionDetails($id)
    {
        try {
            $question = AssessmentQuestion::findOrFail($id);
            
            // Get selected topic IDs for this question (use DefaultTopic since TopicID column doesn't exist)
            $selectedTopicIds = [];
            if ($question->DefaultTopic) {
                // Since only one topic is stored in DefaultTopic, just return it as an array
                $selectedTopicIds = [strval($question->DefaultTopic)];
            }
            
            return response()->json([
                'success' => true,
                'question' => $question,
                'selected_topic_ids' => $selectedTopicIds
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching question details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAnswers($questionId)
    {
        try {
            $answers = AssessmentAnswer::where('QuestionID', $questionId)
                ->orderBy('AnswerID')
                ->get();

            // Update AnswerImage to be a full URL if present
            $answers = $answers->map(function ($answer) {
                if ($answer->AnswerImage) {
                    // If already a full URL, leave as is
                    if (!preg_match('/^https?:\/\//', $answer->AnswerImage)) {
                        $answer->AnswerImage = url($answer->AnswerImage);
                    }
                }
                return $answer;
            });

            return response()->json([
                'success' => true,
                'answers' => $answers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading answers: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateAnswers(Request $request, $questionId)
    {
        try {
            $answers = $request->input('answers');
            
            // First, set all answers for this question to 'N'
            AssessmentAnswer::where('QuestionID', $questionId)
                ->update(['ExpectedAnswer' => 'N']);
            
            foreach ($answers as $answerData) {
                AssessmentAnswer::where('AnswerID', $answerData['id'])
                    ->where('QuestionID', $questionId)
                    ->update([
                        'AnswerText' => $answerData['text'],
                        'ExpectedAnswer' => $answerData['is_correct'] ? 'Y' : 'N'
                    ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Answers updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating answers: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        // Get filter parameters
        $search = $request->input('search');
        $topics = $request->input('topics') ? explode(',', $request->input('topics')) : [];

        // Build query with filters
        $query = AssessmentQuestion::with(['answers', 'topic'])
            ->orderBy('DateCreate', 'desc');

        // Apply search filter (question text)
        if ($search) {
            $query->where('QuestionText', 'LIKE', "%{$search}%");
        }

        // Apply topic filter
        if (!empty($topics)) {
            $query->whereIn('DefaultTopic', $topics);
        }

        // Get the filtered data
        $records = $query->get();

        // Create CSV content
        $csvData = [];
        
        // CSV Headers
        $csvData[] = [
            'No',
            'Default Topic',
            'Question',
            'Correct Answer',
            'Wrong Answers',
            'Date Created',
            'Date Updated'
        ];

        // CSV Data rows
        $counter = 1;
        foreach ($records as $record) {
            // Get topic name
            $topicName = 'N/A';
            if ($record->DefaultTopic) {
                $topic = DB::table('assessmenttopic')
                    ->where('TopicID', $record->DefaultTopic)
                    ->first();
                $topicName = $topic ? $topic->TopicName : 'Topic ID: ' . $record->DefaultTopic;
            }

            // Get correct and wrong answers
            $correctAnswers = [];
            $wrongAnswers = [];
            
            foreach ($record->answers as $answer) {
                if ($answer->ExpectedAnswer === 'Y') {
                    $correctAnswers[] = $answer->AnswerText;
                } else {
                    $wrongAnswers[] = $answer->AnswerText;
                }
            }
            
            // Format wrong answers with the same separator as correct answers for consistency
            $wrongAnswersText = implode(' | ', $wrongAnswers);
            
            $csvData[] = [
                $counter++,
                $topicName,
                $record->QuestionText,
                implode(' | ', $correctAnswers),
                $wrongAnswersText,
                \Carbon\Carbon::parse($record->DateCreate)->format('Y-m-d H:i:s'),
                \Carbon\Carbon::parse($record->DateUpdate)->format('Y-m-d H:i:s')
            ];
        }

        // Generate filename with timestamp
        $filename = 'assessment-questions-' . date('Y-m-d-H-i-s') . '.csv';

        // Create response
        $response = response()->streamDownload(function () use ($csvData) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for proper UTF-8 encoding in Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);

        return $response;
    }
}
