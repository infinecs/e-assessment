<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventsController extends Controller
{
    public function index(Request $request)
    {
        // Build query with potential filters
        $query = DB::table('assessmentevent as e')
            ->leftJoin('assessmentcategory as c', 'e.CategoryID', '=', 'c.CategoryID')
            ->select('e.*', 'c.CategoryName')
            ->orderBy('e.EventID', 'desc');

        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('e.EventName', 'LIKE', "%{$search}%")
                  ->orWhere('e.EventCode', 'LIKE', "%{$search}%");
            });
        }

        // Apply category filter if provided
        if ($request->has('categories') && !empty($request->categories)) {
            $categories = explode(',', $request->categories);
            $query->whereIn('c.CategoryName', $categories);
        }

        // Apply topic filter if provided (this is more complex as it requires checking TopicID field)
        if ($request->has('topics') && !empty($request->topics)) {
            $topics = explode(',', $request->topics);
            // We need to get topic IDs first
            $topicIds = DB::table('assessmenttopic')
                ->whereIn('TopicName', $topics)
                ->pluck('TopicID')
                ->toArray();
            
            if (!empty($topicIds)) {
                $query->where(function ($q) use ($topicIds) {
                    foreach ($topicIds as $topicId) {
                        $q->orWhere('e.TopicID', 'LIKE', "%{$topicId}%");
                    }
                });
            }
        }

        // Paginate the filtered results and preserve query parameters
        $records = $query->paginate(10);
        $records->appends($request->all());
            
        // Get all categories for the edit modal dropdown
        $allCategories = DB::table('assessmentcategory')
            ->select('CategoryID', 'CategoryName')
            ->orderBy('CategoryName')
            ->get();
            
        return view('assessment.events', compact('records', 'allCategories'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'EventName' => 'required|string|max:255',
                'EventCode' => 'required|string|max:50|unique:assessmentevent,EventCode',
                'QuestionLimit' => 'required|integer|min:1',
                'DurationEachQuestion' => 'required|integer|min:1',
                'StartDate' => 'required|date',
                'EndDate' => 'required|date|after:StartDate',
                'CategoryID' => 'required|integer',
                'selected_topic_ids' => 'required|array|min:1' // At least one topic required
            ]);
            
            // Convert selected topics to comma-separated string
            if (isset($validatedData['selected_topic_ids']) && !empty($validatedData['selected_topic_ids'])) {
                $validatedData['TopicID'] = implode(',', $validatedData['selected_topic_ids']);
            }
            
            // Remove selected_topic_ids from validated data as it's not a database field
            unset($validatedData['selected_topic_ids']);
            
            // Add creation timestamp and default AdminID
            $validatedData['DateCreate'] = now();
            $validatedData['DateUpdate'] = now();
            $validatedData['AdminID'] = 0; // Set default AdminID to 0
            
            $event = AssessmentEvent::create($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Event created successfully!',
                'data' => $event
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating event: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {
            $eventIds = $request->input('event_ids');
            
            if (empty($eventIds) || !is_array($eventIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No events selected for deletion'
                ], 400);
            }

            $deletedCount = AssessmentEvent::whereIn('EventID', $eventIds)->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} event(s)"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting events: ' . $e->getMessage()
            ], 500);
        }
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
            $topicNames = [];
            if ($event->TopicID) {
                $selectedTopicIds = array_map('trim', explode(',', $event->TopicID));
                $selectedTopicIds = array_map('strval', array_unique(array_filter($selectedTopicIds)));
                
                // Get topic names for search functionality
                if (!empty($selectedTopicIds)) {
                    $topics = DB::table('assessmenttopic')
                        ->whereIn('TopicID', $selectedTopicIds)
                        ->select('TopicName')
                        ->get();
                    $topicNames = $topics->pluck('TopicName')->toArray();
                }
            }
            
            return response()->json([
                'success' => true,
                'event' => $event,
                'category_topics' => $categoryTopics,
                'selected_topic_ids' => $selectedTopicIds,
                'topic_names' => $topicNames
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

    public function exportExcel(Request $request)
    {
        // Get filter parameters
        $search = $request->input('search');
        $categories = $request->input('categories') ? explode(',', $request->input('categories')) : [];
        $topics = $request->input('topics') ? explode(',', $request->input('topics')) : [];

        // Build query with filters
        $query = DB::table('assessmentevent as e')
            ->leftJoin('assessmentcategory as c', 'e.CategoryID', '=', 'c.CategoryID')
            ->select('e.*', 'c.CategoryName')
            ->orderBy('e.EventID', 'desc');

        // Apply search filter (event name or code)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('e.EventName', 'LIKE', "%{$search}%")
                  ->orWhere('e.EventCode', 'LIKE', "%{$search}%");
            });
        }

        // Apply category filter
        if (!empty($categories)) {
            $query->whereIn('c.CategoryName', $categories);
        }

        // Get the filtered data
        $records = $query->get();

        // Apply topic filter if needed
        if (!empty($topics)) {
            $records = $records->filter(function ($record) use ($topics) {
                if (!$record->TopicID) return false;
                
                // Get topic names for this event
                $eventTopicIds = array_map('trim', explode(',', $record->TopicID));
                $eventTopicIds = array_unique(array_filter($eventTopicIds));
                
                if (empty($eventTopicIds)) return false;
                
                $eventTopicNames = DB::table('assessmenttopic')
                    ->whereIn('TopicID', $eventTopicIds)
                    ->pluck('TopicName')
                    ->toArray();
                
                // Check if any of the event's topics match the filter
                return !empty(array_intersect($eventTopicNames, $topics));
            });
        }

        // Create CSV content
        $csvData = [];
        
        // CSV Headers
        $csvData[] = [
            'No',
            'Event Name',
            'Event Code',
            'Category',
            'Topics',
            'Question Limit',
            'Duration Each Question (seconds)',
            'Start Date',
            'End Date'
        ];

        // CSV Data rows
        $rowNumber = 1;
        foreach ($records as $record) {
            // Get topic names for this event
            $topicNames = [];
            if ($record->TopicID) {
                $topicIds = array_map('trim', explode(',', $record->TopicID));
                $topicIds = array_unique(array_filter($topicIds));
                
                if (!empty($topicIds)) {
                    $topics = DB::table('assessmenttopic')
                        ->whereIn('TopicID', $topicIds)
                        ->pluck('TopicName')
                        ->toArray();
                    $topicNames = $topics;
                }
            }
            $topicNamesStr = !empty($topicNames) ? implode("\n\n", $topicNames) : 'N/A';
            
            $csvData[] = [
                $rowNumber,
                $record->EventName ?? 'N/A',
                $record->EventCode ?? 'N/A',
                $record->CategoryName ?? 'N/A',
                $topicNamesStr,
                $record->QuestionLimit ?? 'N/A',
                $record->DurationEachQuestion ?? 'N/A',
                $record->StartDate ? \Carbon\Carbon::parse($record->StartDate)->format('Y-m-d') : 'N/A',
                $record->EndDate ? \Carbon\Carbon::parse($record->EndDate)->format('Y-m-d') : 'N/A'
            ];
            
            $rowNumber++;
        }

        // Generate filename with timestamp
        $filename = 'assessment-events-' . date('Y-m-d-H-i-s') . '.csv';

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
