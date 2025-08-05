<?php

namespace App\Http\Controllers;
use App\Models\AssessmentTopic;
use Illuminate\Http\Request;

class AssessmentTopicController extends Controller
{
    public function index()
    {
        // Use the MODEL, not the controller - order by most recent first
        $records = AssessmentTopic::orderBy('DateCreate', 'desc')->paginate(10); // 10 per page
        
        // Get all categories for the filter dropdown
        $allCategories = \Illuminate\Support\Facades\DB::table('assessmentcategory')
            ->select('CategoryID', 'CategoryName', 'TopicIDs')
            ->orderBy('CategoryName')
            ->get();
            
        return view('assessment.topic', compact('records', 'allCategories'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'TopicName' => 'required|string|max:255|unique:assessmenttopic,TopicName'
            ]);

            // Add default values for AdminID and dates
            $topicData = array_merge($validatedData, [
                'AdminID' => 0, // Default AdminID
                'DateCreate' => now(),
                'DateUpdate' => now()
            ]);

            $topic = AssessmentTopic::create($topicData);
            
            return response()->json([
                'success' => true,
                'message' => 'Topic created successfully!',
                'data' => $topic
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating topic: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $topic = AssessmentTopic::findOrFail($id);
            $topic->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Topic deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting topic: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {
            $topicIds = $request->input('topic_ids');
            
            if (empty($topicIds) || !is_array($topicIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No topics selected for deletion'
                ], 400);
            }

            $deletedCount = AssessmentTopic::whereIn('TopicID', $topicIds)->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} topic(s)"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting topics: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $topic = AssessmentTopic::findOrFail($id);
            
            $validatedData = $request->validate([
                'TopicName' => 'required|string|max:255|unique:assessmenttopic,TopicName,' . $id . ',TopicID'
            ]);
            
            $topic->update(array_merge($validatedData, [
                'DateUpdate' => now()
            ]));
            
            return response()->json([
                'success' => true,
                'message' => 'Topic updated successfully!',
                'data' => $topic
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating topic: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTopicDetails($id)
    {
        try {
            $topic = AssessmentTopic::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'topic' => $topic
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching topic details: ' . $e->getMessage()
            ], 500);
        }
    }
}
