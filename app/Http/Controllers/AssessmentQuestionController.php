<?php

namespace App\Http\Controllers;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentQuestionController extends Controller
{
    public function index()
    {
        // Use the MODEL, not the controller, also get all topics for edit modal
        $records = AssessmentQuestion::paginate(10); // 10 per page
        
        // Get all topics for the edit modal dropdown
        $allTopics = DB::table('assessmenttopic')
            ->select('TopicID', 'TopicName')
            ->orderBy('TopicName')
            ->get();
            
        return view('assessment.question', compact('records', 'allTopics'));
    }

    public function destroy($id)
    {
        try {
            $question = AssessmentQuestion::findOrFail($id);
            
            // Also delete associated answers
            AssessmentAnswer::where('QuestionID', $id)->delete();
            
            $question->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Question and its answers deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting question: ' . $e->getMessage()
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
            
            // If specific topics are selected, use those as comma-separated string
            if (isset($validatedData['selected_topic_ids']) && !empty($validatedData['selected_topic_ids'])) {
                $validatedData['TopicID'] = implode(',', $validatedData['selected_topic_ids']);
                // Use the first selected topic as the default topic
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
            
            // Get selected topic IDs for this question
            $selectedTopicIds = [];
            if ($question->TopicID) {
                $selectedTopicIds = array_map('trim', explode(',', $question->TopicID));
                $selectedTopicIds = array_map('strval', array_unique(array_filter($selectedTopicIds)));
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
}
