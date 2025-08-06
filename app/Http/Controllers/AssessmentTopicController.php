<?php

namespace App\Http\Controllers;
use App\Models\AssessmentTopic;
use Illuminate\Http\Request;

class AssessmentTopicController extends Controller
{
    public function index(Request $request)
    {
        // Build query with filters
        $query = AssessmentTopic::orderBy('DateCreate', 'desc');

        // Apply server-side search filter
        $search = $request->input('search');
        if ($search) {
            $query->where('TopicName', 'LIKE', "%{$search}%");
        }

        // Apply category filter
        $categories = $request->input('categories');
        if ($categories) {
            $categoryIds = explode(',', $categories);
            
            // Get all categories to find which ones contain topics
            $allCategories = \Illuminate\Support\Facades\DB::table('assessmentcategory')
                ->whereIn('CategoryID', $categoryIds)
                ->select('CategoryID', 'TopicIDs')
                ->get();
                
            // Build array of topic IDs that belong to selected categories
            $allowedTopicIds = [];
            foreach ($allCategories as $category) {
                if ($category->TopicIDs) {
                    $topicIds = array_map('trim', explode(',', $category->TopicIDs));
                    $allowedTopicIds = array_merge($allowedTopicIds, $topicIds);
                }
            }
            
            if (!empty($allowedTopicIds)) {
                $query->whereIn('TopicID', array_unique($allowedTopicIds));
            } else {
                // No topics found in selected categories, return empty result
                $query->where('TopicID', -1);
            }
        }

        // Get paginated results
        $records = $query->paginate(10);
        
        // Append query parameters to pagination links
        $records->appends($request->query());
        
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

    public function exportExcel(Request $request)
    {
        // Get filter parameters
        $search = $request->input('search');
        $categories = $request->input('categories') ? explode(',', $request->input('categories')) : [];

        // Get all categories for lookup
        $allCategories = \Illuminate\Support\Facades\DB::table('assessmentcategory')
            ->select('CategoryID', 'CategoryName', 'TopicIDs')
            ->get()
            ->keyBy('CategoryID');

        // Build query with filters
        $query = AssessmentTopic::orderBy('DateCreate', 'desc');

        // Apply search filter
        if ($search) {
            $query->where('TopicName', 'LIKE', "%{$search}%");
        }

        // Get all topics
        $records = $query->get();

        // Filter by categories if specified
        if (!empty($categories)) {
            $records = $records->filter(function ($topic) use ($allCategories, $categories) {
                foreach ($allCategories as $category) {
                    if (!in_array($category->CategoryID, $categories)) continue;
                    
                    if ($category->TopicIDs) {
                        $topicIds = array_map('trim', explode(',', $category->TopicIDs));
                        if (in_array($topic->TopicID, $topicIds)) {
                            return true;
                        }
                    }
                }
                return false;
            });
        }

        // Create CSV content
        $csvData = [];
        
        // CSV Headers
        $csvData[] = [
            'No',
            'Topic Name',
            'Categories',
            'Date Created',
            'Date Updated'
        ];

        // CSV Data rows
        $rowNumber = 1;
        foreach ($records as $record) {
            // Find categories that contain this topic
            $topicCategories = [];
            foreach ($allCategories as $category) {
                if ($category->TopicIDs) {
                    $topicIds = array_map('trim', explode(',', $category->TopicIDs));
                    if (in_array($record->TopicID, $topicIds)) {
                        $topicCategories[] = $category->CategoryName;
                    }
                }
            }
            
            $categoriesStr = !empty($topicCategories) ? implode("\n\n", $topicCategories) : 'No categories';
            
            $csvData[] = [
                $rowNumber,
                $record->TopicName ?? 'N/A',
                $categoriesStr,
                $record->DateCreate ? \Carbon\Carbon::parse($record->DateCreate)->format('Y-m-d H:i:s') : 'N/A',
                $record->DateUpdate ? \Carbon\Carbon::parse($record->DateUpdate)->format('Y-m-d H:i:s') : 'N/A'
            ];
            
            $rowNumber++;
        }

        // Generate filename with timestamp
        $filename = 'assessment-topics-' . date('Y-m-d-H-i-s') . '.csv';

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
