@extends('layout.appMain')

@section('content')
<!-- CSRF token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Custom styles for checkboxes -->
<style>
.row-checkbox:checked,
#checkbox-all:checked,
.category-filter-checkbox:checked {
    background-color: #7c3aed !important; /* violet-600 */
    border-color: #7c3aed !important;
}

.row-checkbox:checked:after,
#checkbox-all:checked:after,
.category-filter-checkbox:checked:after {
    content: '✓';
    color: white;
    font-size: 12px;
    font-weight: bold;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.row-checkbox,
#checkbox-all,
.category-filter-checkbox {
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
            Assessment Topics
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
                            Assessment Topics
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
                    <input type="text" id="searchInput" placeholder="Search topics by name..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm w-64 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="relative">
                    <button type="button" id="categoryFilterBtn" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white dark:bg-zinc-700 dark:border-zinc-600 dark:text-white hover:bg-gray-50 dark:hover:bg-zinc-600 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 flex items-center gap-2">
                        <i class="fas fa-folder text-gray-500"></i>
                        <span id="categoryFilterText">Categories</span>
                        <span id="categoryCount" class="hidden bg-violet-500 text-white text-xs px-2 py-1 rounded-full">0</span>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    
                    <!-- Category Dropdown -->
                    <div id="categoryDropdownFilter" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto">
                        <div class="p-3">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Categories</span>
                                <button type="button" id="clearCategories" class="text-xs text-violet-600 hover:text-violet-800">Clear All</button>
                            </div>
                            <input type="text" id="categorySearchFilter" placeholder="Search categories..." 
                                class="w-full mb-3 px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-600 dark:text-white">
                            <div id="categoryListFilter" class="space-y-2">
                                <!-- Categories will be loaded here -->
                            </div>
                        </div>
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
                <!-- Export button -->
                <button id="export-excel-btn" type="button"
                    class="px-6 py-1.5 text-white bg-green-500 rounded hover:bg-green-600 text-sm">
                    <i class="fas fa-file-csv"></i>
                    Export to CSV
                </button>

                <!-- Delete button (hidden by default) -->
                <button id="bulk-delete-btn" type="button"
                    class="hidden px-4 py-1.5 text-white bg-red-500 rounded hover:bg-red-600 text-sm">
                    Delete
                </button>

                <!-- Add button -->
                <button type="button" id="add-topic-btn"
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
                            <th class="px-2 py-1.5">Topic Name</th>
                            <th class="px-2 py-1.5">Categories</th>
                            <th class="px-2 py-1.5">Actions</th>
                            <th class="px-2 py-1.5">Date Created</th>
                            <th class="px-2 py-1.5">Date Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $row)
                            @php
                                // Find categories that contain this topic
                                $topicCategories = collect($allCategories ?? [])->filter(function($category) use ($row) {
                                    $topicIds = explode(',', $category->TopicIDs ?? '');
                                    return in_array($row->TopicID, $topicIds);
                                });
                                $categoryNames = $topicCategories->pluck('CategoryName')->join(', ');
                                $categoryIds = $topicCategories->pluck('CategoryID')->join(',');
                            @endphp
                            <tr class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:hover:bg-zinc-700/50 dark:border-zinc-600"
                                data-topic-id="{{ $row->TopicID }}"
                                data-topic-name="{{ $row->TopicName }}"
                                data-category-ids="{{ $categoryIds }}"
                                data-category-names="{{ $categoryNames }}">
                                <td class="w-4 p-3">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white"
                                            data-topic-id="{{ $row->TopicID }}">
                                    </div>
                                </td>
                                <td class="px-2 py-1.5">{{ $row->TopicName }}</td>
                                
                                <!-- Categories -->
                                <td class="px-2 py-1.5">
                                    @if($categoryNames)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($topicCategories as $category)
                                                <span class="inline-block px-2 py-1 text-xs bg-violet-100 text-violet-800 rounded-full dark:bg-violet-900 dark:text-violet-200">
                                                    {{ $category->CategoryName }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs italic">No categories</span>
                                    @endif
                                </td>

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
                                                    onclick="editTopic({{ $row->TopicID }}, '{{ $row->TopicName }}')"
                                                    class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                                    <i class="mdi mdi-pencil text-base"></i>
                                                    <span>Edit</span>
                                                </button>
                                                <button type="button"
                                                    onclick="deleteTopic({{ $row->TopicID }})"
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
                                <td colspan="6" class="px-2 py-1.5 text-center">No topics found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $records->links('pagination::tailwind') }}
    </div>
</div>

<!-- Edit Topic Modal -->
<div id="editTopicModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Black transparent overlay like question page -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-zinc-800">
            <form id="editTopicForm">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-zinc-800">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                Edit Topic
                            </h3>
                            <div class="mt-4">
                                <div>
                                    <label for="edit_topic_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Topic Name</label>
                                    <input type="text" id="edit_topic_name" name="TopicName" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update Topic
                    </button>
                    <button type="button" onclick="closeEditTopicModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Topic Modal -->
<div id="addTopicModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Black transparent overlay like question page -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-zinc-800">
            <form id="addTopicForm">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-zinc-800">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                Add New Topic
                            </h3>
                            <div class="mt-4">
                                <div>
                                    <label for="add_topic_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Topic Name</label>
                                    <input type="text" id="add_topic_name" name="TopicName" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
                                        placeholder="Enter topic name">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Add Topic
                    </button>
                    <button type="button" onclick="closeAddTopicModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
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
        const categoryFilterBtn = document.getElementById('categoryFilterBtn');
        const categoryDropdownFilter = document.getElementById('categoryDropdownFilter');
        const categoryListFilter = document.getElementById('categoryListFilter');
        const categorySearchFilter = document.getElementById('categorySearchFilter');
        const categoryCount = document.getElementById('categoryCount');
        const clearCategories = document.getElementById('clearCategories');
        const performSearchBtn = document.getElementById('performSearchBtn');
        const clearAllFilters = document.getElementById('clearAllFilters');
        
        // Search state
        let allCategories = [];
        let selectedCategoriesForSearch = new Set();
        let searchTimeout = null;

        // Debounced search function
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performFilteredSearch();
            }, 800); // Increased delay for server requests
        }

        // Load categories for filters
        function loadFiltersData() {
            console.log('Loading categories for topic filters...');
            
            try {
                // Load categories from the page data
                const categories = @json($allCategories ?? []);
                allCategories = categories;
                console.log('Loaded categories:', allCategories);
                renderCategoryList();
                console.log('Filter data loaded successfully!');
            } catch (error) {
                console.error('Error loading filter data:', error);
            }
        }

        // Render category list
        function renderCategoryList() {
            console.log('Rendering category list, categories count:', allCategories.length);
            const searchTerm = categorySearchFilter.value.toLowerCase();
            const filteredCategories = allCategories.filter(category => 
                category.CategoryName.toLowerCase().includes(searchTerm) || 
                category.CategoryID.toString().includes(searchTerm)
            );

            console.log('Filtered categories count:', filteredCategories.length);
            categoryListFilter.innerHTML = '';
            filteredCategories.forEach(category => {
                const div = document.createElement('div');
                div.className = 'flex items-center';
                div.innerHTML = `
                    <input type="checkbox" id="category_${category.CategoryID}" 
                           value="${category.CategoryID}" 
                           data-name="${category.CategoryName}"
                           class="category-filter-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                    <label for="category_${category.CategoryID}" class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        ${category.CategoryName}
                    </label>
                `;
                categoryListFilter.appendChild(div);
            });

            // Restore selected state
            selectedCategoriesForSearch.forEach(categoryId => {
                const checkbox = document.getElementById(`category_${categoryId}`);
                if (checkbox) checkbox.checked = true;
            });

            // Add event listeners
            document.querySelectorAll('.category-filter-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', handleCategoryChange);
            });
            
            console.log('Category list rendered with', filteredCategories.length, 'items');
        }

        // Handle category selection
        function handleCategoryChange(event) {
            const categoryId = event.target.value;
            
            if (event.target.checked) {
                selectedCategoriesForSearch.add(categoryId);
            } else {
                selectedCategoriesForSearch.delete(categoryId);
            }
            
            updateCategoryDisplay();
        }

        // Update category display
        function updateCategoryDisplay() {
            const count = selectedCategoriesForSearch.size;
            if (count > 0) {
                categoryCount.textContent = count;
                categoryCount.classList.remove('hidden');
            } else {
                categoryCount.classList.add('hidden');
            }
            updateClearAllButton();
        }

        // Update clear all button visibility
        function updateClearAllButton() {
            const hasFilters = selectedCategoriesForSearch.size > 0 || searchInput.value.trim();
            clearAllFilters.classList.toggle('hidden', !hasFilters);
        }

        // Perform search with filters - AJAX VERSION
        function performFilteredSearch() {
            const searchTerm = searchInput.value.trim();
            const selectedCategoryIds = Array.from(selectedCategoriesForSearch);
            
            // Show loading state
            const tableBody = document.querySelector('tbody');
            const originalContent = tableBody.innerHTML;
            tableBody.innerHTML = '<tr><td colspan="6" class="px-2 py-1.5 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            
            // Build query parameters for AJAX request
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (selectedCategoryIds.length > 0) params.append('categories', selectedCategoryIds.join(','));
            
            // Update URL without page reload
            const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
            window.history.pushState({ path: newUrl }, '', newUrl);
            
            // Make AJAX request
            fetch(`/topic?${params.toString()}`, {
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
            
            // Clear category selections  
            selectedCategoriesForSearch.clear();
            document.querySelectorAll('.category-filter-checkbox').forEach(cb => cb.checked = false);
            updateCategoryDisplay();
            
            // Close dropdowns
            categoryDropdownFilter.classList.add('hidden');
            
            // Show loading state
            const tableBody = document.querySelector('tbody');
            tableBody.innerHTML = '<tr><td colspan="6" class="px-2 py-1.5 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            
            // Update URL without page reload
            const newUrl = window.location.pathname;
            window.history.pushState({ path: newUrl }, '', newUrl);
            
            // Make AJAX request to get all topics
            fetch('/topic', {
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
            const selectAllElement = document.getElementById('checkbox-all');
            const newRowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            
            // Update select all functionality
            if (selectAllElement) {
                // Remove old event listeners by cloning the element
                const newSelectAll = selectAllElement.cloneNode(true);
                selectAllElement.parentNode.replaceChild(newSelectAll, selectAllElement);
                
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

        // Remove the showNoResultsMessage function since server-side filtering handles this

        function updateSelectAllState() {
            // With server-side filtering, work with all checkboxes on current page
            const selectAll = document.getElementById('checkbox-all');
            const currentRowCheckboxes = document.querySelectorAll('.row-checkbox');
            const checkedBoxes = Array.from(currentRowCheckboxes).filter(cb => cb.checked);
            
            if (!selectAll) return; // Safety check
            
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

        // Event listeners for search
        categoryFilterBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Category filter button clicked');
            categoryDropdownFilter.classList.toggle('hidden');
        });

        clearCategories.addEventListener('click', () => {
            selectedCategoriesForSearch.clear();
            document.querySelectorAll('.category-filter-checkbox').forEach(cb => cb.checked = false);
            updateCategoryDisplay();
        });

        categorySearchFilter.addEventListener('input', renderCategoryList);
        performSearchBtn.addEventListener('click', performFilteredSearch);
        clearAllFilters.addEventListener('click', clearAllFiltersAction);
        
        // Real-time search as user types with debouncing
        searchInput.addEventListener('input', () => {
            updateClearAllButton();
            debounceSearch();
        });
        
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout); // Cancel debounce on Enter
                performFilteredSearch();
            }
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!categoryFilterBtn.contains(e.target) && !categoryDropdownFilter.contains(e.target)) {
                categoryDropdownFilter.classList.add('hidden');
            }
        });

        // Initialize search
        console.log('Initializing search functionality...');
        loadFiltersData();
        initializeFilters(); // Restore filters from URL
        initializeRowCheckboxes(); // Initialize checkbox functionality
        initializeDropdowns(); // Initialize dropdown functionality
        console.log('Search initialization complete');

        // Make performFilteredSearch available globally for modal callbacks
        window.performFilteredSearch = performFilteredSearch;

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
            
            // Restore category selections
            const categoriesParam = urlParams.get('categories');
            if (categoriesParam) {
                const categoryIds = categoriesParam.split(',');
                
                // Check the appropriate checkboxes when they're rendered and sync selectedCategoriesForSearch
                setTimeout(() => {
                    selectedCategoriesForSearch.clear(); // Clear first to avoid duplicates
                    categoryIds.forEach(categoryId => {
                        selectedCategoriesForSearch.add(categoryId);
                        const checkbox = document.getElementById(`category_${categoryId}`);
                        if (checkbox) checkbox.checked = true;
                    });
                    updateCategoryDisplay();
                }, 100);
            }
        }

        function updateBulkDeleteVisibility() {
            // With server-side filtering, all rows on current page are visible
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const currentRowCheckboxes = document.querySelectorAll('.row-checkbox');
            const anyChecked = Array.from(currentRowCheckboxes).some(cb => cb.checked);
            
            if (bulkDeleteBtn) {
                bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
            }
        }

        // Note: selectAll and initial rowCheckboxes event listeners are now handled in initializeRowCheckboxes()
        // This ensures they work properly after AJAX content updates

        // Bulk delete functionality
        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                const topicIds = Array.from(checkedBoxes).map(cb => cb.dataset.topicId);
                
                if (topicIds.length === 0) {
                    alert('Please select topics to delete.');
                    return;
                }
                
                const confirmMessage = `Are you sure you want to delete ${topicIds.length} topic(s)? This action cannot be undone.`;
                if (!confirm(confirmMessage)) {
                    return;
                }
                
                // Perform bulk delete
                fetch('/topic/bulk-delete', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ topic_ids: topicIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Refresh the current filtered view instead of full page reload
                        performFilteredSearch();
                    } else {
                        alert('Error deleting topics: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting topics');
                });
            });
        }

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

        // Edit topic form submission
        document.getElementById('editTopicForm').addEventListener('submit', function(e) {
            e.preventDefault();
            updateTopic();
        });

        // Add topic button event listener
        document.getElementById('add-topic-btn').addEventListener('click', function() {
            document.getElementById('addTopicModal').classList.remove('hidden');
        });

        // Add topic form submission
        document.getElementById('addTopicForm').addEventListener('submit', function(e) {
            e.preventDefault();
            addTopic();
        });

        // Export to Excel functionality
        const exportExcelBtn = document.getElementById('export-excel-btn');
        exportExcelBtn.addEventListener('click', function() {
            // Get current filter values from the search state
            const searchTerm = searchInput.value.trim();
            const selectedCategoryIds = Array.from(selectedCategoriesForSearch);
            
            // Build query parameters
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (selectedCategoryIds.length > 0) params.append('categories', selectedCategoryIds.join(','));
            
            // Create download URL using Laravel route
            const exportUrl = `{{ route('topic.exportExcel') }}?${params.toString()}`;
            
            // Show loading state
            const originalText = exportExcelBtn.innerHTML;
            exportExcelBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
            exportExcelBtn.disabled = true;
            
            // Create temporary link and trigger download
            const link = document.createElement('a');
            link.href = exportUrl;
            link.target = '_blank'; // Open in new tab to handle potential errors
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Reset button state after a delay
            setTimeout(() => {
                exportExcelBtn.innerHTML = originalText;
                exportExcelBtn.disabled = false;
            }, 2000);
        });
    });

    let currentTopicId = null;

    // Topic edit and delete functions
    function editTopic(topicId, topicName) {
        currentTopicId = topicId;
        
        // Populate form fields
        document.getElementById('edit_topic_name').value = topicName;
        
        // Show modal
        document.getElementById('editTopicModal').classList.remove('hidden');
    }

    function closeEditTopicModal() {
        document.getElementById('editTopicModal').classList.add('hidden');
        currentTopicId = null;
    }

    function closeAddTopicModal() {
        document.getElementById('addTopicModal').classList.add('hidden');
        // Clear the form
        document.getElementById('addTopicForm').reset();
    }

    function addTopic() {
        const formData = new FormData(document.getElementById('addTopicForm'));
        const data = Object.fromEntries(formData);

        // Basic validation
        if (!data.TopicName || data.TopicName.trim() === '') {
            alert('Please enter a topic name.');
            return;
        }

        fetch('/topic', {
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
                        throw new Error('This topic name already exists in the database. Please choose a different name.');
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
                closeAddTopicModal();
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

    function updateTopic() {
        if (!currentTopicId) return;

        const formData = new FormData(document.getElementById('editTopicForm'));
        const data = Object.fromEntries(formData);

        fetch(`/topic/${currentTopicId}`, {
            method: 'PUT',
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
                        throw new Error('This topic name already exists in the database. Please choose a different name.');
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
                closeEditTopicModal();
                // Refresh the current filtered view instead of full page reload
                performFilteredSearch();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message);
        });
    }

    function deleteTopic(topicId) {
        if (!confirm('Are you sure you want to delete this topic? This action cannot be undone.')) {
            return;
        }

        fetch(`/topic/${topicId}`, {
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
            alert('An error occurred while deleting the topic.');
        });
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const editTopicModal = document.getElementById('editTopicModal');
        const addTopicModal = document.getElementById('addTopicModal');
        
        if (event.target === editTopicModal) {
            closeEditTopicModal();
        }
        
        if (event.target === addTopicModal) {
            closeAddTopicModal();
        }
    });
</script>
