@extends('layout.appMain')

@section('content')
<!-- CSRF token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Custom styles for checkboxes -->
<style>
.row-checkbox:checked,
#checkbox-all:checked,
.event-filter-checkbox:checked,
.category-filter-checkbox:checked,
.topic-filter-checkbox:checked {
    background-color: #7c3aed !important; /* violet-600 */
    border-color: #7c3aed !important;
}

.row-checkbox:checked:after,
#checkbox-all:checked:after,
.event-filter-checkbox:checked:after,
.category-filter-checkbox:checked:after,
.topic-filter-checkbox:checked:after {
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
.event-filter-checkbox,
.category-filter-checkbox,
.topic-filter-checkbox {
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
                Assessment Results
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
                                Assessment Results
                            </a>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="col-span-12 xl:col-span-6">
        <div class="card dark:bg-zinc-800 dark:border-zinc-600">
            <!-- Header with search and filters -->
            <div class="card-body border-b border-gray-100 dark:border-zinc-600 flex items-center justify-between">
                <!-- Search Bar and Filters -->
                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Main Search Input -->
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search by name, email, or phone..." 
                            class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm w-60 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white dark:placeholder-gray-400">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                    </div>

                    <!-- Event Filter -->
                    <div class="relative">
                        <button type="button" id="eventFilterBtn" 
                            class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm bg-white dark:bg-zinc-700 dark:border-zinc-600 dark:text-white hover:bg-gray-50 dark:hover:bg-zinc-600 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-gray-500 text-sm"></i>
                            <span id="eventFilterText">Events</span>
                            <span id="eventCount" class="hidden bg-violet-500 text-white text-xs px-2 py-0.5 rounded-full">0</span>
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </button>
                        
                        <!-- Event Dropdown -->
                        <div id="eventDropdownFilter" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto">
                            <div class="p-3">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Events</span>
                                    <button type="button" id="clearEvents" class="text-xs text-violet-600 hover:text-violet-800">Clear All</button>
                                </div>
                                <input type="text" id="eventSearchFilter" placeholder="Search events..." 
                                    class="w-full mb-3 px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-600 dark:text-white">
                                <div id="eventListFilter" class="space-y-2">
                                    <!-- Events will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="relative">
                        <button type="button" id="categoryFilterBtn" 
                            class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm bg-white dark:bg-zinc-700 dark:border-zinc-600 dark:text-white hover:bg-gray-50 dark:hover:bg-zinc-600 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 flex items-center gap-2">
                            <i class="fas fa-folder text-gray-500 text-sm"></i>
                            <span id="categoryFilterText">Categories</span>
                            <span id="categoryCount" class="hidden bg-violet-500 text-white text-xs px-2 py-0.5 rounded-full">0</span>
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

                    <!-- Topic Filter -->
                    <div class="relative">
                        <button type="button" id="topicFilterBtn" 
                            class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm bg-white dark:bg-zinc-700 dark:border-zinc-600 dark:text-white hover:bg-gray-50 dark:hover:bg-zinc-600 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 flex items-center gap-2">
                            <i class="fas fa-tags text-gray-500 text-sm"></i>
                            <span id="topicFilterText">Topics</span>
                            <span id="topicCount" class="hidden bg-violet-500 text-white text-xs px-2 py-0.5 rounded-full">0</span>
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </button>
                        
                        <!-- Topic Dropdown -->
                        <div id="topicDropdownFilter" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto">
                            <div class="p-3">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Topics</span>
                                    <button type="button" id="clearTopics" class="text-xs text-violet-600 hover:text-violet-800">Clear All</button>
                                </div>
                                <input type="text" id="topicSearchFilter" placeholder="Search topics..." 
                                    class="w-full mb-3 px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-600 dark:text-white">
                                <div id="topicListFilter" class="space-y-2">
                                    <!-- Topics will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date Filter -->
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <input type="date" id="dateFromFilter" 
                                class="px-2.5 py-2 border border-gray-300 rounded-lg text-sm bg-white dark:bg-zinc-700 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        </div>
                        <span class="text-gray-500 text-sm">to</span>
                        <div class="relative">
                            <input type="date" id="dateToFilter" 
                                class="px-2.5 py-2 border border-gray-300 rounded-lg text-sm bg-white dark:bg-zinc-700 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        </div>
                    </div>

                    <!-- Search Button -->
                    <button type="button" id="performSearchBtn" 
                        class="px-3.5 py-2 bg-violet-500 text-white rounded-lg text-sm hover:bg-violet-600 focus:ring-2 focus:ring-violet-500 focus:ring-offset-1 flex items-center gap-2">
                        <i class="fas fa-search text-sm"></i>
                        Search
                    </button>

                    <!-- Clear All Button -->
                    <button type="button" id="clearAllFilters" class="hidden px-3 py-2 text-gray-600 hover:text-gray-800 text-sm dark:text-gray-400 dark:hover:text-gray-200 border border-gray-300 dark:border-zinc-600 rounded-lg">
                        <i class="fas fa-times text-sm"></i>
                        Clear All
                    </button>

                    <!-- Delete Button - inline with search elements -->
                    <button id="bulk-delete-btn" type="button"
                        class="hidden px-3.5 py-2 text-white bg-red-500 rounded-lg hover:bg-red-600 text-sm flex items-center gap-1.5">
                        <i class="fas fa-trash text-sm"></i>
                        Delete
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="isolate">
                    <div class="relative rounded-lg" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                        <table class="w-full min-w-[1100px] text-sm text-center text-gray-500">
                            <thead
                                class="text-xs text-gray-700 uppercase dark:text-gray-100 bg-gray-50 dark:bg-zinc-700 sticky top-0 z-40 shadow-sm">
                                <tr>
                                <th class="p-3">
                                    <div class="flex items-center justify-center">
                                        <input id="checkbox-all" type="checkbox"
                                            class="w-4 h-4 border-gray-300 rounded bg-white">
                                        <label for="checkbox-all" class="sr-only">checkbox</label>
                                    </div>
                                </th>
                                <th class="px-3 py-2">Name</th>
                                <th class="px-3 py-2">Phone Number</th>
                                <th class="px-3 py-2">Email</th>
                                <th class="px-3 py-2">Score</th>
                                <th class="px-3 py-2">Date Answered</th>
                                <th class="px-3 py-2">Actions</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $row)
                                <tr data-id="{{ $row->AssessmentID }}"
                                    data-participant-name="{{ $row->participant->name ?? '' }}"
                                    data-participant-phone="{{ $row->participant->phone_number ?? '' }}"
                                    data-participant-email="{{ $row->participant->email ?? '' }}"
                                    data-event-id="{{ $row->EventID ?? '' }}"
                                    data-date-answered="{{ $row->DateCreate }}"
                                    class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:border-zinc-600">
                                    <td class="w-4 p-3">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox"
                                                class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">{{ $row->participant->name ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $row->participant->phone_number ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $row->participant->email ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $row->TotalScore }} / {{ $row->TotalQuestion }}</td>
                                    <td class="px-3 py-2">{{ \Carbon\Carbon::parse($row->DateCreate)->format('d M Y') }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <button type="button"
                                            class="view-details px-4 py-1 text-sm bg-blue-500 text-blue-600 rounded hover:underline"
                                            data-id="{{ $row->AssessmentID }}">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-2 text-center">No records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
        </div>

        @if (method_exists($records, 'links'))
    <div class="mt-4 flex">
        {{ $records->links('pagination::tailwind') }}
    </div>
@endif
</div>

<!-- Modal -->

<div id="details-modal"
    class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 transition">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-2xl mx-2 transform scale-100 transition-all overflow-hidden border border-gray-200 dark:border-zinc-700 flex flex-col" style="max-height:90vh;">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700 flex justify-between items-center bg-gray-50 dark:bg-zinc-800">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">
                <i class="fas fa-file-alt mr-2 text-blue-500"></i> Assessment Details
            </h2>
            <button id="close-modal"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white text-2xl font-bold">
                &times;
            </button>
        </div>
        <!-- Body -->
        <div class="p-6 overflow-y-auto flex-1 space-y-4 bg-white dark:bg-zinc-900" id="modal-content" style="min-height:100px; max-height:60vh;">
            <div class="text-center text-gray-600 dark:text-gray-300">Loading...</div>
        </div>
        <!-- Footer -->
        <div class="px-6 py-3 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-right">
            <button id="close-modal-footer"
                class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-base font-semibold shadow">
                Close
            </button>
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
        const eventFilterBtn = document.getElementById('eventFilterBtn');
        const eventDropdownFilter = document.getElementById('eventDropdownFilter');
        const eventListFilter = document.getElementById('eventListFilter');
        const eventSearchFilter = document.getElementById('eventSearchFilter');
        const eventCount = document.getElementById('eventCount');
        const clearEvents = document.getElementById('clearEvents');
        
        const categoryFilterBtn = document.getElementById('categoryFilterBtn');
        const categoryDropdownFilter = document.getElementById('categoryDropdownFilter');
        const categoryListFilter = document.getElementById('categoryListFilter');
        const categorySearchFilter = document.getElementById('categorySearchFilter');
        const categoryCount = document.getElementById('categoryCount');
        const clearCategories = document.getElementById('clearCategories');
        
        const topicFilterBtn = document.getElementById('topicFilterBtn');
        const topicDropdownFilter = document.getElementById('topicDropdownFilter');
        const topicListFilter = document.getElementById('topicListFilter');
        const topicSearchFilter = document.getElementById('topicSearchFilter');
        const topicCount = document.getElementById('topicCount');
        const clearTopics = document.getElementById('clearTopics');
        
        const dateFromFilter = document.getElementById('dateFromFilter');
        const dateToFilter = document.getElementById('dateToFilter');
        const performSearchBtn = document.getElementById('performSearchBtn');
        const clearAllFilters = document.getElementById('clearAllFilters');
        
        // Search state
        let allEvents = [];
        let allCategories = [];
        let allTopics = [];
        let selectedEventsForSearch = new Set();
        let selectedCategoriesForSearch = new Set();
        let selectedTopicsForSearch = new Set();
        let searchTimeout = null;

        // Debounced search function
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performFilteredSearch();
            }, 300);
        }

        // Load filter data
        function loadFiltersData() {
            console.log('Loading filter data for results...');
            
            try {
                // Load events
                const events = @json($allEvents ?? []);
                allEvents = events;
                renderEventList();
                
                // Load categories
                const categories = @json($allCategories ?? []);
                allCategories = categories;
                renderCategoryList();
                
                // Load topics
                const topics = @json($allTopics ?? []);
                allTopics = topics;
                renderTopicList();
                
                console.log('Filter data loaded successfully!');
            } catch (error) {
                console.error('Error loading filter data:', error);
            }
        }

        // Render event list
        function renderEventList() {
            const searchTerm = eventSearchFilter.value.toLowerCase();
            const filteredEvents = allEvents.filter(event => 
                event.EventName.toLowerCase().includes(searchTerm) || 
                event.EventID.toString().includes(searchTerm)
            );

            eventListFilter.innerHTML = '';
            filteredEvents.forEach(event => {
                const div = document.createElement('div');
                div.className = 'flex items-center';
                div.innerHTML = `
                    <input type="checkbox" id="event_${event.EventID}" 
                           value="${event.EventID}" 
                           data-name="${event.EventName}"
                           class="event-filter-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                    <label for="event_${event.EventID}" class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        ${event.EventName}
                    </label>
                `;
                eventListFilter.appendChild(div);
            });

            // Restore selected state
            selectedEventsForSearch.forEach(eventId => {
                const checkbox = document.getElementById(`event_${eventId}`);
                if (checkbox) checkbox.checked = true;
            });

            // Add event listeners
            document.querySelectorAll('.event-filter-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', handleEventChange);
            });
        }

        // Render category list
        function renderCategoryList() {
            const searchTerm = categorySearchFilter.value.toLowerCase();
            const filteredCategories = allCategories.filter(category => 
                category.CategoryName.toLowerCase().includes(searchTerm) || 
                category.CategoryID.toString().includes(searchTerm)
            );

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
        }

        // Render topic list
        function renderTopicList() {
            const searchTerm = topicSearchFilter.value.toLowerCase();
            const filteredTopics = allTopics.filter(topic => 
                topic.TopicName.toLowerCase().includes(searchTerm) || 
                topic.TopicID.toString().includes(searchTerm)
            );

            topicListFilter.innerHTML = '';
            filteredTopics.forEach(topic => {
                const div = document.createElement('div');
                div.className = 'flex items-center';
                div.innerHTML = `
                    <input type="checkbox" id="topic_${topic.TopicID}" 
                           value="${topic.TopicID}" 
                           data-name="${topic.TopicName}"
                           class="topic-filter-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                    <label for="topic_${topic.TopicID}" class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        ${topic.TopicName}
                    </label>
                `;
                topicListFilter.appendChild(div);
            });

            // Restore selected state
            selectedTopicsForSearch.forEach(topicId => {
                const checkbox = document.getElementById(`topic_${topicId}`);
                if (checkbox) checkbox.checked = true;
            });

            // Add event listeners
            document.querySelectorAll('.topic-filter-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', handleTopicChange);
            });
        }

        // Handle filter changes
        function handleEventChange(event) {
            const eventId = event.target.value;
            if (event.target.checked) {
                selectedEventsForSearch.add(eventId);
            } else {
                selectedEventsForSearch.delete(eventId);
            }
            updateEventDisplay();
        }

        function handleCategoryChange(event) {
            const categoryId = event.target.value;
            if (event.target.checked) {
                selectedCategoriesForSearch.add(categoryId);
            } else {
                selectedCategoriesForSearch.delete(categoryId);
            }
            updateCategoryDisplay();
        }

        function handleTopicChange(event) {
            const topicId = event.target.value;
            if (event.target.checked) {
                selectedTopicsForSearch.add(topicId);
            } else {
                selectedTopicsForSearch.delete(topicId);
            }
            updateTopicDisplay();
        }

        // Update display counters
        function updateEventDisplay() {
            const count = selectedEventsForSearch.size;
            if (count > 0) {
                eventCount.textContent = count;
                eventCount.classList.remove('hidden');
            } else {
                eventCount.classList.add('hidden');
            }
            updateClearAllButton();
        }

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

        function updateTopicDisplay() {
            const count = selectedTopicsForSearch.size;
            if (count > 0) {
                topicCount.textContent = count;
                topicCount.classList.remove('hidden');
            } else {
                topicCount.classList.add('hidden');
            }
            updateClearAllButton();
        }

        // Update clear all button visibility
        function updateClearAllButton() {
            const hasFilters = selectedEventsForSearch.size > 0 || 
                             selectedCategoriesForSearch.size > 0 || 
                             selectedTopicsForSearch.size > 0 || 
                             searchInput.value.trim() ||
                             dateFromFilter.value ||
                             dateToFilter.value;
            clearAllFilters.classList.toggle('hidden', !hasFilters);
        }

        // Perform filtered search
        function performFilteredSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const dateFrom = dateFromFilter.value;
            const dateTo = dateToFilter.value;
            let visibleRows = 0;

            tableRows.forEach(row => {
                if (row.children.length === 1 && row.children[0].getAttribute('colspan')) {
                    return; // Skip "No records found" row
                }

                const participantName = row.dataset.participantName?.toLowerCase() || '';
                const participantPhone = row.dataset.participantPhone?.toLowerCase() || '';
                const participantEmail = row.dataset.participantEmail?.toLowerCase() || '';
                const eventId = row.dataset.eventId || '';
                const dateAnswered = row.dataset.dateAnswered || '';

                // Check text search (name, phone, email)
                const textMatch = !searchTerm || 
                    participantName.includes(searchTerm) ||
                    participantPhone.includes(searchTerm) ||
                    participantEmail.includes(searchTerm);

                // Check event filter
                const eventMatch = selectedEventsForSearch.size === 0 || selectedEventsForSearch.has(eventId);

                // Check date filter
                let dateMatch = true;
                if (dateFrom || dateTo) {
                    const answerDate = new Date(dateAnswered).toISOString().split('T')[0];
                    if (dateFrom && answerDate < dateFrom) dateMatch = false;
                    if (dateTo && answerDate > dateTo) dateMatch = false;
                }

                // Show row if all conditions match
                if (textMatch && eventMatch && dateMatch) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                    // Uncheck hidden rows
                    const checkbox = row.querySelector('.row-checkbox');
                    if (checkbox) checkbox.checked = false;
                }
            });

            // Update UI
            updateSelectAllState();  
            updateBulkDeleteVisibility();
            showNoResultsMessage(visibleRows === 0 && (searchTerm || selectedEventsForSearch.size > 0 || dateFrom || dateTo));

            console.log(`Search results: ${visibleRows} results found`);
        }

        // Clear all filters
        function clearAllFiltersAction() {
            // Clear text search
            searchInput.value = '';
            
            // Clear event selections  
            selectedEventsForSearch.clear();
            document.querySelectorAll('.event-filter-checkbox').forEach(cb => cb.checked = false);
            updateEventDisplay();
            
            // Clear category selections  
            selectedCategoriesForSearch.clear();
            document.querySelectorAll('.category-filter-checkbox').forEach(cb => cb.checked = false);
            updateCategoryDisplay();
            
            // Clear topic selections  
            selectedTopicsForSearch.clear();
            document.querySelectorAll('.topic-filter-checkbox').forEach(cb => cb.checked = false);
            updateTopicDisplay();
            
            // Clear date filters
            dateFromFilter.value = '';
            dateToFilter.value = '';
            
            // Close dropdowns
            eventDropdownFilter.classList.add('hidden');
            categoryDropdownFilter.classList.add('hidden');
            topicDropdownFilter.classList.add('hidden');
            
            // Perform search to show all results
            performFilteredSearch();
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
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No results found</p>
                                <p class="text-sm">Try adjusting your search filters</p>
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
            const visibleCheckboxes = Array.from(rowCheckboxes).filter(cb => 
                cb.closest('tr').style.display !== 'none'
            );
            const checkedVisible = visibleCheckboxes.filter(cb => cb.checked);
            
            if (visibleCheckboxes.length === 0) {
                selectAll.indeterminate = false;
                selectAll.checked = false;
            } else if (checkedVisible.length === visibleCheckboxes.length) {
                selectAll.indeterminate = false;
                selectAll.checked = true;
            } else if (checkedVisible.length > 0) {
                selectAll.indeterminate = true;
                selectAll.checked = false;
            } else {
                selectAll.indeterminate = false;
                selectAll.checked = false;
            }
        }

        // Event listeners for dropdowns
        eventFilterBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            eventDropdownFilter.classList.toggle('hidden');
        });

        categoryFilterBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            categoryDropdownFilter.classList.toggle('hidden');
        });

        topicFilterBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            topicDropdownFilter.classList.toggle('hidden');
        });

        // Clear buttons
        clearEvents.addEventListener('click', () => {
            selectedEventsForSearch.clear();
            document.querySelectorAll('.event-filter-checkbox').forEach(cb => cb.checked = false);
            updateEventDisplay();
        });

        clearCategories.addEventListener('click', () => {
            selectedCategoriesForSearch.clear();
            document.querySelectorAll('.category-filter-checkbox').forEach(cb => cb.checked = false);
            updateCategoryDisplay();
        });

        clearTopics.addEventListener('click', () => {
            selectedTopicsForSearch.clear();
            document.querySelectorAll('.topic-filter-checkbox').forEach(cb => cb.checked = false);
            updateTopicDisplay();
        });

        // Search functionality
        eventSearchFilter.addEventListener('input', renderEventList);
        categorySearchFilter.addEventListener('input', renderCategoryList);
        topicSearchFilter.addEventListener('input', renderTopicList);
        performSearchBtn.addEventListener('click', performFilteredSearch);
        clearAllFilters.addEventListener('click', clearAllFiltersAction);
        
        // Real-time search and date filters
        searchInput.addEventListener('input', () => {
            updateClearAllButton();
            debounceSearch();
        });
        
        dateFromFilter.addEventListener('change', () => {
            updateClearAllButton();
            performFilteredSearch();
        });
        
        dateToFilter.addEventListener('change', () => {
            updateClearAllButton();
            performFilteredSearch();
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!eventFilterBtn.contains(e.target) && !eventDropdownFilter.contains(e.target)) {
                eventDropdownFilter.classList.add('hidden');
            }
            if (!categoryFilterBtn.contains(e.target) && !categoryDropdownFilter.contains(e.target)) {
                categoryDropdownFilter.classList.add('hidden');
            }
            if (!topicFilterBtn.contains(e.target) && !topicDropdownFilter.contains(e.target)) {
                topicDropdownFilter.classList.add('hidden');
            }
        });

        // Initialize filters
        loadFiltersData();

        function updateBulkDeleteVisibility() {
            const visibleCheckboxes = Array.from(rowCheckboxes).filter(cb => 
                cb.closest('tr').style.display !== 'none'
            );
            const anyChecked = visibleCheckboxes.some(cb => cb.checked);
            bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
        }

        if (selectAll) {
            selectAll.addEventListener('change', () => {
                const visibleCheckboxes = Array.from(rowCheckboxes).filter(cb => 
                    cb.closest('tr').style.display !== 'none'
                );
                visibleCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                updateBulkDeleteVisibility();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                updateSelectAllState();
                updateBulkDeleteVisibility();
            });
        });

        bulkDeleteBtn.addEventListener('click', () => {
            const selectedIds = Array.from(rowCheckboxes)
                .map(cb => cb.checked ? cb.closest('tr').dataset.id : null)
                .filter(id => id !== null);

            if (selectedIds.length === 0) return;
            if (!confirm(`Delete ${selectedIds.length} records?`)) return;

            fetch('{{ route('assessment.bulkDelete') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ids: selectedIds
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert('Failed to delete records.');
                    }
                })
                .catch(err => alert('Error deleting records.'));
        });
    });

    
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('details-modal');
    const modalContent = document.getElementById('modal-content');
    const closeModal = document.getElementById('close-modal');
    const closeModalFooter = document.getElementById('close-modal-footer'); // optional if footer button exists

    // Function to open modal and load details
    function openModal(id) {
        modal.classList.remove('hidden');
        modalContent.innerHTML = 'Loading...';

        fetch(`/assessment/${id}/details`)
            .then(res => res.json())
            .then(data => {
    if (data.status === 'success') {
        let html = '';


        data.results.forEach(item => {
            const isCorrect = item.participantAnswer === item.correctAnswer;
            const correctClass = isCorrect
                ? 'bg-green-50 border-green-400 text-green-700'
                : 'bg-red-50 border-red-400 text-red-700';

            html += `
                <div class="border rounded-lg p-4 mb-4 shadow-sm bg-gray-50 dark:bg-zinc-800">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 text-xs md:text-sm">
                        <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 rounded mr-2 text-xs">Q</span>
                        ${item.question}
                    </h3>
                    <div class="space-y-2">
                        <div class="p-2 border rounded ${correctClass} text-xs md:text-sm">
                            <span class="font-semibold">Your Answer:</span>
                            ${item.participantAnswer || '-'}
                        </div>
                        ${!isCorrect ? `
                        <div class="p-2 border rounded bg-green-50 border-green-400 text-green-700 text-xs md:text-sm">
                            <span class="font-semibold">Correct Answer:</span>
                            ${item.correctAnswer || '-'}
                        </div>` : ''}
                    </div>
                </div>
            `;
        });

        if (!data.results.length) {
            html = '<div class="text-center text-gray-600 dark:text-gray-300 text-sm">No details found.</div>';
        }

        document.getElementById('modal-content').innerHTML = html;
    } else {
        document.getElementById('modal-content').innerHTML = '<div class="text-sm text-gray-600">Failed to load details.</div>';
    }
})
            .catch(() => {
                modalContent.innerHTML = 'Error fetching data.';
            });
    }

    // Attach click to all "View Details" buttons
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', () => {
            openModal(btn.dataset.id);
        });
    });

    // Close modal on clicking close buttons
    closeModal?.addEventListener('click', () => modal.classList.add('hidden'));
    closeModalFooter?.addEventListener('click', () => modal.classList.add('hidden'));

});
</script>
