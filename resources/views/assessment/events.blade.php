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
            Assessment 
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
                            Assessments
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
                    <input type="text" id="searchInput" placeholder="Search Assessment Name/Code" 
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
                    <i class="fas fa-file-csv"></i>
                    Export to CSV
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
                <div class="relative rounded-lg" style="max-height: 500px; min-height: 350px; overflow-y: auto; overflow-x: auto; display: flex; flex-direction: column; justify-content: flex-start;">
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
                            <th class="px-2 py-1.5 bg-gray-50 dark:bg-zinc-700">Assessment Name</th>
                            <th class="px-2 py-1.5 bg-gray-50 dark:bg-zinc-700">Assessment Code</th>
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
                                data-category-id="{{ $row->CategoryID ?? '' }}"
                                data-category-name="{{ $row->CategoryName ?? '' }}"
                                data-topic-names="">
                                <td class="w-4 p-3">
                                    <div class="flex items-center">
                                        <input type="checkbox" data-event-id="{{ $row->EventID }}"
                                            class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                                    </div>
                                </td>
                                <td class="px-2 py-1.5">
                                    <a href="#" class="assessment-info-link text-violet-600 hover:underline" data-event-id="{{ $row->EventID }}">
                                        {{ $row->EventName }}
                                    </a>
                                </td>
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
                                                    onclick="editEvent({{ $row->EventID }}, '{{ $row->EventName }}', '{{ $row->EventCode }}', {{ $row->QuestionLimit }}, {{ $row->DurationEachQuestion }}, '{{ $row->StartDate }}', '{{ $row->EndDate }}', '{{ $row->EventPassword ?? '' }}')"
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
                                                <button type="button"
                                                    onclick="openWeightageModal({{ $row->EventID }})"
                                                    class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                                    <i class="mdi mdi-scale-balance text-base"></i>
                                                    <span>Weightage</span>
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
                                <td colspan="12" class="px-2 py-1.5 text-center">No assessment found</td>
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
                                    Edit Assessment
                                </h3>
                                <div>
                                    <label for="edit_event_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assessment Name</label>
                                    <input type="text" id="edit_event_name" name="EventName" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="edit_event_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assessment Code</label>
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
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Select Topics for Assessment</label>
                                    
                                    <!-- Container for selected topic tags -->
                                    <div id="edit-selected-topics-tags-container" class="mb-3 p-2 border border-gray-200 dark:border-zinc-600 rounded-md min-h-[40px] bg-gray-50 dark:bg-zinc-700/50 flex flex-wrap gap-2">
                                        <!-- Tags will be rendered here -->
                                    </div>

                                    <div class="max-h-[420px] overflow-y-auto border border-gray-300 rounded-md p-3 dark:border-zinc-600 dark:bg-zinc-700" id="eventTopicsContainer">
                                        <!-- Topics will be loaded here dynamically -->
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Select at least one topic from any category for this assessment.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:items-center dark:bg-zinc-700">
                    <div class="flex items-center mr-auto mt-3 sm:mt-0">
                        <input type="text" id="edit_event_password" name="EventPassword" placeholder="Password" class="block w-64 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white mr-2">
                        <button type="button" onclick="generateEditEventPassword()" class="inline-flex justify-center rounded-md border border-green-300 shadow-sm px-3 py-2 bg-green-200 text-sm font-medium text-gray-700 hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
                            Generate
                        </button>
                    </div>
                    <div class="flex items-center ml-auto mt-3 sm:mt-0">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Update Assessment
                        </button>
                        <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
                            Cancel
                        </button>
                    </div>
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
                                    Add New Assessment
                                </h3>
                                <div>
                                    <label for="add_event_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assessment Name</label>
                                    <input type="text" id="add_event_name" name="EventName" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="add_event_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assessment Code</label>
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
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Select Topics for Assessment</label>
                                    
                                    <!-- Container for selected topic tags -->
                                    <div id="selected-topics-tags-container" class="mb-3 p-2 border border-gray-200 dark:border-zinc-600 rounded-md min-h-[40px] bg-gray-50 dark:bg-zinc-700/50 flex flex-wrap gap-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">No topics selected</span>
                                    </div>

                                    <div class="max-h-[420px] overflow-y-auto border border-gray-300 rounded-md p-3 dark:border-zinc-600 dark:bg-zinc-700" id="addEventTopicsContainer">
                                        <!-- Topics will be loaded here dynamically -->
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Select at least one topic from any category for this assessment.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:items-center dark:bg-zinc-700">
                    <div class="flex items-center mr-auto mt-3 sm:mt-0">
                        <input type="text" id="add_event_password" name="EventPassword" placeholder="Password" class="block w-64 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white mr-2">
                        <button type="button" onclick="generateEventPassword()" class="inline-flex justify-center rounded-md border border-green-300 shadow-sm px-3 py-2 bg-green-200 text-sm font-medium text-gray-700 hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
                            Generate
                        </button>
                    </div>
                    <div class="flex items-center ml-auto mt-3 sm:mt-0">
                        <button type="submit" id="addAssessmentBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Add Assessment
                        </button>
                        <button type="button" onclick="closeAddModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500 ml-4">
                            Cancel
                        </button>
                    </div>
                </div>
                
            </form>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $records->links('pagination::tailwind') }}
</div>

<!-- Weightage Modal -->
<div id="weightageModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-zinc-800">
            <form id="weightageForm">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-zinc-800">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-2">
                        Topic Weightage
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Assign a weightage to each topic. If set, the total must be exactly 100%.
                    </p>
                    <div id="weightageTopicsContainer" class="max-h-80 overflow-y-auto space-y-4 pr-2">
                        <!-- Topics and weightage inputs will be loaded here -->
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Weightage:</span>
                            <span id="totalWeightage" class="text-lg font-bold text-gray-900 dark:text-gray-100">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-zinc-700 mt-2">
                            <div id="weightageProgressBar" class="bg-violet-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <div id="weightageValidationMessage" class="text-red-500 text-xs mt-2 h-4"></div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700">
                    <button type="submit" id="saveWeightageBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm transition-opacity">
                        Save Weightages
                    </button>
                    <button type="button" onclick="closeWeightageModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
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
        // Prevent spaces in Event Code fields (add & edit)
        function preventSpaces(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            input.addEventListener('keydown', function(e) {
                if (e.key === ' ') e.preventDefault();
            });
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/\s+/g, '');
            });
        }
        preventSpaces('add_event_code');
        preventSpaces('edit_event_code');
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

        // Perform search with filters - SERVER SIDE (AJAX - no page refresh)
        function performFilteredSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const selectedCategoryNames = Array.from(selectedCategories);
            const selectedTopicNames = Array.from(selectedTopics);
            
            // Build query parameters for server-side filtering
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (selectedCategoryNames.length > 0) params.append('categories', selectedCategoryNames.join(','));
            if (selectedTopicNames.length > 0) params.append('topics', selectedTopicNames.join(','));
            params.append('ajax', '1'); // Add AJAX flag
            
            // Show loading state
            const tbody = document.querySelector('tbody');
            tbody.innerHTML = '<tr><td colspan="12" class="px-2 py-1.5 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            
            // Make AJAX request to get filtered results
            fetch(`/events?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update table body with new data
                    tbody.innerHTML = data.html;

                    // Restore pagination if provided
                    const paginationContainer = document.querySelector('.mt-4');
                    if (paginationContainer) {
                        if (data.pagination && data.pagination.links) {
                            paginationContainer.innerHTML = data.pagination.links;
                        } else if (data.total !== undefined) {
                            let paginationText = `Showing ${data.total} results`;
                            if (data.current_page && data.last_page && data.last_page > 1) {
                                paginationText += ` (Page ${data.current_page} of ${data.last_page})`;
                            }
                            paginationContainer.innerHTML = `<div class="text-sm text-gray-700 dark:text-gray-300">${paginationText}</div>`;
                        }
                    }

                    // Update URL without page refresh
                    const currentUrl = new URL(window.location.href);
                    const newParams = new URLSearchParams();
                    if (searchTerm) newParams.append('search', searchTerm);
                    if (selectedCategoryNames.length > 0) newParams.append('categories', selectedCategoryNames.join(','));
                    if (selectedTopicNames.length > 0) newParams.append('topics', selectedTopicNames.join(','));
                    currentUrl.search = newParams.toString();
                    window.history.pushState({}, '', currentUrl.toString());

                    // Re-initialize row checkboxes and assessment info links for new content
                    initializeRowCheckboxes();
                    initializeAssessmentInfoLinks();

                    // Update filter displays
                    updateCategoryDisplay();
                    updateTopicDisplay();
                    updateClearAllButton();

                    console.log(`AJAX search completed: ${data.total || 0} events found`);
                } else {
                    tbody.innerHTML = '<tr><td colspan="12" class="px-2 py-1.5 text-center text-red-500">Error loading results</td></tr>';
                    console.error('Search error:', data.message);
                }
            })
            .catch(error => {
                console.error('AJAX error:', error);
                tbody.innerHTML = '<tr><td colspan="12" class="px-2 py-1.5 text-center text-red-500">Error loading results</td></tr>';
            });
        }

        // Clear all filters - SERVER SIDE (AJAX - no page refresh)
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
            
            // Show loading state
            const tbody = document.querySelector('tbody');
            tbody.innerHTML = '<tr><td colspan="12" class="px-2 py-1.5 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            
            // Make AJAX request to get all results (no filters)
            fetch('/events?ajax=1', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update table body with new data
                    tbody.innerHTML = data.html;

                    // Update pagination info display (simple text)
                    const paginationContainer = document.querySelector('.mt-4');
                    if (paginationContainer && data.total !== undefined) {
                        let paginationText = `Showing ${data.total} results`;
                        if (data.current_page && data.last_page && data.last_page > 1) {
                            paginationText += ` (Page ${data.current_page} of ${data.last_page})`;
                        }
                        paginationContainer.innerHTML = `<div class="text-sm text-gray-700 dark:text-gray-300">${paginationText}</div>`;
                    }

                    // Update URL without page refresh
                    const currentUrl = new URL(window.location.href);
                    currentUrl.search = '';
                    window.history.pushState({}, '', currentUrl.toString());

                    // Re-initialize row checkboxes and assessment info links for new content
                    initializeRowCheckboxes();
                    initializeAssessmentInfoLinks();

                    console.log('Filters cleared successfully');
                } else {
                    tbody.innerHTML = '<tr><td colspan="12" class="px-2 py-1.5 text-center text-red-500">Error loading results</td></tr>';
                    console.error('Clear filters error:', data.message);
                }
                // Always refresh the page after clearing filters
                window.location.reload();
            })
            .catch(error => {
                console.error('AJAX error:', error);
                tbody.innerHTML = '<tr><td colspan="12" class="px-2 py-1.5 text-center text-red-500">Error loading results</td></tr>';
                // Always refresh the page after clearing filters, even on error
                window.location.reload();
            });
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

        // Modal for assessment info


        function showAssessmentInfoModal(eventId) {
            fetch(`/events/${eventId}/details`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.event) {
                    let modal = document.getElementById('assessmentInfoModal');
                    if (!modal) {
                        modal = document.createElement('div');
                        modal.id = 'assessmentInfoModal';
                        modal.className = 'fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center bg-black bg-opacity-50';
                        modal.innerHTML = `
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-auto dark:bg-zinc-800">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-zinc-800">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                                Details for <span id="eventNameSpan"></span>
                                            </h3>
                                            <div class="mt-4">
                                                <div class="max-h-96 overflow-y-auto">
                                                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-400">
                                                            <tr>
                                                                <th scope="col" class="px-4 py-3" style="width:auto;white-space:nowrap;">Category Name</th>
                                                                <th scope="col" class="px-4 py-3" style="width:auto;white-space:nowrap;">Topics</th>
                                                                <th scope="col" class="px-4 py-3" style="width:auto;white-space:nowrap;">Password</th>
                                                                <th scope="col" class="px-4 py-3">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="px-4 py-3" id="categoryNameCell" style="width:auto;white-space:nowrap;"></td>
                                                                <td class="px-4 py-3" id="topicsCell" style="width:auto;white-space:nowrap;"></td>
                                                                <td class="px-4 py-3" id="passwordCell" style="width:auto;white-space:nowrap;"></td>
                                <td class="px-4 py-3" id="actionCell"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700">
                                    <button type="button" id="closeAssessmentInfoModal" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Close
                                    </button>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(modal);
                    }
                    // Set values
                    modal.querySelector('#eventNameSpan').textContent = data.event.EventName;
                    // Fallback: if CategoryName is missing, get it from the table row
                    let categoryName = data.event.CategoryName;
                    if (!categoryName) {
                        // Find the row in the table
                        const row = document.querySelector(`tr[data-event-id='${eventId}']`);
                        if (row) {
                            categoryName = row.getAttribute('data-category-name') || '-';
                        } else {
                            categoryName = '-';
                        }
                    }
                    modal.querySelector('#categoryNameCell').textContent = categoryName;
                    // Show topics as a bulleted list
                    if (data.topic_names && data.topic_names.length) {
                        const ul = document.createElement('ul');
                        ul.style.listStyleType = 'disc';
                        ul.style.paddingLeft = '1.5em';
                        data.topic_names.forEach(topic => {
                            const li = document.createElement('li');
                            li.textContent = topic;
                            ul.appendChild(li);
                        });
                        const topicsCell = modal.querySelector('#topicsCell');
                        topicsCell.textContent = '';
                        topicsCell.appendChild(ul);
                    } else {
                        modal.querySelector('#topicsCell').textContent = '-';
                    }
                    // Password field with eye icon for show/hide
                    const passwordValue = data.event.EventPassword || '-';
                    const passwordCell = modal.querySelector('#passwordCell');
                    passwordCell.innerHTML = `<span id="assessmentPassword" style="letter-spacing:2px;">${passwordValue ? '•'.repeat(passwordValue.length) : '-'}</span>`;

                    // Improved design: subtle background, modern button, tooltip
                    passwordCell.style.background = 'var(--tw-bg-opacity,1)'; // Remove custom color, use table bg
                    passwordCell.style.borderRadius = '';
                    passwordCell.style.textAlign = 'center';
                    passwordCell.style.verticalAlign = 'middle';

                    const actionCell = modal.querySelector('#actionCell');
                    // Start with closed eye (password hidden)
                    actionCell.innerHTML = `
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <button id="togglePasswordBtn" type="button" title="Show/Hide Password" style="background: #ede9fe; border: none; outline: none; cursor: pointer; border-radius: 6px; padding: 6px 10px; display: flex; align-items: center; justify-content: center; transition: background 0.2s; box-shadow: 0 1px 2px rgba(80,80,120,0.04);">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#6d28d9" width="22" height="22">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m3.249-2.383A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.043 5.306M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                </svg>
                            </button>
                            <button id="copyPasswordBtn" type="button" title="Copy Password" style="background: #dbeafe; border: none; outline: none; cursor: pointer; border-radius: 6px; padding: 6px 10px; display: flex; align-items: center; justify-content: center; transition: background 0.2s; box-shadow: 0 1px 2px rgba(80,80,120,0.04);"
                                onmouseenter="this.style.background='#93c5fd'" onmouseleave="this.style.background='#dbeafe'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#2563eb" width="20" height="20">
                                    <rect x="9" y="9" width="13" height="13" rx="2" stroke="#2563eb" stroke-width="2" fill="none"/>
                                    <rect x="3" y="3" width="13" height="13" rx="2" stroke="#2563eb" stroke-width="2" fill="none"/>
                                </svg>
                            </button>
                        </div>
                    `;
                    // Copy password button functionality
                    const copyBtn = actionCell.querySelector('#copyPasswordBtn');
                    if (copyBtn) {
                        copyBtn.addEventListener('click', function() {
                            // Always copy the actual password, not the dots
                            if (passwordValue && passwordValue !== '-') {
                                navigator.clipboard.writeText(passwordValue).then(() => {
                                    // Show notification below modal
                                    let notif = document.getElementById('copyPasswordNotif');
                                    if (!notif) {
                                        notif = document.createElement('div');
                                        notif.id = 'copyPasswordNotif';
                                        notif.style.position = 'fixed';
                                        notif.style.left = '50%';
                                        notif.style.top = 'calc(50% + 180px)';
                                        notif.style.transform = 'translateX(-50%)';
                                        notif.style.background = '#2563eb';
                                        notif.style.color = 'white';
                                        notif.style.padding = '10px 24px';
                                        notif.style.borderRadius = '8px';
                                        notif.style.fontSize = '1rem';
                                        notif.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
                                        notif.style.zIndex = '9999';
                                        notif.textContent = 'Password copied successfully!';
                                        document.body.appendChild(notif);
                                    } else {
                                        notif.textContent = 'Password copied successfully!';
                                        notif.style.display = '';
                                    }
                                    setTimeout(() => {
                                        if (notif) notif.style.display = 'none';
                                    }, 1500);
                                });
                            }
                        });
                    }
                    // Always start hidden
                    passwordCell.querySelector('#assessmentPassword').textContent = passwordValue ? '•'.repeat(passwordValue.length) : '-';
                    const toggleBtn = actionCell.querySelector('#togglePasswordBtn');
                    const passwordSpan = passwordCell.querySelector('#assessmentPassword');
                    let revealed = false;
                    toggleBtn.addEventListener('mouseenter', function() {
                        toggleBtn.style.background = '#ddd6fe';
                    });
                    toggleBtn.addEventListener('mouseleave', function() {
                        toggleBtn.style.background = '#ede9fe';
                    });
                    toggleBtn.addEventListener('click', function() {
                        revealed = !revealed;
                        if (revealed) {
                            passwordSpan.textContent = passwordValue;
                            // Eye open (show password)
                            actionCell.querySelector('#eyeIcon').outerHTML = `
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#6d28d9" width="22" height="22">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            `;
                        } else {
                            passwordSpan.textContent = passwordValue ? '•'.repeat(passwordValue.length) : '-';
                            // Eye closed (hide password)
                            actionCell.querySelector('#eyeIcon').outerHTML = `
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#6d28d9" width="22" height="22">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m3.249-2.383A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.043 5.306M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                </svg>
                            `;
                        }
                    });
                    // Show modal
                    modal.classList.remove('hidden');
                    // Attach close event just like category modal
                    const closeBtn = modal.querySelector('#closeAssessmentInfoModal');
                    closeBtn.onclick = null;
                    closeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        modal.classList.add('hidden');
                    });
                } else {
                    alert('Failed to load assessment details.');
                }
            })
            .catch(() => {
                alert('Error loading assessment details.');
            });
        }

        // Attach event listeners to assessment name links
        function initializeAssessmentInfoLinks() {
            document.querySelectorAll('.assessment-info-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const eventId = this.getAttribute('data-event-id');
                    showAssessmentInfoModal(eventId);
                });
            });
        }

        // Re-attach after AJAX table reloads
        function afterTableReload() {
            initializeRowCheckboxes();
            initializeAssessmentInfoLinks();
        }

        // Patch AJAX table reloads to call afterTableReload
        const origPerformFilteredSearch = performFilteredSearch;
        performFilteredSearch = function() {
            origPerformFilteredSearch();
            setTimeout(afterTableReload, 500);
        };
        const origClearAllFiltersAction = clearAllFiltersAction;
        clearAllFiltersAction = function() {
            origClearAllFiltersAction();
            setTimeout(afterTableReload, 500);
        };

        // Initial attach
        initializeAssessmentInfoLinks();

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
            const currentRowCheckboxes = document.querySelectorAll('.row-checkbox');
            const visibleCheckboxes = Array.from(currentRowCheckboxes).filter(cb => 
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
            const currentRowCheckboxes = document.querySelectorAll('.row-checkbox');
            const visibleCheckboxes = Array.from(currentRowCheckboxes).filter(cb => 
                cb.closest('tr').style.display !== 'none'
            );
            const anyChecked = visibleCheckboxes.some(cb => cb.checked);
            if (bulkDeleteBtn) {
                bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
            }
        }

        // Initialize row checkboxes after AJAX content update
        function initializeRowCheckboxes() {
            // Remove old event listeners
            const oldCheckboxes = document.querySelectorAll('.row-checkbox');
            oldCheckboxes.forEach(cb => {
                cb.removeEventListener('change', handleRowCheckboxChange);
            });
            
            // Get new checkboxes and add event listeners
            const newCheckboxes = document.querySelectorAll('.row-checkbox');
            newCheckboxes.forEach(cb => {
                cb.addEventListener('change', handleRowCheckboxChange);
            });
            
            // Update select all state
            updateSelectAllState();
            updateBulkDeleteVisibility();
            
            // Re-initialize dropdown menus for new content
            document.querySelectorAll('.dropdown').forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');

                // Remove old listeners by cloning
                const newToggle = toggle.cloneNode(true);
                toggle.parentNode.replaceChild(newToggle, toggle);

                newToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
                    menu.classList.toggle('hidden');
                });
            });
        }

        // Handle row checkbox change
        function handleRowCheckboxChange() {
            updateSelectAllState();
            updateBulkDeleteVisibility();
        }

        // Checkbox management
        if (selectAll) {
            selectAll.addEventListener('change', () => {
                const visibleCheckboxes = Array.from(document.querySelectorAll('.row-checkbox')).filter(cb => 
                    cb.closest('tr').style.display !== 'none'
                );
                visibleCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                updateBulkDeleteVisibility();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', handleRowCheckboxChange);
        });

        // Bulk delete functionality
        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                const eventIds = Array.from(checkedBoxes).map(cb => parseInt(cb.getAttribute('data-event-id'))).filter(id => !isNaN(id));
                
                if (eventIds.length === 0) {
                    alert('Please select assessments to delete.');
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
                        alert(data.message || 'Assessments deleted successfully!');
                        location.reload();
                    } else {
                        alert(data.message || 'Error deleting assessments');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting assessments');
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
            var passwordInput = document.getElementById('edit_event_password');
            var password = passwordInput.value.trim();
            var errorMsg = '';
            if (!password) {
                errorMsg = 'Password is required.';
            } else if (password.length < 8) {
                errorMsg = 'Password must be at least 8 characters.';
            } else if (!/[A-Z]/.test(password)) {
                errorMsg = 'Password must contain at least one uppercase letter.';
            } else if (!/[a-z]/.test(password)) {
                errorMsg = 'Password must contain at least one lowercase letter.';
            } else if (!/[0-9]/.test(password)) {
                errorMsg = 'Password must contain at least one number.';
            } else if (!/[!@#$%^&*(),.?":{}|<>\[\]\\/~`_+=;'\-]/.test(password)) {
                errorMsg = 'Password must contain at least one special character.';
            } else if (/\s/.test(password)) {
                errorMsg = 'Password must not contain spaces.';
            }
            if (errorMsg) {
                e.preventDefault();
                alert(errorMsg);
                passwordInput.focus();
                return false;
            }
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

        // Export to Excel functionality (uses current filters)
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
    let addModalSelectedTopics = new Map(); // For the Add Event Modal
    let editModalSelectedTopics = new Map(); // For the Edit Event Modal

    function editEvent(eventId, eventName, eventCode, questionLimit, duration, startDate, endDate) {
        currentEventId = eventId;
        editModalSelectedTopics.clear(); // Clear previous state

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

                    // Set password field
                    document.getElementById('edit_event_password').value = data.event.EventPassword || '';

                    // Set category
                    const categorySelect = document.getElementById('edit_category');
                    categorySelect.value = data.event.CategoryID || '';

                    // Populate the editModalSelectedTopics map
                    if (data.selected_topic_ids && data.topic_names) {
                        data.selected_topic_ids.forEach((id, index) => {
                            editModalSelectedTopics.set(String(id), data.topic_names[index]);
                        });
                    }
                    renderEditSelectedTopicTags();

                    // Load category topics if category is selected
                    if (data.event.CategoryID) {
                        loadCategoryTopics(data.event.CategoryID);
                    }

                    // Show modal
                    document.getElementById('editEventModal').classList.remove('hidden');
                } else {
                    alert('Error loading assessment details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading assessment details.');
            });
    }

    function loadCategoryTopics(categoryId) {
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
                            const isSelected = editModalSelectedTopics.has(String(topic.TopicID));
                            const div = document.createElement('div');
                            div.className = 'flex items-center mb-2';
                            div.innerHTML = `
                                <input type="checkbox" 
                                       id="event_topic_${topic.TopicID}" 
                                       name="edit_selected_topic_ids[]" 
                                       value="${topic.TopicID}"
                                       data-topic-name="${topic.TopicName}"
                                       ${isSelected ? 'checked' : ''}
                                       class="event-topic-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                                <label for="event_topic_${topic.TopicID}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    ${topic.TopicName} (ID: ${topic.TopicID})
                                </label>
                            `;
                            container.appendChild(div);
                        });

                        // Add event listeners
                        document.querySelectorAll('.event-topic-checkbox').forEach(checkbox => {
                            checkbox.removeEventListener('change', handleEditTopicCheckboxChange);
                            checkbox.addEventListener('change', handleEditTopicCheckboxChange);
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
        editModalSelectedTopics.clear();
        renderEditSelectedTopicTags();
    }

    function openAddModal() {
        document.getElementById('addEventModal').classList.remove('hidden');
        // Reset form
        document.getElementById('addEventForm').reset();
        document.getElementById('add-topics-section').classList.add('hidden');
        document.getElementById('addEventTopicsContainer').innerHTML = '';
        addModalSelectedTopics.clear(); // Reset selected topics
        renderSelectedTopicTags(); // Clear the tags display
    }

    function closeAddModal() {
        document.getElementById('addEventModal').classList.add('hidden');
        document.getElementById('add-topics-section').classList.add('hidden');
        document.getElementById('addEventTopicsContainer').innerHTML = '';
        // Reset form and state
        document.getElementById('addEventForm').reset();
        addModalSelectedTopics.clear();
        renderSelectedTopicTags();
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
                            const isSelected = addModalSelectedTopics.has(String(topic.TopicID));
                            const div = document.createElement('div');
                            div.className = 'flex items-center mb-2';
                            div.innerHTML = `
                                <input type="checkbox" 
                                       id="add_event_topic_${topic.TopicID}" 
                                       name="add_selected_topic_ids[]" 
                                       value="${topic.TopicID}"
                                       data-topic-name="${topic.TopicName}"
                                       ${isSelected ? 'checked' : ''}
                                       class="add-event-topic-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                                <label for="add_event_topic_${topic.TopicID}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    ${topic.TopicName} (ID: ${topic.TopicID})
                                </label>
                            `;
                            container.appendChild(div);
                        });

                        // Add event listeners to the new checkboxes
                        document.querySelectorAll('.add-event-topic-checkbox').forEach(checkbox => {
                            checkbox.removeEventListener('change', handleAddTopicCheckboxChange); // Prevent duplicates
                            checkbox.addEventListener('change', handleAddTopicCheckboxChange);
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

    function handleAddTopicCheckboxChange(event) {
        const checkbox = event.target;
        const topicId = checkbox.value;
        const topicName = checkbox.dataset.topicName;

        if (checkbox.checked) {
            addModalSelectedTopics.set(topicId, topicName);
        } else {
            addModalSelectedTopics.delete(topicId);
        }
        renderSelectedTopicTags();
    }

    function renderSelectedTopicTags() {
        const container = document.getElementById('selected-topics-tags-container');
        container.innerHTML = ''; // Clear existing tags
        if (addModalSelectedTopics.size === 0) {
            container.innerHTML = '<span class="text-xs text-gray-500 dark:text-gray-400">No topics selected</span>';
        } else {
            addModalSelectedTopics.forEach((name, id) => {
                const tag = document.createElement('div');
                tag.className = 'bg-violet-500 text-white text-xs font-medium px-2.5 py-1 rounded-full flex items-center gap-2';
                tag.innerHTML = `
                    <span>${name}</span>
                    <button type="button" class="text-violet-200 hover:text-white" onclick="removeSelectedTopic('${id}')">&times;</button>
                `;
                container.appendChild(tag);
            });
        }
    }

    function removeSelectedTopic(topicId) {
        addModalSelectedTopics.delete(String(topicId));
        renderSelectedTopicTags();
        
        // Uncheck the corresponding checkbox if it's visible in the DOM
        const checkbox = document.getElementById(`add_event_topic_${topicId}`);
        if (checkbox) {
            checkbox.checked = false;
        }
    }

    function addEvent() {
        const formData = new FormData(document.getElementById('addEventForm'));
        const data = Object.fromEntries(formData);

        // Password validation (moved from DOMContentLoaded)
        var passwordInput = document.getElementById('add_event_password');
        var password = passwordInput ? passwordInput.value.trim() : '';
        var errorMsg = '';
        if (!password) {
            errorMsg = 'Password is required.';
        } else if (password.length < 8) {
            errorMsg = 'Password must be at least 8 characters.';
        } else if (!/[A-Z]/.test(password)) {
            errorMsg = 'Password must contain at least one uppercase letter.';
        } else if (!/[a-z]/.test(password)) {
            errorMsg = 'Password must contain at least one lowercase letter.';
        } else if (!/[0-9]/.test(password)) {
            errorMsg = 'Password must contain at least one number.';
        } else if (!/[!@#$%^&*(),.?":{}|<>\[\]\\/~`_+=;'-]/.test(password)) {
            errorMsg = 'Password must contain at least one special character.';
        } else if (/\s/.test(password)) {
            errorMsg = 'Password must not contain spaces.';
        }
        if (errorMsg) {
            alert(errorMsg);
            if (passwordInput) passwordInput.focus();
            return false;
        }

        // Validate dates
        const startDate = new Date(data.StartDate);
        const endDate = new Date(data.EndDate);

        if (startDate >= endDate) {
            alert('End date must be after start date.');
            return;
        }

        // Get selected topic IDs from our map
        const selectedTopics = Array.from(addModalSelectedTopics.keys());

        // Validate that at least one topic is selected
        if (selectedTopics.length === 0) {
            alert('Please select at least one topic for the assessment.');
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
                alert(data.message || 'Assessment added successfully!');
                closeAddModal();
                location.reload(); // Refresh the page to show new assessment
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
            let errorMessage = 'An error occurred while adding the assessment.';
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
                    errorMessage = 'An error occurred while adding the assessment.';
                }
            }
            alert(errorMessage);
        });
    }

    function handleEditTopicCheckboxChange(event) {
        const checkbox = event.target;
        const topicId = checkbox.value;
        const topicName = checkbox.dataset.topicName;

        if (checkbox.checked) {
            editModalSelectedTopics.set(topicId, topicName);
        } else {
            editModalSelectedTopics.delete(topicId);
        }
        renderEditSelectedTopicTags();
    }

    function renderEditSelectedTopicTags() {
        const container = document.getElementById('edit-selected-topics-tags-container');
        container.innerHTML = ''; // Clear existing tags
        if (editModalSelectedTopics.size === 0) {
            container.innerHTML = '<span class="text-xs text-gray-500 dark:text-gray-400">No topics selected</span>';
        } else {
            editModalSelectedTopics.forEach((name, id) => {
                const tag = document.createElement('div');
                tag.className = 'bg-violet-500 text-white text-xs font-medium px-2.5 py-1 rounded-full flex items-center gap-2';
                tag.innerHTML = `
                    <span>${name}</span>
                    <button type="button" class="text-violet-200 hover:text-white" onclick="removeEditSelectedTopic('${id}')">&times;</button>
                `;
                container.appendChild(tag);
            });
        }
    }

    function removeEditSelectedTopic(topicId) {
        editModalSelectedTopics.delete(String(topicId));
        renderEditSelectedTopicTags();
        
        // Uncheck the corresponding checkbox if it's visible in the DOM
        const checkbox = document.getElementById(`event_topic_${topicId}`);
        if (checkbox) {
            checkbox.checked = false;
        }
    }

    function updateEvent() {
        if (!currentEventId) return;

        const formData = new FormData(document.getElementById('editEventForm'));
        const data = Object.fromEntries(formData);
        
        // Get selected topic IDs from our map
        const selectedTopics = Array.from(editModalSelectedTopics.keys());
        
        // Validate that at least one topic is selected
        if (selectedTopics.length === 0) {
            alert('Please select at least one topic for the assessment.');
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
            alert('An error occurred while updating the assessment.');
        });
    }

    function deleteEvent(eventId) {
        if (!confirm('Are you sure you want to delete this assessment? This action cannot be undone.')) {
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
            alert('An error occurred while deleting the assessment.');
        });
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const editModal = document.getElementById('editEventModal');
        const addModal = document.getElementById('addEventModal');
        const weightageModal = document.getElementById('weightageModal');
        
        if (event.target === editModal) {
            closeEditModal();
        }
        
        if (event.target === addModal) {
            closeAddModal();
        }

        if (event.target === weightageModal) {
            closeWeightageModal();
        }
    });

    let currentWeightageEventId = null;

    function openWeightageModal(eventId) {
    currentWeightageEventId = eventId;
    const modal = document.getElementById('weightageModal');
    const container = document.getElementById('weightageTopicsContainer');
    container.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading topics...</div>';
    modal.classList.remove('hidden');

    fetch(`/events/${eventId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                container.innerHTML = '';
                let weightages = {};
                
                // Better handling of weightages data
                if (data.event.TopicWeightages) {
                    try {
                        if (typeof data.event.TopicWeightages === 'string') {
                            weightages = JSON.parse(data.event.TopicWeightages);
                        } else if (typeof data.event.TopicWeightages === 'object') {
                            weightages = data.event.TopicWeightages;
                        }
                    } catch (e) {
                        console.error("Failed to parse TopicWeightages:", e);
                        weightages = {};
                    }
                }

                if (data.topic_names && data.topic_names.length > 0) {
                    data.selected_topic_ids.forEach((topicId, index) => {
                        const topicName = data.topic_names[index];
                        const weightage = weightages[topicId] || 0;
                        const div = document.createElement('div');
                        div.className = 'flex items-center justify-between py-2';
                        div.innerHTML = `
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 flex-1">
                                ${topicName}
                                <span class="text-xs text-gray-500 ml-1">(ID: ${topicId})</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" 
                                       name="weightages[${topicId}]" 
                                       value="${weightage}" 
                                       min="0" 
                                       max="100" 
                                       step="1"
                                       class="w-20 px-2 py-1 border border-gray-300 rounded-md text-center dark:bg-zinc-700 dark:border-zinc-600 dark:text-white" 
                                       oninput="updateTotalWeightage()">
                                <span class="text-sm text-gray-500">%</span>
                            </div>
                        `;
                        container.appendChild(div);
                    });
                } else {
                    container.innerHTML = '<div class="text-center py-4 text-gray-500 dark:text-gray-400">No topics found for this event.</div>';
                }
                updateTotalWeightage();
            } else {
                container.innerHTML = '<div class="text-center py-4 text-red-500">Error loading topics</div>';
                console.error('Error loading event details:', data.message);
            }
        })
        .catch(error => {
            container.innerHTML = '<div class="text-center py-4 text-red-500">Error loading topics</div>';
            console.error('Error:', error);
        });
}

    function closeWeightageModal() {
        document.getElementById('weightageModal').classList.add('hidden');
        currentWeightageEventId = null;
    }

    function updateTotalWeightage() {
    const inputs = document.querySelectorAll('#weightageTopicsContainer input[type="number"]');
    let total = 0;
    let hasWeightages = false;
    let hasZero = false;

    inputs.forEach(input => {
        const val = parseInt(input.value) || 0;
        if (val > 0) {
            hasWeightages = true;
        } else if (hasWeightages) { // Only consider zero a problem if other weightages are set
            hasZero = true;
        }
        total += val;
    });

    const totalEl = document.getElementById('totalWeightage');
    const progressBar = document.getElementById('weightageProgressBar');
    const validationMsg = document.getElementById('weightageValidationMessage');
    const saveBtn = document.getElementById('saveWeightageBtn');

    totalEl.textContent = total + '%';

    const progress = Math.min(total, 100);
    progressBar.style.width = progress + '%';

    // Reset styles
    totalEl.classList.remove('text-red-500');
    progressBar.classList.remove('bg-red-500');
    progressBar.classList.add('bg-violet-600');
    validationMsg.textContent = '';
    saveBtn.disabled = false;
    saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');

    let error = false;

    if (total > 100) {
        totalEl.classList.add('text-red-500');
        progressBar.classList.add('bg-red-500');
        progressBar.classList.remove('bg-violet-600');
        validationMsg.textContent = 'Total weightage cannot exceed 100%.';
        error = true;
    } else if (hasWeightages && total !== 100) {
        validationMsg.textContent = 'Total weightage must be exactly 100% if you set any weightages.';
        error = true;
    } else if (hasWeightages && hasZero) {
        validationMsg.textContent = 'If you set any weightages, all topics must have at least 1% weightage.';
        error = true;
    } else if (hasWeightages && total === 100) {
        validationMsg.textContent = 'Perfect! All weightages are set correctly.';
        validationMsg.className = 'text-green-500 text-xs mt-2 h-4';
    }

    if (error) {
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
        validationMsg.className = 'text-red-500 text-xs mt-2 h-4';
    }
}
   document.getElementById('weightageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentWeightageEventId) {
        alert('No event selected for weightage update.');
        return;
    }

    const saveBtn = document.getElementById('saveWeightageBtn');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    saveBtn.disabled = true;

    // Get all weightage inputs directly
    const inputs = document.querySelectorAll('#weightageTopicsContainer input[type="number"]');
    const weightages = {};
    let totalWeightage = 0;
    let hasWeightages = false;
    let hasZero = false;

    // Process inputs directly
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name && name.includes('weightages[')) {
            // Extract topic ID from name like "weightages[123]"
            const matches = name.match(/weightages\[(\d+)\]/);
            if (matches) {
                const topicId = matches[1];
                const weight = parseInt(input.value) || 0;
                weightages[topicId] = weight;
                totalWeightage += weight;
                
                if (weight > 0) {
                    hasWeightages = true;
                } else if (hasWeightages) {
                    hasZero = true;
                }
            }
        }
    });

    console.log('Collected weightages:', weightages);
    console.log('Total weightage:', totalWeightage);
    console.log('Has weightages:', hasWeightages);
    console.log('Has zero:', hasZero);

    // Client-side validation
    if (hasWeightages) {
        if (hasZero) {
            alert('If you set any weightages, all topics must have at least 1% weightage.');
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
            return;
        }
        if (totalWeightage !== 100) {
            alert('Total weightage must be exactly 100% if you set any weightages.');
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
            return;
        }
    }

    // Check if we have any weightages to save
    if (Object.keys(weightages).length === 0) {
        alert('No weightages found to save.');
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        return;
    }

    console.log('Sending request to:', `/events/${currentWeightageEventId}/weightages`);
    console.log('Sending weightages:', weightages);

    fetch(`/events/${currentWeightageEventId}/weightages`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ weightages })
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response text:', text);
                throw new Error(`HTTP ${response.status}: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Success response:', data);
        if (data.success) {
            alert(data.message || 'Weightages saved successfully!');
            closeWeightageModal();
            
            // Optional: Reload the page to reflect changes
            // location.reload();
        } else {
            alert(data.message || 'Error saving weightages');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('An error occurred while saving weightages: ' + error.message);
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
});
// No longer disabling Add Assessment button based on password field

// Generate password for edit modal
function generateStrongPassword(length = 10) {
    const upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const lower = 'abcdefghijklmnopqrstuvwxyz';
    const number = '0123456789';
    const special = '!@#$%^&*()_+[]{}|;:,.<>?/~`-=';
    const all = upper + lower + number + special;
    let password = '';
    // Ensure at least one of each required character type
    password += upper[Math.floor(Math.random() * upper.length)];
    password += lower[Math.floor(Math.random() * lower.length)];
    password += number[Math.floor(Math.random() * number.length)];
    password += special[Math.floor(Math.random() * special.length)];
    // Fill the rest
    for (let i = 4; i < length; ++i) {
        password += all[Math.floor(Math.random() * all.length)];
    }
    // Shuffle to avoid predictable positions
    password = password.split('').sort(() => 0.5 - Math.random()).join('');
    // Ensure no spaces
    if (/\s/.test(password)) {
        return generateStrongPassword(length);
    }
    return password;
}

function generateEditEventPassword() {
    var passwordInput = document.getElementById('edit_event_password');
    var password = generateStrongPassword(10);
    if (passwordInput) {
        passwordInput.value = password;
    }
}

function generateEventPassword() {
    var passwordInput = document.getElementById('add_event_password');
    var addBtn = document.getElementById('addAssessmentBtn');
    var password = generateStrongPassword(10);
    if (passwordInput) {
        passwordInput.value = password;
        if (addBtn) addBtn.disabled = false;
    }
    toggleAddBtn();
}
// Password validation for add modal is now handled inside addEvent()
</script>
