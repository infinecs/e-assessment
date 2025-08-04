<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventsController extends Controller
{
    public function index()
    {
        // Use Eloquent and paginate, also get category names and get all categories for edit modal
        $records = DB::table('assessmentevent as e')
            ->leftJoin('assessmentcategory as c', 'e.CategoryID', '=', 'c.CategoryID')
            ->select('e.*', 'c.CategoryName')
            ->paginate(10);
            
        // Get all categories for the edit modal dropdown
        $allCategories = DB::table('assessmentcategory')
            ->select('CategoryID', 'CategoryName')
            ->orderBy('CategoryName')
            ->get();
            
        return view('assessment.events', compact('records', 'allCategories'));
    }

    public function destroy($id)
    {
        try {
            $event = AssessmentEvent::findOrFail($id);
            $event->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting event: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $event = AssessmentEvent::findOrFail($id);
            
            $validatedData = $request->validate([
                'EventName' => 'required|string|max:255',
                'EventCode' => 'required|string|max:50',
                'QuestionLimit' => 'required|integer|min:1',
                'DurationEachQuestion' => 'required|integer|min:1',
                'StartDate' => 'required|date',
                'EndDate' => 'required|date|after:StartDate',
                'CategoryID' => 'required|integer',
                'selected_topic_ids' => 'array' // For selected topics from category
            ]);
            
            // If specific topics are selected, use those; otherwise keep existing
            if (isset($validatedData['selected_topic_ids']) && !empty($validatedData['selected_topic_ids'])) {
                $validatedData['TopicID'] = implode(',', $validatedData['selected_topic_ids']);
            }
            
            // Remove selected_topic_ids from validated data as it's not a database field
            unset($validatedData['selected_topic_ids']);
            
            $event->update($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully!',
                'data' => $event
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating event: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEventDetails($id)
    {
        try {
            $event = AssessmentEvent::findOrFail($id);
            
            // Get category topics if category is selected
            $categoryTopics = [];
            if ($event->CategoryID) {
                $category = DB::table('assessmentcategory')->where('CategoryID', $event->CategoryID)->first();
                if ($category && $category->TopicIDs) {
                    $topicIds = array_map('trim', explode(',', $category->TopicIDs));
                    $topicIds = array_unique(array_filter($topicIds));
                    
                    if (!empty($topicIds)) {
                        $categoryTopics = DB::table('assessmenttopic')
                            ->whereIn('TopicID', $topicIds)
                            ->select('TopicID', 'TopicName')
                            ->get();
                    }
                }
            }
            
            // Get currently selected topics for this event
            $selectedTopicIds = [];
            if ($event->TopicID) {
                $selectedTopicIds = array_map('trim', explode(',', $event->TopicID));
                $selectedTopicIds = array_map('strval', array_unique(array_filter($selectedTopicIds)));
            }
            
            return response()->json([
                'success' => true,
                'event' => $event,
                'category_topics' => $categoryTopics,
                'selected_topic_ids' => $selectedTopicIds
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching event details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCategoryTopics($categoryId)
    {
        try {
            $category = DB::table('assessmentcategory')->where('CategoryID', $categoryId)->first();
            
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }
            
            $topics = [];
            if ($category->TopicIDs) {
                $topicIds = array_map('trim', explode(',', $category->TopicIDs));
                $topicIds = array_unique(array_filter($topicIds));
                
                if (!empty($topicIds)) {
                    $topics = DB::table('assessmenttopic')
                        ->whereIn('TopicID', $topicIds)
                        ->select('TopicID', 'TopicName')
                        ->get();
                }
            }
            
            return response()->json([
                'success' => true,
                'topics' => $topics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching category topics: ' . $e->getMessage()
            ], 500);
        }
    }
}
