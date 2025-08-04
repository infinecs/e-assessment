<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\AssessmentCategory;
use Illuminate\Http\Request;

class AssessmentCategoryController extends Controller
{
public function index()
{
   $categories = DB::table('assessmentcategory as c')
    ->select('c.CategoryID', 'c.CategoryName', 'c.DateCreate', 'c.DateUpdate', 'c.AdminID', 'c.TopicIDs')
    ->orderBy('c.CategoryID')
    ->get();

    $processedCategories = $categories->map(function ($cat) {
        $topicDetails = [];
        $uniqueTopicIds = [];
        
        // Get topics from category's TopicIDs field
        if ($cat->TopicIDs) {
            $uniqueTopicIds = array_map('trim', explode(',', $cat->TopicIDs));
            $uniqueTopicIds = array_unique(array_filter($uniqueTopicIds));
            
            if (!empty($uniqueTopicIds)) {
                $topicDetails = DB::table('assessmenttopic')
                    ->whereIn('TopicID', $uniqueTopicIds)
                    ->select('TopicID', 'TopicName')
                    ->get();
            }
        }

        $cat->topics_count = count($uniqueTopicIds);
        $cat->topic_details = $topicDetails;
        $cat->assigned_topic_ids = array_values($uniqueTopicIds);
        return $cat;
    });

    // Get all available topics for the edit modal
    $allTopics = DB::table('assessmenttopic')
        ->select('TopicID', 'TopicName')
        ->orderBy('TopicName')
        ->get();

    // Paginate the processed results
    $perPage = 10;
    $currentPage = request()->get('page', 1);
    $offset = ($currentPage - 1) * $perPage;
    $paginatedItems = $processedCategories->slice($offset, $perPage)->values();

    $records = new \Illuminate\Pagination\LengthAwarePaginator(
        $paginatedItems,
        $processedCategories->count(),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'pageName' => 'page']
    );



    return view('assessment.category', compact('records', 'allTopics'));
    
    // Example:

}

public function destroy($id)
{
    try {
        $category = DB::table('assessmentcategory')->where('CategoryID', $id)->first();
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
        
        DB::table('assessmentcategory')->where('CategoryID', $id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting category: ' . $e->getMessage()
        ], 500);
    }
}

public function update(Request $request, $id)
{
    try {
        $category = DB::table('assessmentcategory')->where('CategoryID', $id)->first();
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
        
        $validatedData = $request->validate([
            'CategoryName' => 'required|string|max:255',
            'topic_ids' => 'array'
        ]);
        
        // Prepare update data
        $categoryData = [
            'CategoryName' => $validatedData['CategoryName'], 
            'DateUpdate' => now()
        ];
        
        // Save topic IDs directly to category table (for time being)
        if (isset($validatedData['topic_ids']) && !empty($validatedData['topic_ids'])) {
            $categoryData['TopicIDs'] = implode(',', $validatedData['topic_ids']);
        } else {
            $categoryData['TopicIDs'] = null;
        }
        
        DB::table('assessmentcategory')
            ->where('CategoryID', $id)
            ->update($categoryData);
        
        return response()->json([
            'success' => true,
            'message' => 'Category and topics saved successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating category: ' . $e->getMessage()
        ], 500);
    }
}

public function getCategoryDetails($id)
{
    try {
        $category = DB::table('assessmentcategory')->where('CategoryID', $id)->first();
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
        
        // Get assigned topic IDs from category table
        $assignedTopicIds = [];
        if ($category->TopicIDs) {
            $assignedTopicIds = array_map('trim', explode(',', $category->TopicIDs));
            $assignedTopicIds = array_values(array_unique(array_filter($assignedTopicIds)));
        }
        
        // Ensure all topic IDs are strings for proper comparison in JavaScript
        $assignedTopicIds = array_map('strval', $assignedTopicIds);
        
        return response()->json([
            'success' => true,
            'category' => $category,
            'assigned_topic_ids' => $assignedTopicIds
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching category details: ' . $e->getMessage()
        ], 500);
    }
}

}