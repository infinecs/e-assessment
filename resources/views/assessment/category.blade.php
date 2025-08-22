@extends('layout.appMain')

@section('content')
<!-- CSRF token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Custom styles for checkboxes -->
<style>
.topic-checkbox:checked,
.add-topic-checkbox:checked,
.edit-topic-checkbox:checked,
.row-checkbox:checked,
#checkbox-all:checked {
    background-color: #7c3aed !important; /* violet-600 */
    border-color: #7c3aed !important;
}

.topic-checkbox:checked:after,
.add-topic-checkbox:checked:after,
.edit-topic-checkbox:checked:after,
.row-checkbox:checked:after,
#checkbox-all:checked:after {
    content: '✓';
    color: white;
    font-size: 12px;
    font-weight: bold;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.topic-checkbox,
.add-topic-checkbox,
.edit-topic-checkbox,
.row-checkbox,
#checkbox-all {
    position: relative;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}
</style>

<!-- Breadcrumb -->
<div class="grid grid-cols-1 pb-6">
    <div class="md:flex items-center justify-between px-[2px]">
        <h4 class="text-[18px] font-medium text-gray-800 mb-sm-0 grow dark:text-gray-100 mb-2 md:mb-0">
            Assessment Category
        </h4>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 ltr:md:space-x-3 rtl:md:space-x-0">
                <li class="inline-flex items-center">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center text-sm text-gray-800 hover:text-gray-900 dark:text-zinc-100 dark:hover:text-white">
                        Assessment
                    </a>
                </li>
                <li>
                    <div class="flex items-center rtl:mr-2">
                        <i
                            class="font-semibold text-gray-600 align-middle far fa-angle-right text-13 rtl:rotate-180 dark:text-zinc-100"></i>
                        <a href="#"
                            class="text-sm font-medium text-gray-500 ltr:ml-2 rtl:mr-2 hover:text-gray-900 ltr:md:ml-2 rtl:md:mr-2 dark:text-gray-100 dark:hover:text-white">
                            Assessment Category
                        </a>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="col-span-12 xl:col-span-6">
    <div class="card dark:bg-zinc-800 dark:border-zinc-600">
        <!-- Header with search and buttons -->
        <div class="card-body border-b border-gray-100 dark:border-zinc-600 flex items-center justify-between">
            <!-- Search Bar -->
            <div class="flex items-center gap-3">
                <div class="relative">
                    <!-- Main Search Input -->
                    <input type="text" id="searchInput" placeholder="Search categories by name..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm w-64 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                </div>

                <!-- Search Button -->
                <button type="button" id="performSearchBtn" 
                    class="px-4 py-2 bg-violet-500 text-white rounded-lg text-sm hover:bg-violet-600 focus:ring-2 focus:ring-violet-500 focus:ring-offset-1 flex items-center gap-2">
                    <i class="fas fa-search"></i>
                    Search
                </button>

                <!-- Clear All Button -->
                <button type="button" id="clearAllFilters" class="hidden px-3 py-2 text-gray-600 hover:text-gray-800 text-sm dark:text-gray-400 dark:hover:text-gray-200 border border-gray-300 dark:border-zinc-600 rounded-lg">
                    <i class="fas fa-times"></i>
                    Clear All
                </button>
            </div>
            
            <div class="ml-auto flex items-center gap-3">
                <!-- Delete button (hidden by default) -->
                <button id="bulk-delete-btn" type="button"
                    class="hidden px-4 py-1.5 text-white bg-red-500 rounded hover:bg-red-600 text-sm">
                    Delete
                </button>

                <!-- Add button -->
                <button type="button" id="add-category-btn"
                    class="px-6 py-1.5 text-white btn bg-violet-500 border-violet-500 hover:bg-violet-600 hover:border-violet-600 focus:bg-violet-600 focus:border-violet-600 focus:ring focus:ring-violet-500/30 active:bg-violet-600 active:border-violet-600 text-sm">
                    Add
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="isolate">
                <div class="relative rounded-lg" style="max-height: 500px; min-height: 350px; overflow-y: auto; overflow-x: auto; display: flex; flex-direction: column; justify-content: flex-start;">
                    <table class="w-full min-w-[900px] text-xs text-center text-gray-500 leading-tight">
                        <thead
                            class="text-[11px] text-gray-700 uppercase dark:text-gray-100 bg-gray-50 dark:bg-zinc-700 sticky top-0 z-40 shadow-sm">
                            <tr>
                                <th class="p-3">
                                    <div class="flex items-center">
                                        <input id="checkbox-all" type="checkbox"
                                            class="w-4 h-4 border-gray-300 rounded bg-white">
                                        <label for="checkbox-all" class="sr-only">checkbox</label>
                                    </div>
                                </th>
                                <th class="px-2 py-1.5">Category Name</th>
                                <th class="px-2 py-1.5">Total Topics</th>
                                <th class="px-2 py-1.5">Actions</th>
                                <th class="px-2 py-1.5">Date Created</th>
                                <th class="px-2 py-1.5">Date Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $row)
                                <tr data-category-id="{{ $row->CategoryID }}"
                                    data-category-name="{{ $row->CategoryName }}"
                                    class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:hover:bg-zinc-700/50 dark:border-zinc-600">
                                    <td class="w-4 p-3">
                                        <div class="flex items-center">
                                            <input type="checkbox"
                                                class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                                        </div>
                                    </td>

                                    <td class="px-2 py-1.5">
                                        <button type="button" 
                                            class="text-violet-600 hover:text-violet-800 underline cursor-pointer"
                                            onclick="showTopicsModal('{{ $row->CategoryID }}', '{{ $row->CategoryName }}', {{ json_encode($row->topic_details) }})">
                                            {{ $row->CategoryName }}
                                        </button>
                                    </td>

                                    <!-- Total topics column -->
                                    <td class="px-2 py-1.5">{{ $row->topics_count ?? 0 }}</td>

                                    <!-- Action buttons -->
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
                                                        onclick="editCategory({{ $row->CategoryID }})"
                                                        class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                                        <i class="mdi mdi-pencil text-base"></i>
                                                        <span>Edit</span>
                                                    </button>
                                                    <button type="button"
                                                        onclick="deleteCategory({{ $row->CategoryID }})"
                                                        class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                                        <i class="mdi mdi-trash-can text-base"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-2 py-1.5">{{ \Carbon\Carbon::parse($row->DateCreate)->format('d M Y') }}</td>
                                    <td class="px-2 py-1.5">{{ \Carbon\Carbon::parse($row->DateUpdate)->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-2 py-1.5 text-center">No categories found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $records->links('pagination::tailwind') }}
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-zinc-800" style="max-height: 90vh;">
            <form id="editCategoryForm" class="flex flex-col h-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-zinc-800 flex-1 overflow-y-auto" style="max-height: calc(90vh - 60px);">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                Edit Category
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="edit_category_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category Name</label>
                                    <input type="text" id="edit_category_name" name="CategoryName" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Select Topics</label>
                                    
                                    <!-- Topic Dropdown -->
                                    <div class="relative">
                                        <button type="button" id="editTopicDropdownBtn" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white dark:bg-zinc-700 dark:border-zinc-600 dark:text-white hover:bg-gray-50 dark:hover:bg-zinc-600 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 flex items-center justify-between">
                                            <span id="editTopicDropdownText">Select Topics</span>
                                            <div class="flex items-center gap-2">
                                                <span id="editTopicCount" class="hidden bg-violet-500 text-white text-xs px-2 py-1 rounded-full">0</span>
                                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                            </div>
                                        </button>
                                        
                                        <!-- Dropdown Menu -->
                                        <div id="editTopicDropdownMenu" class="hidden absolute top-full left-0 mt-2 w-full bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto">
                                            <div class="p-3">
                                                <div class="flex items-center justify-between mb-3">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Topics</span>
                                                    <button type="button" id="editClearTopics" class="text-xs text-violet-600 hover:text-violet-800 dark:text-violet-400 dark:hover:text-violet-300">Clear All</button>
                                                </div>
                                                <input type="text" id="editTopicSearch" placeholder="Search topics..." 
                                                    class="w-full mb-3 px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-600 dark:text-white dark:placeholder-gray-400 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                                <div id="editTopicList" class="space-y-2">
                                                    <!-- Topics will be loaded here dynamically -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Selected Topics Display -->
                                    <div id="editSelectedTopicsDisplay" class="mt-3 hidden">
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selected Topics:</div>
                                        <div id="editSelectedTopicsList" class="flex flex-wrap gap-2">
                                            <!-- Selected topics will appear here as tags -->
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Select the topics that should be associated with this category.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-2 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700 ">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update Category
                    </button>
                    <button type="button" onclick="closeEditCategoryModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Topics Modal -->
<div id="topicsModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-zinc-800">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-zinc-800">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                            Topics for <span id="categoryName"></span>
                        </h3>
                        <div class="mt-4">
                            <div class="max-h-96 overflow-y-auto">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-4 py-3">Topic ID</th>
                                            <th scope="col" class="px-4 py-3">Topic Name</th>
                                        </tr>
                                    </thead>
                                    <tbody id="topicsTableBody">
                                        <!-- Topics will be populated here -->
                                    </tbody>
                                </table>
                                <div id="noTopicsMessage" class="hidden text-center py-4 text-gray-500 dark:text-gray-400">
                                    No topics found for this category.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700">
                <button type="button" onclick="closeTopicsModal()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-zinc-800" style="max-height: 90vh;">
            <form id="addCategoryForm" class="flex flex-col h-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-zinc-800 flex-1 overflow-y-auto" style="max-height: calc(90vh - 60px);">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                Add New Category
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="add_category_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category Name</label>
                                    <input type="text" id="add_category_name" name="CategoryName" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
                                        placeholder="Enter category name">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Select Topics</label>
                                    
                                    <!-- Topic Dropdown -->
                                    <div class="relative">
                                        <button type="button" id="addTopicDropdownBtn" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white dark:bg-zinc-700 dark:border-zinc-600 dark:text-white hover:bg-gray-50 dark:hover:bg-zinc-600 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 flex items-center justify-between">
                                            <span id="addTopicDropdownText">Select Topics</span>
                                            <div class="flex items-center gap-2">
                                                <span id="addTopicCount" class="hidden bg-violet-500 text-white text-xs px-2 py-1 rounded-full">0</span>
                                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                            </div>
                                        </button>
                                        
                                        <!-- Dropdown Menu -->
                                        <div id="addTopicDropdownMenu" class="hidden absolute top-full left-0 mt-2 w-full bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto">
                                            <div class="p-3">
                                                <div class="flex items-center justify-between mb-3">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Topics</span>
                                                    <button type="button" id="addClearTopics" class="text-xs text-violet-600 hover:text-violet-800 dark:text-violet-400 dark:hover:text-violet-300">Clear All</button>
                                                </div>
                                                <input type="text" id="addTopicSearch" placeholder="Search topics..." 
                                                    class="w-full mb-3 px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-600 dark:text-white dark:placeholder-gray-400 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                                <div id="addTopicList" class="space-y-2">
                                                    <!-- Topics will be loaded here dynamically -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Selected Topics Display -->
                                    <div id="addSelectedTopicsDisplay" class="mt-3 hidden">
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selected Topics:</div>
                                        <div id="addSelectedTopicsList" class="flex flex-wrap gap-2">
                                            <!-- Selected topics will appear here as tags -->
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Select the topics that should be associated with this category.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-2 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700 ">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Add Category
                    </button>
                    <button type="button" onclick="closeAddCategoryModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('checkbox-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
        
        // Search elements
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('tbody tr');
        const performSearchBtn = document.getElementById('performSearchBtn');
        const clearAllFilters = document.getElementById('clearAllFilters');
        
        // Search state
        let searchTimeout = null;

        // Debounced search function
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performFilteredSearch();
            }, 150); // Reduced delay for faster response
        }

        // Perform search with filters - AJAX VERSION
        function performFilteredSearch() {
            const searchTerm = searchInput.value.trim();
            
            // Show loading state
            const tableBody = document.querySelector('tbody');
            const originalContent = tableBody.innerHTML;
            tableBody.innerHTML = '<tr><td colspan="6" class="px-2 py-1.5 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            
            // Build query parameters for AJAX request
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            
            // Update URL without page reload
            const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
            window.history.pushState({ path: newUrl }, '', newUrl);
            
            // Make AJAX request
            fetch(`/category?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update table content
                    tableBody.innerHTML = data.html;
                    
                    // Update pagination if exists
                    const paginationContainer = document.querySelector('.mt-4');
                    if (paginationContainer && data.pagination && data.pagination.links) {
                        paginationContainer.innerHTML = data.pagination.links;
                    }
                    
                    // Reinitialize dynamic content
                    initializeRowCheckboxes();
                    initializeDropdowns();
                } else {
                    console.error('Search failed:', data.message);
                    tableBody.innerHTML = originalContent;
                }
            })
            .catch(error => {
                console.error('Error performing search:', error);
                tableBody.innerHTML = originalContent;
            });
        }

        // Clear all filters - AJAX VERSION
        function clearAllFiltersAction() {
            // Clear text search
            searchInput.value = '';
            
            // Update clear button visibility
            updateClearAllButton();
            
            // Show loading state
            const tableBody = document.querySelector('tbody');
            tableBody.innerHTML = '<tr><td colspan="6" class="px-2 py-1.5 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            
            // Update URL without page reload
            const newUrl = window.location.pathname;
            window.history.pushState({ path: newUrl }, '', newUrl);
            
            // Make AJAX request to get all categories
            fetch('/category', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update table content
                    tableBody.innerHTML = data.html;
                    
                    // Update pagination if exists
                    const paginationContainer = document.querySelector('.mt-4');
                    if (paginationContainer && data.pagination && data.pagination.links) {
                        paginationContainer.innerHTML = data.pagination.links;
                    }
                    
                    // Reinitialize dynamic content
                    initializeRowCheckboxes();
                    initializeDropdowns();
                } else {
                    console.error('Clear filters failed:', data.message);
                    location.reload(); // Fallback to page reload
                }
            })
            .catch(error => {
                console.error('Error clearing filters:', error);
                location.reload(); // Fallback to page reload
            });
        }

        // Reinitialize row checkboxes after AJAX content update
        function initializeRowCheckboxes() {
            const selectAll = document.getElementById('checkbox-all');
            const newRowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            
            // Update select all functionality
            if (selectAll) {
                // Remove old event listeners by cloning the element
                const newSelectAll = selectAll.cloneNode(true);
                selectAll.parentNode.replaceChild(newSelectAll, selectAll);
                
                newSelectAll.addEventListener('change', () => {
                    newRowCheckboxes.forEach(cb => cb.checked = newSelectAll.checked);
                    updateBulkDeleteVisibility();
                });
            }
            
            // Add event listeners to new checkboxes
            newRowCheckboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    updateSelectAllState();
                    updateBulkDeleteVisibility();
                });
            });
            
            // Update state
            updateSelectAllState();
            updateBulkDeleteVisibility();
        }

        // Reinitialize dropdown functionality after AJAX content update
        function initializeDropdowns() {
            document.querySelectorAll('.dropdown').forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                if (toggle && menu) {
                    // Remove existing event listener by cloning
                    const newToggle = toggle.cloneNode(true);
                    toggle.parentNode.replaceChild(newToggle, toggle);
                    
                    newToggle.addEventListener('click', (e) => {
                        e.stopPropagation();
                        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
                        menu.classList.toggle('hidden');
                    });
                }
            });
        }

        // Show no results message
        function showNoResultsMessage(show) {
            let noResultsRow = document.querySelector('.no-results-row');
            
            if (show) {
                if (!noResultsRow) {
                    const tbody = document.querySelector('tbody');
                    noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    noResultsRow.innerHTML = `
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No categories found</p>
                                <p class="text-sm">Try adjusting your search term</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(noResultsRow);
                }
                noResultsRow.style.display = '';
            } else {
                if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
            }
        }

        function updateSelectAllState() {
            // With server-side filtering, work with all checkboxes on current page
            const currentRowCheckboxes = document.querySelectorAll('.row-checkbox');
            const checkedBoxes = Array.from(currentRowCheckboxes).filter(cb => cb.checked);
            const selectAll = document.getElementById('checkbox-all');
            
            if (!selectAll) return;
            
            if (currentRowCheckboxes.length === 0) {
                selectAll.indeterminate = false;
                selectAll.checked = false;
            } else if (checkedBoxes.length === currentRowCheckboxes.length) {
                selectAll.indeterminate = false;
                selectAll.checked = true;
            } else if (checkedBoxes.length > 0) {
                selectAll.indeterminate = true;
                selectAll.checked = false;
            } else {
                selectAll.indeterminate = false;
                selectAll.checked = false;
            }
        }

        // Update clear all button visibility
        function updateClearAllButton() {
            const hasFilters = searchInput.value.trim();
            clearAllFilters.classList.toggle('hidden', !hasFilters);
        }

        // Event listeners for search (prevent page refresh)
        performSearchBtn.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent any form submission
            performFilteredSearch();
        });
        
        clearAllFilters.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent any form submission
            clearAllFiltersAction();
        });
        
        // Real-time search as user types with debouncing (no page refresh)
        searchInput.addEventListener('input', (e) => {
            e.preventDefault(); // Prevent any default behavior
            updateClearAllButton();
            debounceSearch();
        });
        
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault(); // Prevent form submission on Enter
                clearTimeout(searchTimeout);
                performFilteredSearch();
            }
        });

        function updateBulkDeleteVisibility() {
            // With server-side filtering, all rows on current page are visible
            const currentRowCheckboxes = document.querySelectorAll('.row-checkbox');
            const anyChecked = Array.from(currentRowCheckboxes).some(cb => cb.checked);
            if (bulkDeleteBtn) {
                bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', () => {
                // With server-side filtering, all rows on current page are visible
                const currentRowCheckboxes = document.querySelectorAll('.row-checkbox');
                currentRowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                updateBulkDeleteVisibility();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                updateSelectAllState();
                updateBulkDeleteVisibility();
            });
        });

        // Dropdown toggle
        document.querySelectorAll('.dropdown').forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');

            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
                menu.classList.toggle('hidden');
            });
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        });

        // Edit category form submission
        document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            updateCategory();
        });

        // Add category button event listener
        document.getElementById('add-category-btn').addEventListener('click', function() {
            document.getElementById('addCategoryModal').classList.remove('hidden');
        });

        // Add category form submission
        document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            addCategory();
        });

        // Initialize search functionality
        initializeRowCheckboxes(); // Initialize checkbox functionality
        initializeDropdowns(); // Initialize dropdown functionality

        // Make performFilteredSearch available globally for modal callbacks
        window.performFilteredSearch = performFilteredSearch;

        // Bulk delete button event listener
        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                bulkDeleteCategories();
            });
        }

        // Initialize Add Topic Dropdown
        initializeAddTopicDropdown();

        // Initialize Edit Topic Dropdown
        initializeEditTopicDropdown();

        // Initialize filters and restore state from URL parameters
        function initializeFilters() {
            // Restore filter states from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            
            // Restore search term
            const searchParam = urlParams.get('search');
            if (searchParam) {
                searchInput.value = searchParam;
                updateClearAllButton();
            }
        }

        // Initialize filters
        initializeFilters();
    });

    // Add Topic Dropdown Functions
    function initializeAddTopicDropdown() {
        const allTopics = @json($allTopics ?? []);
        const dropdownBtn = document.getElementById('addTopicDropdownBtn');
        const dropdownMenu = document.getElementById('addTopicDropdownMenu');
        const searchInput = document.getElementById('addTopicSearch');
        const topicList = document.getElementById('addTopicList');
        const clearBtn = document.getElementById('addClearTopics');
        const dropdownText = document.getElementById('addTopicDropdownText');
        const countSpan = document.getElementById('addTopicCount');
        const selectedDisplay = document.getElementById('addSelectedTopicsDisplay');
        const selectedList = document.getElementById('addSelectedTopicsList');

        const form = document.getElementById('addCategoryForm');
        form._selectedTopics = new Set();
        let selectedTopics = form._selectedTopics;
        let selectedTopicData = new Map(); // Store topic data for display

        // Load topics into dropdown
        function loadTopics(searchTerm = '') {
            const filteredTopics = allTopics.filter(topic => 
                topic.TopicName.toLowerCase().includes(searchTerm.toLowerCase())
            );

            topicList.innerHTML = '';

            if (filteredTopics.length === 0) {
                topicList.innerHTML = `
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        <p>No topics found</p>
                    </div>
                `;
                return;
            }

            filteredTopics.forEach(topic => {
                const isSelected = selectedTopics.has(topic.TopicID.toString());
                const topicItem = document.createElement('div');
                topicItem.className = 'flex items-center p-2 hover:bg-gray-50 dark:hover:bg-zinc-600 rounded cursor-pointer';
                topicItem.innerHTML = `
                    <input type="checkbox" 
                           id="add_topic_${topic.TopicID}" 
                           name="topic_ids[]" 
                           value="${topic.TopicID}"
                           class="add-topic-checkbox w-4 h-4 border-gray-300 rounded bg-white dark:bg-zinc-600 dark:border-zinc-500 text-violet-600 focus:ring-violet-500"
                           ${isSelected ? 'checked' : ''}>
                    <label for="add_topic_${topic.TopicID}" class="ml-2 text-sm text-gray-900 dark:text-white cursor-pointer flex-1">
                        ${topic.TopicName} (ID: ${topic.TopicID})
                    </label>
                `;
                
                topicItem.addEventListener('click', (e) => {
                    if (e.target.tagName !== 'INPUT') {
                        const checkbox = topicItem.querySelector('input[type="checkbox"]');
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change'));
                    }
                });

                const checkbox = topicItem.querySelector('input[type="checkbox"]');
                checkbox.addEventListener('change', (e) => {
                    if (e.target.checked) {
                        selectedTopics.add(topic.TopicID.toString());
                        selectedTopicData.set(topic.TopicID.toString(), {
                            name: topic.TopicName,
                            id: topic.TopicID
                        });
                    } else {
                        selectedTopics.delete(topic.TopicID.toString());
                        selectedTopicData.delete(topic.TopicID.toString());
                    }
                    updateDropdownText();
                });

                topicList.appendChild(topicItem);
            });
        }

        // Update dropdown button text and count
        function updateDropdownText() {
            if (selectedTopics.size === 0) {
                dropdownText.textContent = 'Select Topics';
                countSpan.classList.add('hidden');
                selectedDisplay.classList.add('hidden');
            } else {
                dropdownText.textContent = `${selectedTopics.size} topic(s) selected`;
                countSpan.textContent = selectedTopics.size;
                countSpan.classList.remove('hidden');
                selectedDisplay.classList.remove('hidden');
                updateSelectedTopicsDisplay();
            }
        }

        // Update selected topics display
        function updateSelectedTopicsDisplay() {
            selectedList.innerHTML = '';
            
            selectedTopicData.forEach((topicInfo, topicId) => {
                const tag = document.createElement('div');
                tag.className = 'inline-flex items-center gap-1 px-3 py-1 bg-violet-500 text-white text-sm rounded-full';
                tag.innerHTML = `
                    <span>${topicInfo.name}</span>
                    <button type="button" onclick="removeSelectedTopic('${topicId}')" class="hover:bg-violet-600 rounded-full p-1 ml-1">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                `;
                selectedList.appendChild(tag);
            });
        }

        // Toggle dropdown
        dropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('hidden');
            if (!dropdownMenu.classList.contains('hidden')) {
                searchInput.focus();
            }
        });

        // Search functionality
        searchInput.addEventListener('input', (e) => {
            loadTopics(e.target.value);
        });

        // Clear all selections
        clearBtn.addEventListener('click', () => {
            selectedTopics.clear();
            selectedTopicData.clear();
            loadTopics(searchInput.value);
            updateDropdownText();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });

        // Prevent dropdown from closing when clicking inside
        dropdownMenu.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Initialize dropdown
        loadTopics();
        updateDropdownText();

        // Expose clear function globally for modal reset
        window.clearAddTopicDropdown = function() {
            selectedTopics.clear();
            selectedTopicData.clear();
            searchInput.value = '';
            loadTopics();
            updateDropdownText();
        };

        // Expose remove function globally for tag removal
        window.removeSelectedTopic = function(topicId) {
            selectedTopics.delete(topicId);
            selectedTopicData.delete(topicId);
            loadTopics(searchInput.value); // Refresh to update checkboxes
            updateDropdownText();
        };
    }

    // Edit Topic Dropdown Functions
    function initializeEditTopicDropdown() {
        const allTopics = @json($allTopics ?? []);
        const dropdownBtn = document.getElementById('editTopicDropdownBtn');
        const dropdownMenu = document.getElementById('editTopicDropdownMenu');
        const searchInput = document.getElementById('editTopicSearch');
        const topicList = document.getElementById('editTopicList');
        const clearBtn = document.getElementById('editClearTopics');
        const dropdownText = document.getElementById('editTopicDropdownText');
        const countSpan = document.getElementById('editTopicCount');
        const selectedDisplay = document.getElementById('editSelectedTopicsDisplay');
        const selectedList = document.getElementById('editSelectedTopicsList');

        const form = document.getElementById('editCategoryForm');
        form._selectedTopics = new Set();
        let selectedTopics = form._selectedTopics;
        let selectedTopicData = new Map(); // Store topic data for display

        // Load topics into dropdown
        function loadTopics(searchTerm = '') {
            const filteredTopics = allTopics.filter(topic => 
                topic.TopicName.toLowerCase().includes(searchTerm.toLowerCase())
            );

            topicList.innerHTML = '';

            if (filteredTopics.length === 0) {
                topicList.innerHTML = `
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        <p>No topics found</p>
                    </div>
                `;
                return;
            }

            filteredTopics.forEach(topic => {
                const isSelected = selectedTopics.has(topic.TopicID.toString());
                const topicItem = document.createElement('div');
                topicItem.className = 'flex items-center p-2 hover:bg-gray-50 dark:hover:bg-zinc-600 rounded cursor-pointer';
                topicItem.innerHTML = `
                    <input type="checkbox" 
                           id="edit_topic_${topic.TopicID}" 
                           name="topic_ids[]" 
                           value="${topic.TopicID}"
                           class="edit-topic-checkbox w-4 h-4 border-gray-300 rounded bg-white dark:bg-zinc-600 dark:border-zinc-500 text-violet-600 focus:ring-violet-500"
                           ${isSelected ? 'checked' : ''}>
                    <label for="edit_topic_${topic.TopicID}" class="ml-2 text-sm text-gray-900 dark:text-white cursor-pointer flex-1">
                        ${topic.TopicName} (ID: ${topic.TopicID})
                    </label>
                `;
                
                topicItem.addEventListener('click', (e) => {
                    if (e.target.tagName !== 'INPUT') {
                        const checkbox = topicItem.querySelector('input[type="checkbox"]');
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change'));
                    }
                });

                const checkbox = topicItem.querySelector('input[type="checkbox"]');
                checkbox.addEventListener('change', (e) => {
                    if (e.target.checked) {
                        selectedTopics.add(topic.TopicID.toString());
                        selectedTopicData.set(topic.TopicID.toString(), {
                            name: topic.TopicName,
                            id: topic.TopicID
                        });
                    } else {
                        selectedTopics.delete(topic.TopicID.toString());
                        selectedTopicData.delete(topic.TopicID.toString());
                    }
                    updateDropdownText();
                });

                topicList.appendChild(topicItem);
            });
        }

        // Update dropdown button text and count
        function updateDropdownText() {
            if (selectedTopics.size === 0) {
                dropdownText.textContent = 'Select Topics';
                countSpan.classList.add('hidden');
                selectedDisplay.classList.add('hidden');
            } else {
                dropdownText.textContent = `${selectedTopics.size} topic(s) selected`;
                countSpan.textContent = selectedTopics.size;
                countSpan.classList.remove('hidden');
                selectedDisplay.classList.remove('hidden');
                updateSelectedTopicsDisplay();
            }
        }

        // Update selected topics display
        function updateSelectedTopicsDisplay() {
            selectedList.innerHTML = '';
            
            selectedTopicData.forEach((topicInfo, topicId) => {
                const tag = document.createElement('div');
                tag.className = 'inline-flex items-center gap-1 px-3 py-1 bg-violet-500 text-white text-sm rounded-full';
                tag.innerHTML = `
                    <span>${topicInfo.name}</span>
                    <button type="button" onclick="removeEditSelectedTopic('${topicId}')" class="hover:bg-violet-600 rounded-full p-1 ml-1">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                `;
                selectedList.appendChild(tag);
            });
        }

        // Toggle dropdown
        dropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('hidden');
            if (!dropdownMenu.classList.contains('hidden')) {
                searchInput.focus();
            }
        });

        // Search functionality
        searchInput.addEventListener('input', (e) => {
            loadTopics(e.target.value);
        });

        // Clear all selections
        clearBtn.addEventListener('click', () => {
            selectedTopics.clear();
            selectedTopicData.clear();
            loadTopics(searchInput.value);
            updateDropdownText();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });

        // Prevent dropdown from closing when clicking inside
        dropdownMenu.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Initialize dropdown
        loadTopics();
        updateDropdownText();

        // Set selected topics from external call
        window.setEditSelectedTopics = function(topicIds) {
            selectedTopics.clear();
            selectedTopicData.clear();
            
            if (topicIds && topicIds.length > 0) {
                topicIds.forEach(topicId => {
                    const topicIdStr = String(topicId);
                    const topic = allTopics.find(t => String(t.TopicID) === topicIdStr);
                    if (topic) {
                        selectedTopics.add(topicIdStr);
                        selectedTopicData.set(topicIdStr, {
                            name: topic.TopicName,
                            id: topic.TopicID
                        });
                    }
                });
            }
            
            loadTopics(searchInput.value);
            updateDropdownText();
        };

        // Expose clear function globally for modal reset
        window.clearEditTopicDropdown = function() {
            selectedTopics.clear();
            selectedTopicData.clear();
            searchInput.value = '';
            loadTopics();
            updateDropdownText();
        };

        // Expose remove function globally for tag removal
        window.removeEditSelectedTopic = function(topicId) {
            selectedTopics.delete(topicId);
            selectedTopicData.delete(topicId);
            loadTopics(searchInput.value); // Refresh to update checkboxes
            updateDropdownText();
        };
    }

    let currentCategoryId = null;

    // Category edit and delete functions
    function editCategory(categoryId) {
        currentCategoryId = categoryId;
        
        // Fetch category details including assigned topics
        fetch(`/category/${categoryId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate category name
                    document.getElementById('edit_category_name').value = data.category.CategoryName;
                    
                    // Set selected topics in dropdown
                    if (window.setEditSelectedTopics) {
                        window.setEditSelectedTopics(data.assigned_topic_ids || []);
                    }
                    
                    // Show modal
                    document.getElementById('editCategoryModal').classList.remove('hidden');
                } else {
                    alert('Error loading category details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading category details.');
            });
    }

    function closeEditCategoryModal() {
        document.getElementById('editCategoryModal').classList.add('hidden');
        currentCategoryId = null;
        // Clear dropdown selections
        if (window.clearEditTopicDropdown) {
            window.clearEditTopicDropdown();
        }
    }

    function closeAddCategoryModal() {
        document.getElementById('addCategoryModal').classList.add('hidden');
        // Clear the form
        document.getElementById('addCategoryForm').reset();
        // Clear dropdown selections
        if (window.clearAddTopicDropdown) {
            window.clearAddTopicDropdown();
        }
    }

    function addCategory() {
        const formData = new FormData(document.getElementById('addCategoryForm'));
        const data = Object.fromEntries(formData);
        
        // Basic validation
        if (!data.CategoryName || data.CategoryName.trim() === '') {
            alert('Please enter a category name.');
            return;
        }
        
        // Get selected topic IDs from the persistent set
        const selectedTopics = Array.from(document.getElementById('addCategoryForm')._selectedTopics || []);
        
        // Validate at least 1 topic is selected
        if (selectedTopics.length === 0) {
            alert('Please select at least 1 topic for this category.');
            return;
        }
        
        data.topic_ids = selectedTopics;

        fetch('/category', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            return response.json().then(data => {
                if (!response.ok) {
                    if (response.status === 422) {
                        throw new Error('This category name already exists in the database. Please choose a different name.');
                    } else {
                        throw new Error(data.message || `HTTP error! status: ${response.status}`);
                    }
                }
                return data;
            });
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeAddCategoryModal();
                // Refresh the current filtered view instead of full page reload
                performFilteredSearch();
            } else {
                alert('Error: ' + (data.message || 'Unknown error occurred'));
                console.error('Server error:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message);
        });
    }

    function updateCategory() {
        if (!currentCategoryId) return;

        const formData = new FormData(document.getElementById('editCategoryForm'));
        const data = Object.fromEntries(formData);
        
        // Basic validation
        if (!data.CategoryName || data.CategoryName.trim() === '') {
            alert('Please enter a category name.');
            return;
        }
        
        // Get selected topic IDs from the persistent set
        const selectedTopics = Array.from(document.getElementById('editCategoryForm')._selectedTopics || []);
        
        // Validate at least 1 topic is selected
        if (selectedTopics.length === 0) {
            alert('Please select at least 1 topic for this category.');
            return;
        }
        
        data.topic_ids = selectedTopics;

        fetch(`/category/${currentCategoryId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeEditCategoryModal();
                // Refresh the current filtered view instead of full page reload
                performFilteredSearch();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the category.');
        });
    }

    function deleteCategory(categoryId) {
        if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
            return;
        }

        fetch(`/category/${categoryId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Refresh the current filtered view instead of full page reload
                performFilteredSearch();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the category.');
        });
    }

    function bulkDeleteCategories() {
        const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            alert('Please select categories to delete.');
            return;
        }

        const selectedIds = [];
        selectedCheckboxes.forEach(function(checkbox) {
            const row = checkbox.closest('tr');
            const categoryId = row.getAttribute('data-category-id');
            if (categoryId) {
                selectedIds.push(parseInt(categoryId));
            }
        });

        if (selectedIds.length === 0) {
            alert('Unable to identify selected categories.');
            return;
        }

        const categoryText = selectedIds.length === 1 ? 'category' : 'categories';
        if (!confirm(`Are you sure you want to delete ${selectedIds.length} ${categoryText}? This action cannot be undone.`)) {
            return;
        }

        fetch('/category/bulk-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Refresh the current filtered view instead of full page reload
                performFilteredSearch();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting categories.');
        });
    }

    // Modal functions for topics
    function showTopicsModal(categoryId, categoryName, topicDetails) {
        const modal = document.getElementById('topicsModal');
        const categoryNameSpan = document.getElementById('categoryName');
        const tableBody = document.getElementById('topicsTableBody');
        const noTopicsMessage = document.getElementById('noTopicsMessage');
        
        categoryNameSpan.textContent = categoryName;
        
        // Clear previous content
        tableBody.innerHTML = '';
        
        if (topicDetails && topicDetails.length > 0) {
            noTopicsMessage.classList.add('hidden');
            topicDetails.forEach(topic => {
                const row = document.createElement('tr');
                row.className = 'bg-white border-b dark:bg-zinc-800 dark:border-zinc-600';
                row.innerHTML = `
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">${topic.TopicID}</td>
                    <td class="px-4 py-3">${topic.TopicName}</td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            noTopicsMessage.classList.remove('hidden');
        }
        
        modal.classList.remove('hidden');
    }

    function closeTopicsModal() {
        const modal = document.getElementById('topicsModal');
        modal.classList.add('hidden');
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        const topicsModal = document.getElementById('topicsModal');
        const editCategoryModal = document.getElementById('editCategoryModal');
        const addCategoryModal = document.getElementById('addCategoryModal');
        
        if (event.target === topicsModal) {
            closeTopicsModal();
        }
        
        if (event.target === editCategoryModal) {
            closeEditCategoryModal();
        }
        
        if (event.target === addCategoryModal) {
            closeAddCategoryModal();
        }
    });
</script>
