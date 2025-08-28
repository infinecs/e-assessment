<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<!-- jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('table').DataTable({
        searching: false,
        lengthChange: false,
        paging: false,
        columnDefs: [
            { orderable: false, targets: 0 }, // Disable sorting for the first column (checkbox)
            { orderable: false, targets: -1 } // Disable sorting for the Actions column (last column)
            // Score and Percentage columns remain sortable by default
        ]
    });
});
</script>
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

/* Modal shadow overlay effect */
.modal-shadow::before {
    content: '';
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 49;
    pointer-events: none;
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
            <div class="card-body border-b border-gray-100 dark:border-zinc-600">
                <!-- Search Bar and Filters Row -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3 flex-wrap">
                        <!-- Main Search Input -->
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search by name or email..."
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm w-64 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white dark:placeholder-gray-400">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-sm"></i>
                            </div>
                        </div>

                        <!-- Event Filter -->
                        <div class="relative">
                            <button type="button" id="eventFilterBtn" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white dark:bg-zinc-700 dark:border-zinc-600 dark:text-white hover:bg-gray-50 dark:hover:bg-zinc-600 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-gray-500"></i>
                                <span id="eventFilterText">Assessments</span>
                                <span id="eventCount" class="hidden bg-violet-500 text-white text-xs px-2 py-1 rounded-full">0</span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </button>
                            
                            <!-- Event Dropdown -->
                            <div id="eventDropdownFilter" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto">
                                <div class="p-3">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Assessments</span>
                                        <button type="button" id="clearEvents" class="text-xs text-violet-600 hover:text-violet-800">Clear All</button>
                                    </div>
                                    <input type="text" id="eventSearchFilter" placeholder="Search assessments..." 
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

                        <!-- Topic Filter -->
                        <div class="relative">
                            <button type="button" id="topicFilterBtn" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white dark:bg-zinc-700 dark:border-zinc-600 dark:text-white hover:bg-gray-50 dark:hover:bg-zinc-600 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 flex items-center gap-2">
                                <i class="fas fa-tags text-gray-500"></i>
                                <span id="topicFilterText">Topics</span>
                                <span id="topicCount" class="hidden bg-violet-500 text-white text-xs px-2 py-1 rounded-full">0</span>
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
                        <div class="relative">
                            <input type="date" id="dateAnsweredFilter" placeholder="Date Answered"
                                class="px-2 py-2 border border-gray-300 rounded-lg text-sm bg-white dark:bg-zinc-700 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
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

                    
                </div>

                <!-- Export and Bulk Delete Buttons Row -->
                <div class="flex justify-between items-center w-full mt-2">
                    <div>
                        <button id="export-excel-btn" type="button"
                            class="px-6 py-2 text-white bg-green-500 rounded hover:bg-green-600 text-sm">
                            <i class="fas fa-file-csv"></i>
                            Export to CSV
                        </button>
                    </div>
                    <div>
                        <button id="bulk-delete-btn" type="button"
                            class="hidden px-6 py-2 text-white bg-red-500 rounded hover:bg-red-600 text-sm">
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="isolate">
                    <div class="relative rounded-lg" style="max-height: 500px; min-height: 350px; overflow-y: auto; overflow-x: auto; display: flex; flex-direction: column; justify-content: flex-start;">
                        <table class="w-full min-w-[1100px] text-sm text-center text-gray-500">
                            <thead
                                class="text-xs text-gray-700 uppercase dark:text-gray-100 bg-gray-50 dark:bg-zinc-700 sticky top-0 z-40 shadow-sm">
                                <tr>
                                    <th class="w-4 p-3 text-center">
                                        <div class="flex items-center justify-center">
                                            <input id="checkbox-all" type="checkbox" class="w-4 h-4 border-gray-300 rounded bg-white">
                                            <label for="checkbox-all" class="sr-only">checkbox</label>
                                        </div>
                                    </th>
                                    <th class="px-3 py-2 text-center">Name</th>
                                    
                                    <th class="px-3 py-2 text-center">Email</th>
                                    <th class="px-3 py-2 text-center">Event Name</th>
                                    <th class="px-3 py-2 text-center"><span style="margin-right:10px;display:inline-block;">Score</span></th>
                                    <th class="px-3 py-2 text-center"><span style="margin-right:10px;display:inline-block;">Percentage</span></th>
                                    <th class="px-3 py-2 text-center"><span style="margin-right:10px;display:inline-block;">Date Answered</span></th>
                                    <th class="px-3 py-2 text-center">Actions</th>
                                </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $row)
                                <tr data-id="{{ $row->AssessmentID }}"
                                    data-participant-name="{{ $row->participant->name ?? '' }}"
                                    
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

                                    <td class="px-3 py-2">{{ $row->participant->email ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $row->event->EventName ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $row->TotalScore }} / {{ $row->TotalQuestion }}</td>
                                    <td class="px-3 py-2">
                                        @if($row->TotalQuestion > 0)
                                            {{ number_format(($row->TotalScore / $row->TotalQuestion) * 100, 2) }}%
                                        @else
                                            -
                                        @endif
                                    </td>
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
                                    <td colspan="9" class="px-3 py-2 text-center">No records found</td>
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
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-2xl mx-2 transform scale-100 transition-all overflow-hidden border border-gray-200 dark:border-zinc-700 flex flex-col" style="max-height:96vh; min-height:60vh;">
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
        <div class="p-6 overflow-y-auto flex-1 space-y-4 bg-white dark:bg-zinc-900" id="modal-content" style="min-height:95px;">
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
        // Export to Excel route from Blade
        const exportExcelRoute = "{{ route('assessment.exportExcel') }}";

        function handleExportExcel() {
            const exportExcelBtn = document.getElementById('export-excel-btn');
            // Get current filter values from the search state
            const searchTerm = searchInput.value.trim();
            const dateAnswered = dateAnsweredFilter.value;
            const selectedEvents = Array.from(selectedEventsForSearch);
            const selectedCategories = Array.from(selectedCategoriesForSearch);
            const selectedTopics = Array.from(selectedTopicsForSearch);
            // Build query parameters
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (dateAnswered) params.append('date_answered', dateAnswered);
            if (selectedEvents.length > 0) params.append('events', selectedEvents.join(','));
            if (selectedCategories.length > 0) params.append('categories', selectedCategories.join(','));
            if (selectedTopics.length > 0) params.append('topics', selectedTopics.join(','));
            // Create download URL using Laravel route
            const exportUrl = `${exportExcelRoute}?${params.toString()}`;
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
        }

        // Attach export button event listener (initial and after AJAX)
        function initializeExportButton() {
            const exportExcelBtn = document.getElementById('export-excel-btn');
            if (exportExcelBtn) {
                exportExcelBtn.removeEventListener('click', handleExportExcel);
                exportExcelBtn.addEventListener('click', handleExportExcel);
            }
        }

        // Call on initial load
        initializeExportButton();
        const selectAll = document.getElementById('checkbox-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        // Bulk delete button initialization
        function initializeBulkDeleteButton() {
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            if (!bulkDeleteBtn) return;
            // Remove previous listeners by cloning
            const newBtn = bulkDeleteBtn.cloneNode(true);
            bulkDeleteBtn.parentNode.replaceChild(newBtn, bulkDeleteBtn);
            newBtn.addEventListener('click', () => {
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(cb => {
                        let tr = cb.closest('tr');
                        return tr && tr.dataset.id ? tr.dataset.id : null;
                    })
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
                .then(async res => {
                    let data;
                    try {
                        data = await res.json();
                    } catch (e) {
                        throw new Error('Invalid JSON response');
                    }
                    if (data.status === 'success') {
                        performFilteredSearch();
                    } else {
                        let msg = 'Failed to delete records.';
                        if (data.message) msg += '\n' + data.message;
                        if (data.debug) msg += '\nDebug: ' + JSON.stringify(data.debug);
                        alert(msg);
                    }
                })
                .catch(err => {
                    alert('Error deleting records. ' + (err && err.message ? err.message : ''));
                });
            });
        }
        
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
        
        const dateAnsweredFilter = document.getElementById('dateAnsweredFilter');
        const performSearchBtn = document.getElementById('performSearchBtn');
        const clearAllFilters = document.getElementById('clearAllFilters');
        
        // Search state
        let allEvents = [];
        let allCategories = [];
        let allTopics = [];
        let categoryTopicsMap = new Map(); // Maps categoryId to array of topic objects
        let selectedEventsForSearch = new Set();
        let selectedCategoriesForSearch = new Set();
        let selectedTopicsForSearch = new Set();
        let searchTimeout = null;

        // Debounced search function
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performFilteredSearch();
            }, 800); // Increased delay for server requests
        }

        // Load filter data
        async function loadFiltersData() {
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
                
                // Load category-topic relationships
                for (const category of allCategories) {
                    try {
                        console.log(`Loading topics for category ${category.CategoryID} (${category.CategoryName})`);
                        const response = await fetch(`/category/${category.CategoryID}/topics`);
                        const data = await response.json();
                        
                        console.log(`Response for category ${category.CategoryID}:`, data);
                        
                        if (data.success && data.topics) {
                            // Store with both string and number keys to handle type mismatches
                            categoryTopicsMap.set(category.CategoryID.toString(), data.topics);
                            categoryTopicsMap.set(category.CategoryID, data.topics);
                            console.log(`Stored ${data.topics.length} topics for category ${category.CategoryID}`);
                        } else {
                            console.log(`No topics found for category ${category.CategoryID}`);
                        }
                    } catch (error) {
                        console.warn(`Failed to load topics for category ${category.CategoryID}:`, error);
                    }
                }
                
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
            console.log('renderTopicList called');
            console.log('selectedCategoriesForSearch:', Array.from(selectedCategoriesForSearch));
            console.log('categoryTopicsMap:', categoryTopicsMap);
            
            const searchTerm = topicSearchFilter.value.toLowerCase();
            
            // Get available topics based on selected categories
            let availableTopics = [];
            if (selectedCategoriesForSearch.size === 0) {
                // If no categories selected, show all topics
                availableTopics = allTopics;
                console.log('No categories selected, showing all topics:', availableTopics.length);
            } else {
                // Only show topics from selected categories
                const topicsSet = new Set();
                selectedCategoriesForSearch.forEach(categoryId => {
                    console.log(`Looking for topics in category ${categoryId}`);
                    // Try both string and number versions of the categoryId
                    let categoryTopics = categoryTopicsMap.get(categoryId) || categoryTopicsMap.get(categoryId.toString()) || categoryTopicsMap.get(parseInt(categoryId));
                    
                    if (categoryTopics) {
                        console.log(`Found ${categoryTopics.length} topics for category ${categoryId}:`, categoryTopics);
                        categoryTopics.forEach(topic => {
                            topicsSet.add(topic);
                        });
                    } else {
                        console.log(`No topics found for category ${categoryId} in categoryTopicsMap`);
                        console.log('Available keys in categoryTopicsMap:', Array.from(categoryTopicsMap.keys()));
                    }
                });
                availableTopics = Array.from(topicsSet);
                console.log('Available topics from selected categories:', availableTopics);
            }
            
            // Filter topics by search term
            const filteredTopics = availableTopics.filter(topic => 
                topic.TopicName.toLowerCase().includes(searchTerm) || 
                topic.TopicID.toString().includes(searchTerm)
            );
            
            console.log('Filtered topics after search:', filteredTopics);

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

            // Restore selected state for topics that are still available
            selectedTopicsForSearch.forEach(topicId => {
                const checkbox = document.getElementById(`topic_${topicId}`);
                if (checkbox) checkbox.checked = true;
            });

            // Remove topics from selected set that are no longer available
            const availableTopicIds = new Set(filteredTopics.map(topic => topic.TopicID.toString()));
            selectedTopicsForSearch.forEach(topicId => {
                if (!availableTopicIds.has(topicId)) {
                    selectedTopicsForSearch.delete(topicId);
                }
            });

            // Add event listeners
            document.querySelectorAll('.topic-filter-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', handleTopicChange);
            });
            
            // Update display after filtering
            updateTopicDisplay();
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
            console.log('Category change:', categoryId, 'checked:', event.target.checked);
            
            if (event.target.checked) {
                selectedCategoriesForSearch.add(categoryId);
            } else {
                selectedCategoriesForSearch.delete(categoryId);
            }
            updateCategoryDisplay();
            // Re-render topics list based on new category selection
            renderTopicList();
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
                             dateAnsweredFilter.value;
            clearAllFilters.classList.toggle('hidden', !hasFilters);
        }

        // Perform filtered search - AJAX VERSION
        function performFilteredSearch() {
            const searchTerm = searchInput.value.trim();
            const dateAnswered = dateAnsweredFilter.value;
            const selectedEvents = Array.from(selectedEventsForSearch);
            const selectedCategories = Array.from(selectedCategoriesForSearch);
            const selectedTopics = Array.from(selectedTopicsForSearch);
            
            // Show loading state
            const tableBody = document.querySelector('tbody');
            const originalContent = tableBody.innerHTML;
            tableBody.innerHTML = '<tr><td colspan="8" class="px-3 py-2 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            
            // Build query parameters for AJAX request
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (dateAnswered) params.append('date_answered', dateAnswered);
            if (selectedEvents.length > 0) params.append('events', selectedEvents.join(','));
            if (selectedCategories.length > 0) params.append('categories', selectedCategories.join(','));
            if (selectedTopics.length > 0) params.append('topics', selectedTopics.join(','));
            
            // Update URL without page reload
            const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
            window.history.pushState({ path: newUrl }, '', newUrl);
            
            // Make AJAX request
            fetch(`/results?${params.toString()}`, {
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
        initializeViewButtons();
        initializeExportButton();
        initializeBulkDeleteButton();
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
            
            // Re-render topics list to show all topics when categories are cleared
            renderTopicList();
            
            // Clear date filter
            dateAnsweredFilter.value = '';
            
            // Close dropdowns
            eventDropdownFilter.classList.add('hidden');
            categoryDropdownFilter.classList.add('hidden');
            topicDropdownFilter.classList.add('hidden');
            
            // Show loading state
            const tableBody = document.querySelector('tbody');
            tableBody.innerHTML = '<tr><td colspan="8" class="px-3 py-2 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            
            // Update URL without page reload
            const newUrl = window.location.pathname;
            window.history.pushState({ path: newUrl }, '', newUrl);
            
            // Make AJAX request to get all results
            fetch('/results', {
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
        initializeViewButtons();
        initializeExportButton();
        initializeBulkDeleteButton();
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

        // Reinitialize view buttons after AJAX content update
        function initializeViewButtons() {
            document.querySelectorAll('.view-details').forEach(btn => {
                // Remove existing event listeners by cloning
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                
                newBtn.addEventListener('click', () => {
                    openModal(newBtn.dataset.id);
                });
            });
        }

        // Show no results message (not needed for server-side filtering)
        function showNoResultsMessage(show) {
            // This function is not needed with server-side filtering
            // The server will handle displaying "No records found" message
        }

        function updateSelectAllState() {
            // With server-side filtering, all rows shown are visible
            const selectAll = document.getElementById('checkbox-all');
            const currentRowCheckboxes = document.querySelectorAll('.row-checkbox');
            const visibleCheckboxes = Array.from(currentRowCheckboxes);
            const checkedVisible = visibleCheckboxes.filter(cb => cb.checked);
            
            if (!selectAll) return; // Safety check
            
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
            // Re-render topics list when categories are cleared
            renderTopicList();
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
        
        // Real-time search and date filter with debouncing
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
        
        dateAnsweredFilter.addEventListener('change', () => {
            updateClearAllButton();
            performFilteredSearch(); // No debounce needed for date changes
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

        // Initialize filters and restore state from URL parameters
        function initializeFilters() {
            loadFiltersData();
            
            // Restore filter states from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            
            // Restore search term
            const searchParam = urlParams.get('search');
            if (searchParam) {
                searchInput.value = searchParam;
                updateClearAllButton();
            }
            
            // Restore date filter
            const dateParam = urlParams.get('date_answered');
            if (dateParam) {
                dateAnsweredFilter.value = dateParam;
                updateClearAllButton();
            }
            
            // Restore event selections
            const eventsParam = urlParams.get('events');
            if (eventsParam) {
                const events = eventsParam.split(',');
                setTimeout(() => {
                    selectedEventsForSearch.clear();
                    events.forEach(eventId => {
                        const checkbox = document.getElementById(`event_${eventId}`);
                        if (checkbox) {
                            checkbox.checked = true;
                            selectedEventsForSearch.add(eventId);
                        }
                    });
                    updateEventDisplay();
                }, 100);
            }
            
            // Restore category selections
            const categoriesParam = urlParams.get('categories');
            if (categoriesParam) {
                const categories = categoriesParam.split(',');
                setTimeout(() => {
                    selectedCategoriesForSearch.clear();
                    categories.forEach(categoryId => {
                        const checkbox = document.getElementById(`category_${categoryId}`);
                        if (checkbox) {
                            checkbox.checked = true;
                            selectedCategoriesForSearch.add(categoryId);
                        }
                    });
                    updateCategoryDisplay();
                    // Re-render topics list after category selection is restored
                    renderTopicList();
                }, 150);
            }
            
            // Restore topic selections
            const topicsParam = urlParams.get('topics');
            if (topicsParam) {
                const topics = topicsParam.split(',');
                setTimeout(() => {
                    selectedTopicsForSearch.clear();
                    topics.forEach(topicId => {
                        const checkbox = document.getElementById(`topic_${topicId}`);
                        if (checkbox) {
                            checkbox.checked = true;
                            selectedTopicsForSearch.add(topicId);
                        }
                    });
                    updateTopicDisplay();
                }, 200);
            }
        }

        // Initialize filters
        initializeFilters();
        initializeRowCheckboxes(); // Initialize checkbox functionality
        initializeViewButtons(); // Initialize view button functionality
        initializeBulkDeleteButton(); // Initialize bulk delete button functionality

        // Make functions available globally for modal callbacks
        window.performFilteredSearch = performFilteredSearch;
        window.openModal = openModal;

        function updateBulkDeleteVisibility() {
            // With server-side filtering, all rows shown are visible
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const currentRowCheckboxes = document.querySelectorAll('.row-checkbox');
            const visibleCheckboxes = Array.from(currentRowCheckboxes);
            const anyChecked = visibleCheckboxes.some(cb => cb.checked);
            
            if (bulkDeleteBtn) {
                bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
            }
        }

        // Note: selectAll and initial rowCheckboxes event listeners are now handled in initializeRowCheckboxes()
        // This ensures they work properly after AJAX content updates

        bulkDeleteBtn.addEventListener('click', () => {
            const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                .map(cb => {
                    // Find the closest tr with a data-id attribute
                    let tr = cb.closest('tr');
                    return tr && tr.dataset.id ? tr.dataset.id : null;
                })
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
            .then(async res => {
                let data;
                try {
                    data = await res.json();
                } catch (e) {
                    throw new Error('Invalid JSON response');
                }
                if (data.status === 'success') {
                    performFilteredSearch();
                } else {
                    let msg = 'Failed to delete records.';
                    if (data.message) msg += '\n' + data.message;
                    if (data.debug) msg += '\nDebug: ' + JSON.stringify(data.debug);
                    alert(msg);
                }
            })
            .catch(err => {
                alert('Error deleting records. ' + (err && err.message ? err.message : ''));
            });
        });

        // Export to Excel functionality
        const exportExcelBtn = document.getElementById('export-excel-btn');
        exportExcelBtn.addEventListener('click', function() {
            // Get current filter values from the search state
            const searchTerm = searchInput.value.trim();
            const dateAnswered = dateAnsweredFilter.value;
            const selectedEvents = Array.from(selectedEventsForSearch);
            const selectedCategories = Array.from(selectedCategoriesForSearch);
            const selectedTopics = Array.from(selectedTopicsForSearch);
            
            // Build query parameters
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (dateAnswered) params.append('date_answered', dateAnswered);
            if (selectedEvents.length > 0) params.append('events', selectedEvents.join(','));
            if (selectedCategories.length > 0) params.append('categories', selectedCategories.join(','));
            if (selectedTopics.length > 0) params.append('topics', selectedTopics.join(','));
            
            // Create download URL using Laravel route
            const exportUrl = `{{ route('assessment.exportExcel') }}?${params.toString()}`;
            
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

    
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('details-modal');
    const modalContent = document.getElementById('modal-content');
    const closeModal = document.getElementById('close-modal');
    const closeModalFooter = document.getElementById('close-modal-footer'); // optional if footer button exists

    // Function to open modal and load details - make it globally available
    window.openModal = function(id) {
        modal.classList.remove('hidden');
        document.body.classList.add('modal-shadow');
        modalContent.innerHTML = 'Loading...';

        fetch(`/assessment/${id}/details`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.questions) {
                    let html = `<div class="space-y-6">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100 mb-4">Assessment Questions & Answers</h3>`;
                    data.questions.forEach((q, idx) => {
                        html += `<div class="border rounded-lg p-4 mb-4 bg-gray-50 dark:bg-zinc-800">
                            <div class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Q${idx+1}: ${q.text}</div>`;
                        if (q.answers && q.answers.length > 0) {
                            html += `<div class="space-y-2">`;
                            q.answers.forEach((ans, aidx) => {
                                // Determine if this is the participant's answer
                                const isParticipant = ans.is_participant;
                                // Determine if this is the correct answer
                                const isCorrect = ans.is_correct;
                                // If participant's answer is wrong, highlight correct
                                let answerClass = '';
                                if (isParticipant && isCorrect) {
                                    answerClass = 'bg-green-100 dark:bg-green-900/20 border-green-500';
                                } else if (isParticipant && !isCorrect) {
                                    answerClass = 'bg-red-100 dark:bg-red-900/20 border-red-500';
                                } else if (!isParticipant && isCorrect) {
                                    answerClass = 'bg-blue-50 dark:bg-blue-900/20 border-blue-500';
                                } else {
                                    answerClass = 'bg-gray-50 dark:bg-zinc-700';
                                }
                                html += `<div class="border rounded p-2 ${answerClass}">
                                    <span class="font-bold">${String.fromCharCode(65+aidx)}.</span> ${ans.text}
                                    ${isParticipant ? '<span class="ml-2 px-2 py-1 text-xs rounded bg-yellow-200 text-yellow-900">Participant Answer</span>' : ''}
                                    ${isCorrect ? '<span class="ml-2 px-2 py-1 text-xs rounded bg-green-200 text-green-900">Correct</span>' : ''}
                                </div>`;
                            });
                            html += `</div>`;
                        } else {
                            html += `<div class="text-gray-500">No answers available.</div>`;
                        }
                        html += `</div>`;
                    });
                    html += `</div>`;
                    modalContent.innerHTML = html;
                } else {
                    modalContent.innerHTML = '<div class="text-sm text-gray-600">Failed to load questions and answers.</div>';
                }
            })
            .catch(() => {
                modalContent.innerHTML = 'Error fetching data.';
            });
    }

    // Attach click to all "View Details" buttons (initial load)
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', () => {
            openModal(btn.dataset.id);
        });
    });

    // Close modal on clicking close buttons
    closeModal?.addEventListener('click', () => {
        modal.classList.add('hidden');
        document.body.classList.remove('modal-shadow');
    });
    closeModalFooter?.addEventListener('click', () => {
        modal.classList.add('hidden');
        document.body.classList.remove('modal-shadow');
    });

});
</script>
