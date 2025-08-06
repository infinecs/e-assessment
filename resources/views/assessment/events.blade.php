@extends('layout.appMain')

@section('content')
<!-- CSRF token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Custom styles for checkboxes -->
<style>
.event-topic-checkbox:checked,
.add-event-topic-checkbox:checked,
.row-checkbox:checked,
#checkbox-all:checked,
.category-filter-checkbox:checked,
.topic-filter-checkbox:checked {
    background-color: #7c3aed !important; /* violet-600 */
    border-color: #7c3aed !important;
}

.event-topic-checkbox:checked:after,
.add-event-topic-checkbox:checked:after,
.row-checkbox:checked:after,
#checkbox-all:checked:after,
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

.event-topic-checkbox,
.add-event-topic-checkbox,
.row-checkbox,
#checkbox-all,
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
            Assessment Events
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
                            Assessment Events
                        </a>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="col-span-12 xl:col-span-6">
    <div class="card dark:bg-zinc-800 dark:border-zinc-600">
        <!-- Header -->
        <div class="card-body border-b border-gray-100 dark:border-zinc-600 flex items-center justify-between">
            <!-- Search Bar -->
            <div class="flex items-center gap-3">
                <div class="relative">
                    <!-- Main Search Input -->
                    <input type="text" id="searchInput" placeholder="Search events by name or code..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm w-64 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="relative">
                    <button type="button" id="categoryFilterBtn" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white dark:bg-zinc-700 dark:border-zinc-600 dark:text-white hover:bg-gray-50 dark:hover:bg-zinc-600 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 flex items-center gap-2">
                        <i class="fas fa-layer-group text-gray-500"></i>
                        <span id="categoryFilterText">Categories</span>
                        <span id="categoryCount" class="hidden bg-violet-500 text-white text-xs px-2 py-1 rounded-full">0</span>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    
                    <!-- Category Dropdown -->
                    <div id="categoryDropdown" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto">
                        <div class="p-3">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Categories</span>
                                <button type="button" id="clearCategories" class="text-xs text-violet-600 hover:text-violet-800">Clear All</button>
                            </div>
                            <div id="categoryList" class="space-y-2">
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
                    <div id="topicDropdown" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto">
                        <div class="p-3">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Topics</span>
                                <button type="button" id="clearTopics" class="text-xs text-violet-600 hover:text-violet-800">Clear All</button>
                            </div>
                            <input type="text" id="topicSearch" placeholder="Search topics..." 
                                class="w-full mb-3 px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-600 dark:text-white">
                            <div id="topicList" class="space-y-2">
                                <!-- Topics will be loaded here -->
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
                    <i class="fas fa-file-excel"></i>
                    Export to Excel
                </button>

                <!-- Delete button (hidden by default) -->
                <button id="bulk-delete-btn" type="button"
                    class="hidden px-4 py-1.5 text-white bg-red-500 rounded hover:bg-red-600 text-sm">
                    Delete
                </button>

                <!-- Add button -->
                <button type="button" id="addEventBtn"
                    class="px-6 py-1.5 text-white btn bg-violet-500 border-violet-500 hover:bg-violet-600 hover:border-violet-600 focus:bg-violet-600 focus:border-violet-600 focus:ring focus:ring-violet-500/30 active:bg-violet-600 active:border-violet-600 text-sm">
                    Add
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="isolate">
                <div class="relative rounded-lg" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                    <table class="w-full min-w-[1100px] text-xs text-center text-gray-500 leading-tight">
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
                            <th class="px-2 py-1.5 bg-gray-50 dark:bg-zinc-700">Event Name</th>
                            <th class="px-2 py-1.5 bg-gray-50 dark:bg-zinc-700">Event Code</th>
                            <th class="px-2 py-1.5 bg-gray-50 dark:bg-zinc-700">Actions</th>
                            <th class="px-2 py-1.5 bg-gray-50 dark:bg-zinc-700">Question Limit</th>
                            <th class="px-2 py-1.5 bg-gray-50 dark:bg-zinc-700">Duration Each Question</th>
                            <th class="px-2 py-1.5 bg-gray-50 dark:bg-zinc-700">Start Date</th>
                            <th class="px-2 py-1.5 bg-gray-50 dark:bg-zinc-700">End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $row)
                            <tr
                                class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:hover:bg-zinc-700/50 dark:border-zinc-600"
                                data-event-id="{{ $row->EventID }}"
                                data-category-name="{{ $row->CategoryName ?? '' }}"
                                data-topic-names="">
                                <td class="w-4 p-3">
                                    <div class="flex items-center">
                                        <input type="checkbox" data-event-id="{{ $row->EventID }}"
                                            class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                                    </div>
                                </td>
                                <td class="px-2 py-1.5">{{ $row->EventName }}</td>
                                <td class="px-2 py-1.5">
                                    <a href="{{ url('participantRegister/' . urlencode($row->EventCode)) }}"
    class="text-blue-600 hover:underline" target="_blank">
    {{ $row->EventCode }}
</a>
                                </td>
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
                                                    onclick="editEvent({{ $row->EventID }}, '{{ $row->EventName }}', '{{ $row->EventCode }}', {{ $row->QuestionLimit }}, {{ $row->DurationEachQuestion }}, '{{ $row->StartDate }}', '{{ $row->EndDate }}')"
                                                    class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                                    <i class="mdi mdi-pencil text-base"></i>
                                                    <span>Edit</span>
                                                </button>
                                                <button type="button"
                                                    onclick="deleteEvent({{ $row->EventID }})"
                                                    class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                                    <i class="mdi mdi-trash-can text-base"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-1.5">{{ $row->QuestionLimit }}</td>
                                <td class="px-2 py-1.5">{{ $row->DurationEachQuestion }}</td>
                                <td class="px-2 py-1.5">{{ \Carbon\Carbon::parse($row->StartDate)->format('d M Y') }}</td>
                                <td class="px-2 py-1.5">{{ \Carbon\Carbon::parse($row->EndDate)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-2 py-1.5 text-center">No events found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div id="editEventModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-3/4 dark:bg-zinc-800">
            <form id="editEventForm">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-zinc-800">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 w-full flex gap-6"> <!-- FLEX LAYOUT FOR FORM -->
                            
                            <!-- LEFT: Input Fields (1/3) -->
                            <div class="w-1/3 space-y-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    Edit Event
                                </h3>
                                <div>
                                    <label for="edit_event_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Name</label>
                                    <input type="text" id="edit_event_name" name="EventName" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="edit_event_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Code</label>
                                    <input type="text" id="edit_event_code" name="EventCode" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="edit_question_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question Limit</label>
                                    <input type="number" id="edit_question_limit" name="QuestionLimit" required min="1"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="edit_duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration Each Question (seconds)</label>
                                    <input type="number" id="edit_duration" name="DurationEachQuestion" required min="1"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="edit_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                                    <input type="date" id="edit_start_date" name="StartDate" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="edit_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                                    <input type="date" id="edit_end_date" name="EndDate" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="edit_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                    <select id="edit_category" name="CategoryID" required onchange="loadCategoryTopics(this.value)"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                        <option value="">Select Category</option>
                                        @if(isset($allCategories) && count($allCategories) > 0)
                                            @foreach($allCategories as $category)
                                                <option value="{{ $category->CategoryID }}">{{ $category->CategoryName }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <!-- RIGHT: Topic Checkboxes (2/3) -->
                            <div class="w-2/3">
                                <div id="topics-section" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Select Topics for Event</label>
                                    <div class="max-h-[500px] overflow-y-auto border border-gray-300 rounded-md p-3 dark:border-zinc-600 dark:bg-zinc-700" id="eventTopicsContainer">
                                        <!-- Topics will be loaded here dynamically -->
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Select specific topics from the category for this event.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update Event
                    </button>
                    <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div id="addEventModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-3/4 dark:bg-zinc-800">
            <form id="addEventForm">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-zinc-800">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 w-full flex gap-6"> <!-- FLEX LAYOUT FOR FORM -->
                            
                            <!-- LEFT: Input Fields (1/3) -->
                            <div class="w-1/3 space-y-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    Add New Event
                                </h3>
                                <div>
                                    <label for="add_event_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Name</label>
                                    <input type="text" id="add_event_name" name="EventName" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="add_event_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Code</label>
                                    <input type="text" id="add_event_code" name="EventCode" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="add_question_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question Limit</label>
                                    <input type="number" id="add_question_limit" name="QuestionLimit" required min="1"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="add_duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration Each Question (seconds)</label>
                                    <input type="number" id="add_duration" name="DurationEachQuestion" required min="1"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="add_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                                    <input type="date" id="add_start_date" name="StartDate" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="add_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                                    <input type="date" id="add_end_date" name="EndDate" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="add_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                    <select id="add_category" name="CategoryID" required onchange="loadAddCategoryTopics(this.value)"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                        <option value="">Select Category</option>
                                        @if(isset($allCategories) && count($allCategories) > 0)
                                            @foreach($allCategories as $category)
                                                <option value="{{ $category->CategoryID }}">{{ $category->CategoryName }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <!-- RIGHT: Topic Checkboxes (2/3) -->
                            <div class="w-2/3">
                                <div id="add-topics-section" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Select Topics for Event</label>
                                    <div class="max-h-[500px] overflow-y-auto border border-gray-300 rounded-md p-3 dark:border-zinc-600 dark:bg-zinc-700" id="addEventTopicsContainer">
                                        <!-- Topics will be loaded here dynamically -->
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Select at least one topic from the category for this event.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Add Event
                    </button>
                    <button type="button" onclick="closeAddModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $records->links('pagination::tailwind') }}
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('checkbox-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('tbody tr');

        // Filter elements
        const categoryFilterBtn = document.getElementById('categoryFilterBtn');
        const categoryDropdown = document.getElementById('categoryDropdown');
        const categoryList = document.getElementById('categoryList');
        const categoryCount = document.getElementById('categoryCount');
        const clearCategories = document.getElementById('clearCategories');

        const topicFilterBtn = document.getElementById('topicFilterBtn');
        const topicDropdown = document.getElementById('topicDropdown');
        const topicList = document.getElementById('topicList');
        const topicSearch = document.getElementById('topicSearch');
        const topicCount = document.getElementById('topicCount');
        const clearTopics = document.getElementById('clearTopics');

        const performSearchBtn = document.getElementById('performSearchBtn');
        const clearAllFilters = document.getElementById('clearAllFilters');

        // Search state
        let allCategories = [];
        let allTopics = [];
        let categoryTopicsMap = new Map(); // Maps categoryId to array of topic names
        let selectedCategories = new Set();
        let selectedTopics = new Set();
        let eventTopicMap = new Map(); // Maps eventId to topic names
        let searchTimeout = null; // For debouncing search

        // Debounced search function
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performFilteredSearch();
            }, 800); // Increased delay for server requests
        }

        // Load categories and topics for filters
        async function loadFiltersData() {
            console.log('Loading categories and topics for filters...');
            
            try {
                // Load categories
                const categories = @json($allCategories ?? []);
                allCategories = categories;
                renderCategoryList();

                // Load category-topic relationships
                for (const category of allCategories) {
                    try {
                        const response = await fetch(`/category/${category.CategoryID}/topics`);
                        const data = await response.json();
                        
                        if (data.success && data.topics) {
                            const topicNames = data.topics.map(topic => topic.TopicName);
                            categoryTopicsMap.set(category.CategoryID, topicNames);
                        }
                    } catch (error) {
                        console.warn(`Failed to load topics for category ${category.CategoryID}:`, error);
                    }
                }

                // Load all topics from events
                const eventRows = Array.from(tableRows).filter(row => 
                    row.children.length > 1 && !row.children[0].getAttribute('colspan')
                );

                let loadedCount = 0;
                const topicsSet = new Set();

                for (const row of eventRows) {
                    const eventId = row.dataset.eventId;
                    if (!eventId) continue;

                    try {
                        const response = await fetch(`/events/${eventId}/details`);
                        const data = await response.json();
                        
                        if (data.success && data.topic_names) {
                            // Store topic names for this event
                            eventTopicMap.set(eventId, data.topic_names);
                            
                            // Add topics to the global set
                            data.topic_names.forEach(topic => topicsSet.add(topic));
                        }
                        
                        loadedCount++;
                    } catch (error) {
                        console.warn(`Failed to load topics for event ${eventId}:`, error);
                        loadedCount++;
                    }
                }

                // Convert topics set to array and sort
                allTopics = Array.from(topicsSet).sort();
                renderTopicList();

                console.log('Filter data loaded successfully!');
                
            } catch (error) {
                console.error('Error loading filter data:', error);
            }
        }

        // Render category list
        function renderCategoryList() {
            categoryList.innerHTML = '';
            allCategories.forEach(category => {
                const div = document.createElement('div');
                div.className = 'flex items-center';
                div.innerHTML = `
                    <input type="checkbox" id="cat_${category.CategoryID}" 
                           value="${category.CategoryID}" 
                           data-name="${category.CategoryName}"
                           class="category-filter-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                    <label for="cat_${category.CategoryID}" class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        ${category.CategoryName}
                    </label>
                `;
                categoryList.appendChild(div);
            });

            // Add event listeners
            document.querySelectorAll('.category-filter-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', handleCategoryChange);
            });
        }

        // Render topic list
        function renderTopicList() {
            const searchTerm = topicSearch.value.toLowerCase();
            
            // Get available topics based on selected categories
            let availableTopics = [];
            if (selectedCategories.size === 0) {
                // If no categories selected, show all topics
                availableTopics = allTopics;
            } else {
                // Only show topics from selected categories
                const topicsSet = new Set();
                selectedCategories.forEach(categoryName => {
                    // Find category ID by name
                    const category = allCategories.find(cat => cat.CategoryName === categoryName);
                    if (category && categoryTopicsMap.has(category.CategoryID)) {
                        categoryTopicsMap.get(category.CategoryID).forEach(topic => {
                            topicsSet.add(topic);
                        });
                    }
                });
                availableTopics = Array.from(topicsSet).sort();
            }
            
            // Filter topics by search term
            const filteredTopics = availableTopics.filter(topic => 
                topic.toLowerCase().includes(searchTerm)
            );

            topicList.innerHTML = '';
            filteredTopics.forEach(topic => {
                const div = document.createElement('div');
                div.className = 'flex items-center';
                div.innerHTML = `
                    <input type="checkbox" id="topic_${topic.replace(/\s+/g, '_')}" 
                           value="${topic}" 
                           class="topic-filter-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                    <label for="topic_${topic.replace(/\s+/g, '_')}" class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        ${topic}
                    </label>
                `;
                topicList.appendChild(div);
            });

            // Restore selected state for topics that are still available
            selectedTopics.forEach(topic => {
                const checkbox = document.getElementById(`topic_${topic.replace(/\s+/g, '_')}`);
                if (checkbox) checkbox.checked = true;
            });

            // Remove topics from selected set that are no longer available
            const availableTopicsSet = new Set(filteredTopics);
            selectedTopics.forEach(topic => {
                if (!availableTopicsSet.has(topic)) {
                    selectedTopics.delete(topic);
                }
            });

            // Add event listeners
            document.querySelectorAll('.topic-filter-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', handleTopicChange);
            });
            
            // Update display after filtering
            updateTopicDisplay();
        }

        // Handle category selection
        function handleCategoryChange(event) {
            const categoryId = event.target.value;
            const categoryName = event.target.dataset.name;
            
            if (event.target.checked) {
                selectedCategories.add(categoryName);
            } else {
                selectedCategories.delete(categoryName);
            }
            
            updateCategoryDisplay();
            // Re-render topics list based on new category selection
            renderTopicList();
        }

        // Handle topic selection
        function handleTopicChange(event) {
            const topic = event.target.value;
            
            if (event.target.checked) {
                selectedTopics.add(topic);
            } else {
                selectedTopics.delete(topic);
            }
            
            updateTopicDisplay();
        }

        // Update category display
        function updateCategoryDisplay() {
            const count = selectedCategories.size;
            if (count > 0) {
                categoryCount.textContent = count;
                categoryCount.classList.remove('hidden');
            } else {
                categoryCount.classList.add('hidden');
            }
            updateClearAllButton();
        }

        // Update topic display
        function updateTopicDisplay() {
            const count = selectedTopics.size;
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
            const hasFilters = selectedCategories.size > 0 || selectedTopics.size > 0 || searchInput.value.trim();
            clearAllFilters.classList.toggle('hidden', !hasFilters);
        }

        // Perform search with filters - SERVER SIDE
        function performFilteredSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const selectedCategoryNames = Array.from(selectedCategories);
            const selectedTopicNames = Array.from(selectedTopics);
            
            // Build query parameters for server-side filtering
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (selectedCategoryNames.length > 0) params.append('categories', selectedCategoryNames.join(','));
            if (selectedTopicNames.length > 0) params.append('topics', selectedTopicNames.join(','));
            
            // Reload the page with filters applied
            const currentUrl = new URL(window.location.href);
            currentUrl.search = params.toString();
            window.location.href = currentUrl.toString();
        }

        // Clear all filters - SERVER SIDE
        function clearAllFiltersAction() {
            // Clear all form inputs
            searchInput.value = '';
            selectedCategories.clear();
            document.querySelectorAll('.category-filter-checkbox').forEach(cb => cb.checked = false);
            updateCategoryDisplay();
            selectedTopics.clear();
            document.querySelectorAll('.topic-filter-checkbox').forEach(cb => cb.checked = false);
            updateTopicDisplay();
            
            // Re-render topics list to show all topics when categories are cleared
            renderTopicList();
            
            // Close dropdowns
            categoryDropdown.classList.add('hidden');
            topicDropdown.classList.add('hidden');
            
            // Reload page without filters
            const currentUrl = new URL(window.location.href);
            currentUrl.search = '';
            window.location.href = currentUrl.toString();
        }

        // Event listeners
        categoryFilterBtn.addEventListener('click', () => {
            categoryDropdown.classList.toggle('hidden');
            topicDropdown.classList.add('hidden');
        });

        topicFilterBtn.addEventListener('click', () => {
            topicDropdown.classList.toggle('hidden');
            categoryDropdown.classList.add('hidden');
        });

        clearCategories.addEventListener('click', () => {
            selectedCategories.clear();
            document.querySelectorAll('.category-filter-checkbox').forEach(cb => cb.checked = false);
            updateCategoryDisplay();
            // Re-render topics list when categories are cleared
            renderTopicList();
        });

        clearTopics.addEventListener('click', () => {
            selectedTopics.clear();
            document.querySelectorAll('.topic-filter-checkbox').forEach(cb => cb.checked = false);
            updateTopicDisplay();
        });

        topicSearch.addEventListener('input', renderTopicList);
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
            if (!categoryFilterBtn.contains(e.target) && !categoryDropdown.contains(e.target)) {
                categoryDropdown.classList.add('hidden');
            }
            if (!topicFilterBtn.contains(e.target) && !topicDropdown.contains(e.target)) {
                topicDropdown.classList.add('hidden');
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
            
            // Restore category selections
            const categoriesParam = urlParams.get('categories');
            if (categoriesParam) {
                const categories = categoriesParam.split(',');
                
                // Check the appropriate checkboxes when they're rendered and sync selectedCategories
                setTimeout(() => {
                    selectedCategories.clear(); // Clear first to avoid duplicates
                    categories.forEach(categoryName => {
                        const checkbox = document.querySelector(`input[data-name="${categoryName}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                            selectedCategories.add(categoryName); // Only add if checkbox exists
                        }
                    });
                    updateCategoryDisplay();
                    // Re-render topics list after category selection is restored
                    renderTopicList();
                }, 100);
            }
            
            // Restore topic selections
            const topicsParam = urlParams.get('topics');
            if (topicsParam) {
                const topics = topicsParam.split(',');
                
                setTimeout(() => {
                    selectedTopics.clear(); // Clear first to avoid duplicates
                    topics.forEach(topicName => {
                        const checkbox = document.getElementById(`topic_${topicName.replace(/\s+/g, '_')}`);
                        if (checkbox) {
                            checkbox.checked = true;
                            selectedTopics.add(topicName); // Only add if checkbox exists
                        }
                    });
                    updateTopicDisplay();
                }, 200); // Slightly longer delay to ensure topics are rendered
            }
        }

        // Initialize
        initializeFilters();

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
                                <p class="text-lg font-medium">No events found</p>
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

        function updateBulkDeleteVisibility() {
            const visibleCheckboxes = Array.from(rowCheckboxes).filter(cb => 
                cb.closest('tr').style.display !== 'none'
            );
            const anyChecked = visibleCheckboxes.some(cb => cb.checked);
            if (bulkDeleteBtn) {
                bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
            }
        }

        // Checkbox management
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
            cb.addEventListener('change', function() {
                updateSelectAllState();
                updateBulkDeleteVisibility();
            });
        });

        // Bulk delete functionality
        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                const eventIds = Array.from(checkedBoxes).map(cb => parseInt(cb.getAttribute('data-event-id'))).filter(id => !isNaN(id));
                
                if (eventIds.length === 0) {
                    alert('Please select events to delete.');
                    return;
                }
                
                console.log('Event IDs to delete:', eventIds); // Debug log
                
                const confirmMessage = `Are you sure you want to delete ${eventIds.length} event(s)? This action cannot be undone.`;
                if (!confirm(confirmMessage)) {
                    return;
                }
                
                // Perform bulk delete - same as other pages
                fetch('/events/bulk-delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ event_ids: eventIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message || 'Events deleted successfully!');
                        location.reload();
                    } else {
                        alert(data.message || 'Error deleting events');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting events');
                });
            });
        }

        // Dropdown menu
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

        // Edit form submission
        document.getElementById('editEventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            updateEvent();
        });

        // Add button click handler
        document.getElementById('addEventBtn').addEventListener('click', function() {
            openAddModal();
        });

        // Add form submission
        document.getElementById('addEventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            addEvent();
        });

        // Export to Excel functionality
        const exportExcelBtn = document.getElementById('export-excel-btn');
        exportExcelBtn.addEventListener('click', function() {
            // Get current filter values
            const searchTerm = searchInput.value.trim();
            const selectedCategoryNames = Array.from(selectedCategories);
            const selectedTopicNames = Array.from(selectedTopics);
            
            // Build query parameters
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (selectedCategoryNames.length > 0) params.append('categories', selectedCategoryNames.join(','));
            if (selectedTopicNames.length > 0) params.append('topics', selectedTopicNames.join(','));
            
            // Create download URL using Laravel route
            const exportUrl = `{{ route('events.exportExcel') }}?${params.toString()}`;
            
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

    let currentEventId = null;

    function editEvent(eventId, eventName, eventCode, questionLimit, duration, startDate, endDate) {
        currentEventId = eventId;
        
        // Fetch event details including category and topics
        fetch(`/events/${eventId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate form fields
                    document.getElementById('edit_event_name').value = data.event.EventName || '';
                    document.getElementById('edit_event_code').value = data.event.EventCode || '';
                    document.getElementById('edit_question_limit').value = data.event.QuestionLimit || '';
                    document.getElementById('edit_duration').value = data.event.DurationEachQuestion || '';
                    document.getElementById('edit_start_date').value = data.event.StartDate ? data.event.StartDate.split(' ')[0] : '';
                    document.getElementById('edit_end_date').value = data.event.EndDate ? data.event.EndDate.split(' ')[0] : '';
                    
                    // Set category
                    const categorySelect = document.getElementById('edit_category');
                    categorySelect.value = data.event.CategoryID || '';
                    
                    // Load category topics if category is selected
                    if (data.event.CategoryID) {
                        loadCategoryTopics(data.event.CategoryID, data.selected_topic_ids);
                    }
                    
                    // Show modal
                    document.getElementById('editEventModal').classList.remove('hidden');
                } else {
                    alert('Error loading event details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading event details.');
            });
    }

    function loadCategoryTopics(categoryId, selectedTopicIds = []) {
        if (!categoryId) {
            document.getElementById('topics-section').classList.add('hidden');
            return;
        }
        
        fetch(`/category/${categoryId}/topics`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('eventTopicsContainer');
                    container.innerHTML = '';
                    
                    if (data.topics && data.topics.length > 0) {
                        data.topics.forEach(topic => {
                            const isSelected = selectedTopicIds.includes(String(topic.TopicID));
                            const div = document.createElement('div');
                            div.className = 'flex items-center mb-2';
                            div.innerHTML = `
                                <input type="checkbox" 
                                       id="event_topic_${topic.TopicID}" 
                                       name="edit_selected_topic_ids[]" 
                                       value="${topic.TopicID}"
                                       ${isSelected ? 'checked' : ''}
                                       class="event-topic-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                                <label for="event_topic_${topic.TopicID}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    ${topic.TopicName} (ID: ${topic.TopicID})
                                </label>
                            `;
                            container.appendChild(div);
                        });
                        document.getElementById('topics-section').classList.remove('hidden');
                    } else {
                        container.innerHTML = '<div class="text-center py-2 text-gray-500 dark:text-gray-400">No topics available for this category</div>';
                        document.getElementById('topics-section').classList.remove('hidden');
                    }
                } else {
                    alert('Error loading category topics: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading category topics.');
            });
    }

    function closeEditModal() {
        document.getElementById('editEventModal').classList.add('hidden');
        document.getElementById('topics-section').classList.add('hidden');
        document.getElementById('eventTopicsContainer').innerHTML = '';
        currentEventId = null;
    }

    function openAddModal() {
        document.getElementById('addEventModal').classList.remove('hidden');
        // Reset form
        document.getElementById('addEventForm').reset();
        document.getElementById('add-topics-section').classList.add('hidden');
        document.getElementById('addEventTopicsContainer').innerHTML = '';
    }

    function closeAddModal() {
        document.getElementById('addEventModal').classList.add('hidden');
        document.getElementById('add-topics-section').classList.add('hidden');
        document.getElementById('addEventTopicsContainer').innerHTML = '';
        // Reset form
        document.getElementById('addEventForm').reset();
    }

    function loadAddCategoryTopics(categoryId) {
        if (!categoryId) {
            document.getElementById('add-topics-section').classList.add('hidden');
            return;
        }
        
        fetch(`/category/${categoryId}/topics`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('addEventTopicsContainer');
                    container.innerHTML = '';
                    
                    if (data.topics && data.topics.length > 0) {
                        data.topics.forEach(topic => {
                            const div = document.createElement('div');
                            div.className = 'flex items-center mb-2';
                            div.innerHTML = `
                                <input type="checkbox" 
                                       id="add_event_topic_${topic.TopicID}" 
                                       name="add_selected_topic_ids[]" 
                                       value="${topic.TopicID}"
                                       class="add-event-topic-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                                <label for="add_event_topic_${topic.TopicID}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    ${topic.TopicName} (ID: ${topic.TopicID})
                                </label>
                            `;
                            container.appendChild(div);
                        });
                        document.getElementById('add-topics-section').classList.remove('hidden');
                    } else {
                        container.innerHTML = '<div class="text-center py-2 text-gray-500 dark:text-gray-400">No topics available for this category</div>';
                        document.getElementById('add-topics-section').classList.remove('hidden');
                    }
                } else {
                    alert('Error loading category topics: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading category topics.');
            });
    }

    function addEvent() {
        const formData = new FormData(document.getElementById('addEventForm'));
        const data = Object.fromEntries(formData);
        
        // Validate dates
        const startDate = new Date(data.StartDate);
        const endDate = new Date(data.EndDate);
        
        if (startDate >= endDate) {
            alert('End date must be after start date.');
            return;
        }
        
        // Get selected topic IDs
        const selectedTopics = [];
        document.querySelectorAll('.add-event-topic-checkbox:checked').forEach(checkbox => {
            selectedTopics.push(checkbox.value);
        });
        
        // Validate that at least one topic is selected
        if (selectedTopics.length === 0) {
            alert('Please select at least one topic for the event.');
            return;
        }
        
        data.selected_topic_ids = selectedTopics;

        // Debug logging
        console.log('Form data being sent:', data);

        // Use the correct route that we just added to the backend
        fetch('/events', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.log('Error response text:', text);
                    throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert(data.message || 'Event added successfully!');
                closeAddModal();
                location.reload(); // Refresh the page to show new event
            } else {
                // Handle validation errors with simple message
                if (data.errors && typeof data.errors === 'object') {
                    // Get the first error message from the first field
                    const firstField = Object.keys(data.errors)[0];
                    const firstError = data.errors[firstField][0];
                    alert(firstError);
                } else {
                    alert(data.message || 'Unknown error occurred');
                }
            }
        })
        .catch(error => {
            console.error('Detailed error:', error);
            // Try to parse the error response for simple error message
            let errorMessage = 'An error occurred while adding the event.';
            if (error.message && error.message.includes('response:')) {
                try {
                    const responseStart = error.message.indexOf('response:') + 'response:'.length;
                    const responseText = error.message.substring(responseStart).trim();
                    const responseData = JSON.parse(responseText);
                    
                    if (responseData.errors && typeof responseData.errors === 'object') {
                        // Get the first error message from the first field
                        const firstField = Object.keys(responseData.errors)[0];
                        const firstError = responseData.errors[firstField][0];
                        errorMessage = firstError;
                    } else if (responseData.message) {
                        errorMessage = responseData.message;
                    }
                } catch (parseError) {
                    // If parsing fails, use a simple error message
                    errorMessage = 'An error occurred while adding the event.';
                }
            }
            alert(errorMessage);
        });
    }

    function updateEvent() {
        if (!currentEventId) return;

        const formData = new FormData(document.getElementById('editEventForm'));
        const data = Object.fromEntries(formData);
        
        // Get selected topic IDs
        const selectedTopics = [];
        document.querySelectorAll('.event-topic-checkbox:checked').forEach(checkbox => {
            selectedTopics.push(checkbox.value);
        });
        
        // Validate that at least one topic is selected
        if (selectedTopics.length === 0) {
            alert('Please select at least one topic for the event.');
            return;
        }
        
        data.selected_topic_ids = selectedTopics;

        fetch(`/events/${currentEventId}`, {
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
                closeEditModal();
                location.reload(); // Refresh the page to show updated data
            } else {
                // Handle validation errors with simple message
                if (data.errors && typeof data.errors === 'object') {
                    // Get the first error message from the first field
                    const firstField = Object.keys(data.errors)[0];
                    const firstError = data.errors[firstField][0];
                    alert(firstError);
                } else {
                    alert(data.message || 'Unknown error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the event.');
        });
    }

    function deleteEvent(eventId) {
        if (!confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
            return;
        }

        fetch(`/events/${eventId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Refresh the page to remove deleted row
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the event.');
        });
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const editModal = document.getElementById('editEventModal');
        const addModal = document.getElementById('addEventModal');
        
        if (event.target === editModal) {
            closeEditModal();
        }
        
        if (event.target === addModal) {
            closeAddModal();
        }
    });
</script>
