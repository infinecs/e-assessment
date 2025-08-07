<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\AssessmentCategory;
use Illuminate\Http\Request;

class AssessmentCategoryController extends Controller
{
public function index(Request $request)
{
   $query = DB::table('assessmentcategory as c')
    ->select('c.CategoryID', 'c.CategoryName', 'c.DateCreate', 'c.DateUpdate', 'c.AdminID', 'c.TopicIDs')
    ->orderBy('c.DateCreate', 'desc');

    // Apply server-side search filter
    $search = $request->input('search');
    if ($search) {
        $query->where('c.CategoryName', 'LIKE', "%{$search}%");
    }

    $categories = $query->get();

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

    // Append query parameters to pagination links
    $records->appends($request->query());

    // Handle AJAX requests
    if ($request->ajax()) {
        $html = '';
        foreach ($records as $row) {
            $html .= '<tr data-category-id="' . $row->CategoryID . '"
                         data-category-name="' . htmlspecialchars($row->CategoryName) . '"
                         class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:hover:bg-zinc-700/50 dark:border-zinc-600">';
            
            $html .= '<td class="w-4 p-3">
                        <div class="flex items-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                        </div>
                      </td>';
            
            $html .= '<td class="px-2 py-1.5">
                        <button type="button" class="text-violet-600 hover:text-violet-800 underline cursor-pointer"
                                onclick="showTopicsModal(\'' . $row->CategoryID . '\', \'' . addslashes($row->CategoryName) . '\', ' . htmlspecialchars(json_encode($row->topic_details)) . ')">
                            ' . htmlspecialchars($row->CategoryName) . '
                        </button>
                      </td>';
            
            $html .= '<td class="px-2 py-1.5">' . ($row->topics_count ?? 0) . '</td>';
            
            // Actions
            $html .= '<td class="px-2 py-1.5 text-center">
                        <div class="relative inline-block dropdown">
                            <button type="button" class="dropdown-toggle flex items-center justify-center w-7 h-7 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 focus:ring focus:ring-gray-200 dark:bg-zinc-600 dark:text-gray-100 dark:hover:bg-zinc-500">
                                <i class="bx bx-dots-vertical text-base"></i>
                            </button>
                            <div class="dropdown-menu hidden absolute right-0 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 dark:bg-zinc-700 z-20">
                                <div class="p-1 flex flex-col gap-1">
                                    <button type="button" onclick="editCategory(' . $row->CategoryID . ')" class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                        <i class="mdi mdi-pencil text-base"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button type="button" onclick="deleteCategory(' . $row->CategoryID . ')" class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                        <i class="mdi mdi-trash-can text-base"></i>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                      </td>';
            
            $html .= '<td class="px-2 py-1.5">' . \Carbon\Carbon::parse($row->DateCreate)->format('d M Y') . '</td>';
            $html .= '<td class="px-2 py-1.5">' . \Carbon\Carbon::parse($row->DateUpdate)->format('d M Y') . '</td>';
            
            $html .= '</tr>';
        }

        // If no records found
        if ($records->isEmpty()) {
            $html = '<tr><td colspan="6" class="px-2 py-1.5 text-center">No categories found</td></tr>';
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
                'from' => $records->firstItem(),
                'to' => $records->lastItem(),
                'links' => $records->render('pagination::tailwind')
            ]
        ]);
    }

    return view('assessment.category', compact('records', 'allTopics'));
    
    // Example:

}

public function store(Request $request)
{
    try {
        $validatedData = $request->validate([
            'CategoryName' => 'required|string|max:255',
            'topic_ids' => 'required|array|min:1'
        ]);
        
        // Check if category name already exists
        $existingCategory = DB::table('assessmentcategory')
            ->where('CategoryName', $validatedData['CategoryName'])
            ->first();
        
        if ($existingCategory) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: The category name has already been taken.'
            ], 422);
        }
        
        // Prepare category data
        $categoryData = [
            'CategoryName' => $validatedData['CategoryName'],
            'AdminID' => 0, // Default AdminID
            'DateCreate' => now(),
            'DateUpdate' => now()
        ];
        
        // Save topic IDs directly to category table (required now)
        $categoryData['TopicIDs'] = implode(',', $validatedData['topic_ids']);
        
        $categoryId = DB::table('assessmentcategory')->insertGetId($categoryData);
        
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully!',
            'data' => ['CategoryID' => $categoryId]
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error creating category: ' . $e->getMessage()
        ], 500);
    }
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
            'topic_ids' => 'required|array|min:1'
        ]);
        
        // Prepare update data
        $categoryData = [
            'CategoryName' => $validatedData['CategoryName'], 
            'DateUpdate' => now()
        ];
        
        // Save topic IDs directly to category table (required now)
        $categoryData['TopicIDs'] = implode(',', $validatedData['topic_ids']);
        
        DB::table('assessmentcategory')
            ->where('CategoryID', $id)
            ->update($categoryData);
        
        return response()->json([
            'success' => true,
            'message' => 'Category and topics saved successfully!'
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
        ], 422);
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

public function bulkDestroy(Request $request)
{
    try {
        $validatedData = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:assessmentcategory,CategoryID'
        ]);

        $ids = $validatedData['ids'];
        $deletedCount = DB::table('assessmentcategory')->whereIn('CategoryID', $ids)->delete();

        $categoryText = $deletedCount === 1 ? 'category' : 'categories';
        
        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} {$categoryText}!"
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting categories: ' . $e->getMessage()
        ], 500);
    }
}

}