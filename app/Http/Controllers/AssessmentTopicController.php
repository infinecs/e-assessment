<?php

namespace App\Http\Controllers;
use App\Models\AssessmentTopic;
use Illuminate\Http\Request;

class AssessmentTopicController extends Controller
{
    public function index()
    {
        // Use the MODEL, not the controller
        $records = AssessmentTopic::paginate(10); // 10 per page
        return view('assessment.topic', compact('records'));
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

    public function update(Request $request, $id)
    {
        try {
            $topic = AssessmentTopic::findOrFail($id);
            
            $validatedData = $request->validate([
                'TopicName' => 'required|string|max:255'
            ]);
            
            $topic->update($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Topic updated successfully!',
                'data' => $topic
            ]);
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
