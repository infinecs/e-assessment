@extends('layout.appMain')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Custom styles for checkboxes and radio buttons -->
<style>
.row-checkbox:checked,
#checkbox-all:checked,
.topic-filter-checkbox:checked {
    background-color: #7c3aed !important; /* violet-600 */
    border-color: #7c3aed !important;
}

.row-checkbox:checked:after,
#checkbox-all:checked:after,
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
            Assessment Questions
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
                            Assessment Questions
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
                    <input type="text" id="searchInput" placeholder="Search questions by text..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm w-64 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
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
                <button type="button" id="add-question-btn"
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
                            <th class="px-2 py-1.5 text-left">Question Text</th>
                            <th class="px-2 py-1.5">Default Topic</th>
                            <th class="px-2 py-1.5">Actions</th>
                            <th class="px-2 py-1.5">Date Created</th>
                            <th class="px-2 py-1.5">Date Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $row)
                            <tr class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:hover:bg-zinc-700/50 dark:border-zinc-600"
                                data-question-id="{{ $row->QuestionID }}"
                                data-default-topic="{{ $row->DefaultTopic ?? '' }}">
                                <td class="w-4 p-3">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white"
                                            data-question-id="{{ $row->QuestionID }}">
                                    </div>
                                </td>
                                <td class="px-2 py-1.5 text-left">
                                    <button type="button" 
                                        class="text-blue-600 hover:text-blue-800 hover:underline text-left question-btn"
                                        data-question-id="{{ $row->QuestionID }}"
                                        data-question-text="{{ $row->QuestionText }}">
                                        {{ $row->QuestionText }}
                                    </button>
                                </td>

                                <!-- Show Default Topic ID -->
                                <td class="px-2 py-1.5">{{ $row->DefaultTopic }}</td>

                                <!-- Actions -->
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
                                                    class="edit-question-btn w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700"
                                                    data-question-id="{{ $row->QuestionID }}">
                                                    <i class="mdi mdi-pencil text-base"></i>
                                                    <span>Edit</span>
                                                </button>
                                                <button type="button"
                                                    class="delete-question-btn w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700"
                                                    data-question-id="{{ $row->QuestionID }}">
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
                                <td colspan="7" class="px-2 py-1.5 text-center">No questions found</td>
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

<!-- Question Answers Modal -->
<div id="question-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl w-full max-w-4xl mx-4 overflow-hidden" style="max-height: 90vh;">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-600 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Question Answers</h3>
            <button type="button" id="close-modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="mdi mdi-close text-xl"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="px-6 py-4 overflow-y-auto" style="max-height: 70vh;">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question:</label>
                <p id="modal-question-text" class="text-gray-900 dark:text-white bg-gray-50 dark:bg-zinc-700 p-3 rounded"></p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Answer Choices:</label>
                <div id="answers-container" class="space-y-3">
                    <!-- Answers will be loaded here -->
                </div>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-600 flex justify-end gap-3">
            <button type="button" id="cancel-btn" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                Cancel
            </button>
            <button type="button" id="save-btn" class="px-4 py-2 text-sm text-white bg-violet-600 rounded hover:bg-violet-700">
                Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div id="edit-question-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-600 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Question</h3>
            <button type="button" id="close-edit-modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="mdi mdi-close text-xl"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
            <form id="edit-question-form" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text</label>
                    <textarea id="edit-question-text" name="QuestionText" rows="4" 
                        class="w-full p-3 border border-gray-300 dark:border-zinc-600 rounded-lg text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                        placeholder="Enter question text" required></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Default Topic</label>
                    <div class="relative">
                        <input type="text" id="topic-search" 
                            class="w-full p-3 border border-gray-300 dark:border-zinc-600 rounded-lg text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                            placeholder="Type topic name or ID to search and select topics..." autocomplete="off">
                        
                        <!-- Dropdown for topic selection -->
                        <div id="topic-dropdown" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            @foreach($allTopics as $topic)
                                <div class="topic-option p-3 hover:bg-gray-100 dark:hover:bg-zinc-700 cursor-pointer border-b border-gray-100 dark:border-zinc-600 last:border-b-0" 
                                     data-topic-id="{{ $topic->TopicID }}" data-topic-name="{{ $topic->TopicName }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $topic->TopicName }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $topic->TopicID }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Selected topics display -->
                        <div id="selected-topics" class="mt-3 flex flex-wrap gap-2 min-h-[2rem]"></div>
                        
                        <!-- Hidden inputs for selected topics -->
                        <div id="selected-topics-inputs"></div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-600 flex justify-end gap-3">
            <button type="button" id="cancel-edit-btn" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                Cancel
            </button>
            <button type="button" id="save-edit-btn" class="px-4 py-2 text-sm text-white bg-violet-600 rounded hover:bg-violet-700">
                Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div id="add-question-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl w-full max-w-4xl mx-4 overflow-hidden" style="max-height: 90vh;">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-600 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add New Question</h3>
            <button type="button" id="close-add-modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="mdi mdi-close text-xl"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="px-6 py-4 overflow-y-auto" style="max-height: 70vh;">
            <form id="add-question-form" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text</label>
                    <textarea id="add-question-text" name="QuestionText" rows="4" 
                        class="w-full p-3 border border-gray-300 dark:border-zinc-600 rounded-lg text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                        placeholder="Enter question text" required></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Default Topic</label>
                    <div class="relative">
                        <input type="text" id="add-topic-search" 
                            class="w-full p-3 border border-gray-300 dark:border-zinc-600 rounded-lg text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                            placeholder="Type topic name or ID to search and select topics..." autocomplete="off">
                        
                        <!-- Dropdown for topic selection -->
                        <div id="add-topic-dropdown" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            @foreach($allTopics as $topic)
                                <div class="add-topic-option p-3 hover:bg-gray-100 dark:hover:bg-zinc-700 cursor-pointer border-b border-gray-100 dark:border-zinc-600 last:border-b-0" 
                                     data-topic-id="{{ $topic->TopicID }}" data-topic-name="{{ $topic->TopicName }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $topic->TopicName }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $topic->TopicID }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Selected topics display -->
                        <div id="add-selected-topics" class="mt-3 flex flex-wrap gap-2 min-h-[2rem]"></div>
                        
                        <!-- Hidden inputs for selected topics -->
                        <div id="add-selected-topics-inputs"></div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Answer Choices</label>
                    <div id="add-answers-container" class="space-y-3">
                            <div class="answer-choice border rounded-lg p-3 bg-gray-50 dark:bg-zinc-700">
                            <div class="flex items-start gap-3">
                                <div class="flex items-center">
                                    <input type="radio" name="add_correct_answer" value="0" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-medium text-sm">A.</span>
                                        <span class="text-xs text-gray-500">Mark as correct answer</span>
                                        <span class="text-xs text-red-500 font-medium">Required</span>
                                    </div>
                                    <textarea class="w-full p-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                                              rows="2" placeholder="Enter answer choice A" required></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="answer-choice border rounded-lg p-3 bg-gray-50 dark:bg-zinc-700">
                            <div class="flex items-start gap-3">
                                <div class="flex items-center">
                                    <input type="radio" name="add_correct_answer" value="1" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-medium text-sm">B.</span>
                                        <span class="text-xs text-gray-500">Mark as correct answer</span>
                                        <span class="text-xs text-red-500 font-medium">Required</span>
                                    </div>
                                    <textarea class="w-full p-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                                              rows="2" placeholder="Enter answer choice B" required></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="answer-choice border rounded-lg p-3 bg-gray-50 dark:bg-zinc-700">
                            <div class="flex items-start gap-3">
                                <div class="flex items-center">
                                    <input type="radio" name="add_correct_answer" value="2" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-medium text-sm">C.</span>
                                        <span class="text-xs text-gray-500">Mark as correct answer</span>
                                        <span class="text-xs text-blue-500 font-medium">Optional</span>
                                    </div>
                                    <textarea class="w-full p-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                                              rows="2" placeholder="Enter answer choice C (optional)"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="answer-choice border rounded-lg p-3 bg-gray-50 dark:bg-zinc-700">
                            <div class="flex items-start gap-3">
                                <div class="flex items-center">
                                    <input type="radio" name="add_correct_answer" value="3" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-medium text-sm">D.</span>
                                        <span class="text-xs text-gray-500">Mark as correct answer</span>
                                        <span class="text-xs text-blue-500 font-medium">Optional</span>
                                    </div>
                                    <textarea class="w-full p-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                                              rows="2" placeholder="Enter answer choice D (optional)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-600 flex justify-end gap-3">
            <button type="button" id="cancel-add-btn" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                Cancel
            </button>
            <button type="button" id="save-add-btn" class="px-4 py-2 text-sm text-white bg-violet-600 rounded hover:bg-violet-700">
                Add Question
            </button>
        </div>
    </div>
</div>

<style>
/* Selected topic tags styling */
.selected-topic-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background-color: #8b5cf6;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.selected-topic-tag .remove-topic {
    cursor: pointer;
    font-weight: bold;
    padding: 0.125rem;
    border-radius: 50%;
    width: 1rem;
    height: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}

.selected-topic-tag .remove-topic:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

.topic-option.selected {
    background-color: #8b5cf6 !important;
    color: white !important;
}
</style>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('checkbox-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
        const modal = document.getElementById('question-modal');
        const closeModal = document.getElementById('close-modal');
        const cancelBtn = document.getElementById('cancel-btn');
        const saveBtn = document.getElementById('save-btn');
        const modalQuestionText = document.getElementById('modal-question-text');
        const answersContainer = document.getElementById('answers-container');
        
        // Search elements
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('tbody tr');
        const topicFilterBtn = document.getElementById('topicFilterBtn');
        const topicDropdownFilter = document.getElementById('topicDropdownFilter');
        const topicListFilter = document.getElementById('topicListFilter');
        const topicSearchFilter = document.getElementById('topicSearchFilter');
        const topicCount = document.getElementById('topicCount');
        const clearTopics = document.getElementById('clearTopics');
        const performSearchBtn = document.getElementById('performSearchBtn');
        const clearAllFilters = document.getElementById('clearAllFilters');
        
        // Search state
        let allTopics = [];
        let selectedTopicsForSearch = new Set();
        let searchTimeout = null;

        // Debounced search function
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performFilteredSearch();
            }, 300);
        }

        // Load topics for filters
        function loadFiltersData() {
            console.log('Loading topics for question filters...');
            
            try {
                // Load topics from the page data
                const topics = @json($allTopics ?? []);
                allTopics = topics;
                console.log('Loaded topics:', allTopics); // Debug log
                renderTopicList();
                console.log('Filter data loaded successfully!');
            } catch (error) {
                console.error('Error loading filter data:', error);
            }
        }

        // Render topic list
        function renderTopicList() {
            console.log('Rendering topic list, topics count:', allTopics.length); // Debug log
            const searchTerm = topicSearchFilter.value.toLowerCase();
            const filteredTopics = allTopics.filter(topic => 
                topic.TopicName.toLowerCase().includes(searchTerm) || 
                topic.TopicID.toString().includes(searchTerm)
            );

            console.log('Filtered topics count:', filteredTopics.length); // Debug log
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
            
            console.log('Topic list rendered with', filteredTopics.length, 'items'); // Debug log
        }

        // Handle topic selection
        function handleTopicChange(event) {
            const topicId = event.target.value;
            
            if (event.target.checked) {
                selectedTopicsForSearch.add(topicId);
            } else {
                selectedTopicsForSearch.delete(topicId);
            }
            
            updateTopicDisplay();
        }

        // Update topic display
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
            const hasFilters = selectedTopicsForSearch.size > 0 || searchInput.value.trim();
            clearAllFilters.classList.toggle('hidden', !hasFilters);
        }

        // Perform search with filters
        function performFilteredSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleRows = 0;

            tableRows.forEach(row => {
                if (row.children.length === 1 && row.children[0].getAttribute('colspan')) {
                    return; // Skip "No questions found" row
                }

                const questionText = row.children[1]?.textContent.toLowerCase() || '';
                const defaultTopic = row.dataset.defaultTopic || '';

                // Check text search
                const textMatch = !searchTerm || questionText.includes(searchTerm);

                // Check topic filter
                const topicMatch = selectedTopicsForSearch.size === 0 || selectedTopicsForSearch.has(defaultTopic);

                // Show row if all conditions match
                if (textMatch && topicMatch) {
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
            showNoResultsMessage(visibleRows === 0 && (searchTerm || selectedTopicsForSearch.size > 0));

            console.log(`Search results: ${visibleRows} questions found`);
        }

        // Clear all filters
        function clearAllFiltersAction() {
            // Clear text search
            searchInput.value = '';
            
            // Clear topic selections  
            selectedTopicsForSearch.clear();
            document.querySelectorAll('.topic-filter-checkbox').forEach(cb => cb.checked = false);
            updateTopicDisplay();
            
            // Close dropdowns
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
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No questions found</p>
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

        // Event listeners for search
        topicFilterBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Topic filter button clicked'); // Debug log
            console.log('Topics loaded:', allTopics.length); // Debug log
            topicDropdownFilter.classList.toggle('hidden');
            console.log('Dropdown visible:', !topicDropdownFilter.classList.contains('hidden')); // Debug log
        });

        clearTopics.addEventListener('click', () => {
            selectedTopicsForSearch.clear();
            document.querySelectorAll('.topic-filter-checkbox').forEach(cb => cb.checked = false);
            updateTopicDisplay();
        });

        topicSearchFilter.addEventListener('input', renderTopicList);
        performSearchBtn.addEventListener('click', performFilteredSearch);
        clearAllFilters.addEventListener('click', clearAllFiltersAction);
        
        // Real-time search as user types with debouncing
        searchInput.addEventListener('input', () => {
            updateClearAllButton();
            debounceSearch();
        });
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                performFilteredSearch();
            }
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!topicFilterBtn.contains(e.target) && !topicDropdownFilter.contains(e.target)) {
                topicDropdownFilter.classList.add('hidden');
            }
        });

        // Initialize search
        console.log('Initializing search functionality...'); // Debug log
        console.log('Search elements check:');
        console.log('- topicFilterBtn:', !!topicFilterBtn);
        console.log('- topicDropdownFilter:', !!topicDropdownFilter);
        console.log('- topicListFilter:', !!topicListFilter);
        console.log('- topicSearchFilter:', !!topicSearchFilter);
        loadFiltersData();
        console.log('Search initialization complete'); // Debug log
        
        // Edit modal elements
        const editModal = document.getElementById('edit-question-modal');
        const closeEditModal = document.getElementById('close-edit-modal');
        const cancelEditBtn = document.getElementById('cancel-edit-btn');
        const saveEditBtn = document.getElementById('save-edit-btn');
        const editQuestionForm = document.getElementById('edit-question-form');
        
        // Add modal elements
        const addModal = document.getElementById('add-question-modal');
        const addQuestionBtn = document.getElementById('add-question-btn');
        const closeAddModal = document.getElementById('close-add-modal');
        const cancelAddBtn = document.getElementById('cancel-add-btn');
        const saveAddBtn = document.getElementById('save-add-btn');
        const addQuestionForm = document.getElementById('add-question-form');
        
        // Topic dropdown elements
        const topicSearch = document.getElementById('topic-search');
        const topicDropdown = document.getElementById('topic-dropdown');
        const selectedTopicsContainer = document.getElementById('selected-topics');
        const selectedTopicsInputs = document.getElementById('selected-topics-inputs');
        
        // Add modal topic dropdown elements
        const addTopicSearch = document.getElementById('add-topic-search');
        const addTopicDropdown = document.getElementById('add-topic-dropdown');
        const addSelectedTopicsContainer = document.getElementById('add-selected-topics');
        const addSelectedTopicsInputs = document.getElementById('add-selected-topics-inputs');
        
        let currentQuestionId = null;
        let currentEditQuestionId = null;
        let selectedTopicsForEdit = new Set();
        let addSelectedTopics = new Set();

        function updateBulkDeleteVisibility() {
            const visibleCheckboxes = Array.from(rowCheckboxes).filter(cb => 
                cb.closest('tr').style.display !== 'none'
            );
            const anyChecked = visibleCheckboxes.some(cb => cb.checked);
            if (bulkDeleteBtn) {
                bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
            }
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

        // Bulk delete functionality
        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                const questionIds = Array.from(checkedBoxes).map(cb => cb.dataset.questionId);
                
                if (questionIds.length === 0) {
                    alert('Please select questions to delete.');
                    return;
                }
                
                const confirmMessage = `Are you sure you want to delete ${questionIds.length} question(s)? This will also delete all associated answers.`;
                if (!confirm(confirmMessage)) {
                    return;
                }
                
                // Perform bulk delete
                fetch('/question/bulk-delete', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ question_ids: questionIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error deleting questions: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting questions');
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
            topicDropdown.classList.add('hidden');
            addTopicDropdown.classList.add('hidden');
        });

        // Topic search functionality
        topicSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = topicDropdown.querySelectorAll('.topic-option');
            let hasVisibleOptions = false;
            
            options.forEach(option => {
                const topicName = option.dataset.topicName.toLowerCase();
                const topicId = option.dataset.topicId.toLowerCase();
                
                // Search by both topic name and topic ID
                if (topicName.includes(searchTerm) || topicId.includes(searchTerm)) {
                    option.style.display = 'block';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });
            
            if (this.value.length > 0 && hasVisibleOptions) {
                topicDropdown.classList.remove('hidden');
            } else {
                topicDropdown.classList.add('hidden');
            }
        });

        topicSearch.addEventListener('focus', function() {
            // Always show dropdown when focused, even if empty
            topicDropdown.classList.remove('hidden');
            
            // If empty, show all options
            if (this.value.length === 0) {
                const options = topicDropdown.querySelectorAll('.topic-option');
                options.forEach(option => {
                    option.style.display = 'block';
                });
            }
        });

        topicSearch.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        topicDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Topic selection - only allow one topic
        document.querySelectorAll('.topic-option').forEach(option => {
            option.addEventListener('click', function() {
                const topicId = this.dataset.topicId;
                const topicName = this.dataset.topicName;
                
                // Clear all previous selections first (single selection only)
                clearSelectedTopics();
                
                // Add the new selection
                selectedTopicsForEdit.add(topicId);
                addSelectedTopic(topicId, topicName);
                this.classList.add('selected');
                
                // Show the selected topic name in the input box
                topicSearch.value = topicName;
                topicDropdown.classList.add('hidden');
            });
        });

        function addSelectedTopic(topicId, topicName) {
            console.log('Adding selected topic:', topicId, topicName); // Debug log
            
            // Create selected topic tag
            const tag = document.createElement('div');
            tag.className = 'selected-topic-tag';
            tag.dataset.topicId = topicId;
            tag.style.display = 'inline-flex'; // Ensure visibility
            tag.innerHTML = `
                <span>${topicName}</span>
                <span class="remove-topic" data-topic-id="${topicId}">×</span>
            `;
            
            selectedTopicsContainer.appendChild(tag);
            console.log('Tag appended to container'); // Debug log
            
            // Create hidden input
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_topic_ids[]';
            input.value = topicId;
            input.dataset.topicId = topicId;
            
            selectedTopicsInputs.appendChild(input);
            
            // Add remove functionality
            tag.querySelector('.remove-topic').addEventListener('click', function() {
                removeSelectedTopic(topicId);
            });
            
            // Make sure the container is visible
            selectedTopicsContainer.style.display = 'flex';
        }

        function removeSelectedTopic(topicId) {
            selectedTopicsForEdit.delete(topicId);
            
            // Remove tag
            const tag = selectedTopicsContainer.querySelector(`[data-topic-id="${topicId}"]`);
            if (tag) tag.remove();
            
            // Remove hidden input
            const input = selectedTopicsInputs.querySelector(`[data-topic-id="${topicId}"]`);
            if (input) input.remove();
            
            // Remove selected class from option
            const option = topicDropdown.querySelector(`[data-topic-id="${topicId}"]`);
            if (option) option.classList.remove('selected');
            
            // Clear the input box since no topic is selected
            topicSearch.value = '';
        }

        function clearSelectedTopics() {
            selectedTopicsForEdit.clear();
            selectedTopicsContainer.innerHTML = '';
            selectedTopicsInputs.innerHTML = '';
            
            // Remove selected class from all options
            document.querySelectorAll('.topic-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Clear the input box
            topicSearch.value = '';
        }

        // Add modal topic functionality
        addTopicSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = addTopicDropdown.querySelectorAll('.add-topic-option');
            let hasVisibleOptions = false;
            
            options.forEach(option => {
                const topicName = option.dataset.topicName.toLowerCase();
                const topicId = option.dataset.topicId.toLowerCase();
                
                if (topicName.includes(searchTerm) || topicId.includes(searchTerm)) {
                    option.style.display = 'block';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });
            
            if (this.value.length > 0 && hasVisibleOptions) {
                addTopicDropdown.classList.remove('hidden');
            } else {
                addTopicDropdown.classList.add('hidden');
            }
        });

        addTopicSearch.addEventListener('focus', function() {
            addTopicDropdown.classList.remove('hidden');
            if (this.value.length === 0) {
                const options = addTopicDropdown.querySelectorAll('.add-topic-option');
                options.forEach(option => {
                    option.style.display = 'block';
                });
            }
        });

        addTopicSearch.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        addTopicDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Add modal topic selection
        document.querySelectorAll('.add-topic-option').forEach(option => {
            option.addEventListener('click', function() {
                const topicId = this.dataset.topicId;
                const topicName = this.dataset.topicName;
                
                clearAddSelectedTopics();
                addSelectedTopics.add(topicId);
                addSelectedTopic(topicId, topicName);
                this.classList.add('selected');
                addTopicSearch.value = topicName;
                addTopicDropdown.classList.add('hidden');
            });
        });

        function addSelectedTopic(topicId, topicName) {
            const tag = document.createElement('div');
            tag.className = 'selected-topic-tag';
            tag.dataset.topicId = topicId;
            tag.style.display = 'inline-flex';
            tag.innerHTML = `
                <span>${topicName}</span>
                <span class="remove-topic" data-topic-id="${topicId}">×</span>
            `;
            
            addSelectedTopicsContainer.appendChild(tag);
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_topic_ids[]';
            input.value = topicId;
            input.dataset.topicId = topicId;
            
            addSelectedTopicsInputs.appendChild(input);
            
            tag.querySelector('.remove-topic').addEventListener('click', function() {
                removeAddSelectedTopic(topicId);
            });
            
            addSelectedTopicsContainer.style.display = 'flex';
        }

        function removeAddSelectedTopic(topicId) {
            addSelectedTopics.delete(topicId);
            
            const tag = addSelectedTopicsContainer.querySelector(`[data-topic-id="${topicId}"]`);
            if (tag) tag.remove();
            
            const input = addSelectedTopicsInputs.querySelector(`[data-topic-id="${topicId}"]`);
            if (input) input.remove();
            
            const option = addTopicDropdown.querySelector(`[data-topic-id="${topicId}"]`);
            if (option) option.classList.remove('selected');
            
            addTopicSearch.value = '';
        }

        function clearAddSelectedTopics() {
            addSelectedTopics.clear();
            addSelectedTopicsContainer.innerHTML = '';
            addSelectedTopicsInputs.innerHTML = '';
            
            document.querySelectorAll('.add-topic-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            addTopicSearch.value = '';
        }

        // Question click handler
        document.querySelectorAll('.question-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                currentQuestionId = this.dataset.questionId;
                const questionText = this.dataset.questionText;
                
                modalQuestionText.textContent = questionText;
                loadAnswers(currentQuestionId);
                modal.classList.remove('hidden');
            });
        });

        // Modal close handlers
        closeModal.addEventListener('click', closeModalHandler);
        cancelBtn.addEventListener('click', closeModalHandler);
        
        // Edit modal close handlers
        closeEditModal.addEventListener('click', closeEditModalHandler);
        cancelEditBtn.addEventListener('click', closeEditModalHandler);
        
        // Add modal handlers
        addQuestionBtn.addEventListener('click', function() {
            addModal.classList.remove('hidden');
        });
        
        closeAddModal.addEventListener('click', closeAddModalHandler);
        cancelAddBtn.addEventListener('click', closeAddModalHandler);
        
        function closeAddModalHandler() {
            addModal.classList.add('hidden');
            addQuestionForm.reset();
            clearAddSelectedTopics();
            addTopicSearch.value = '';
            addTopicDropdown.classList.add('hidden');
        }
        
        function closeModalHandler() {
            modal.classList.add('hidden');
            currentQuestionId = null;
        }
        
        function closeEditModalHandler() {
            editModal.classList.add('hidden');
            currentEditQuestionId = null;
            editQuestionForm.reset();
            clearSelectedTopics();
            topicSearch.value = '';
            topicDropdown.classList.add('hidden');
        }

        // Edit question button handlers
        document.querySelectorAll('.edit-question-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                currentEditQuestionId = this.dataset.questionId;
                loadQuestionForEdit(currentEditQuestionId);
            });
        });

        // Delete question button handlers
        document.querySelectorAll('.delete-question-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const questionId = this.dataset.questionId;
                deleteQuestion(questionId);
            });
        });

        // Load answers for a question
        function loadAnswers(questionId) {
            answersContainer.innerHTML = '<div class="text-center">Loading...</div>';
            
            fetch(`/question/${questionId}/answers`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderAnswers(data.answers);
                    } else {
                        answersContainer.innerHTML = '<div class="text-red-500">Error loading answers</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    answersContainer.innerHTML = '<div class="text-red-500">Error loading answers</div>';
                });
        }

        // Render answers in the modal
        function renderAnswers(answers) {
            answersContainer.innerHTML = '';
            
            // Create 4 answer boxes (A, B, C, D) similar to add modal
            for (let index = 0; index < 4; index++) {
                const optionLetter = String.fromCharCode(65 + index); // A, B, C, D
                const answer = answers[index] || { AnswerID: '', AnswerText: '', ExpectedAnswer: 'N' };
                const isCorrect = answer.ExpectedAnswer === 'Y';
                const isRequired = index < 2; // First 2 (A and B) are required
                
                const answerDiv = document.createElement('div');
                answerDiv.className = `answer-choice border rounded-lg p-3 ${isCorrect ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'bg-gray-50 dark:bg-zinc-700'}`;
                
                answerDiv.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="flex items-center">
                            <input type="radio" name="correct_answer" value="${answer.AnswerID || index}" 
                                   ${isCorrect ? 'checked' : ''} 
                                   class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-medium text-sm">${optionLetter}.</span>
                                <span class="text-xs text-gray-500">Mark as correct answer</span>
                                <span class="text-xs ${isRequired ? 'text-red-500' : 'text-blue-500'} font-medium">${isRequired ? 'Required' : 'Optional'}</span>
                            </div>
                            <textarea class="w-full p-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                                      rows="2" data-answer-id="${answer.AnswerID}" data-answer-index="${index}" 
                                      placeholder="Enter answer choice ${optionLetter}${isRequired ? '' : ' (optional)'}" 
                                      ${isRequired ? 'required' : ''}>${answer.AnswerText || ''}</textarea>
                        </div>
                    </div>
                `;
                
                answersContainer.appendChild(answerDiv);
            }
        }

        // Save changes
        saveBtn.addEventListener('click', function() {
            const answers = [];
            const correctAnswerRadio = document.querySelector('input[name="correct_answer"]:checked');
            
            // Check if a correct answer is selected
            if (!correctAnswerRadio) {
                alert('Please select a correct answer');
                return;
            }
            
            const correctAnswerValue = correctAnswerRadio.value;
            
            // Validate answer texts and collect data
            let hasEmptyRequiredAnswers = false;
            let filledAnswersCount = 0;
            
            answersContainer.querySelectorAll('textarea').forEach((textarea, index) => {
                const answerId = textarea.dataset.answerId;
                const answerIndex = textarea.dataset.answerIndex;
                const answerText = textarea.value.trim();
                const isRequired = index < 2; // First 2 (A and B) are required
                
                // Check if required answer (A or B) is empty
                if (isRequired && !answerText) {
                    hasEmptyRequiredAnswers = true;
                }
                
                // Count filled answers
                if (answerText) {
                    filledAnswersCount++;
                }
                
                // Determine if this answer is correct
                let isCorrect = false;
                if (answerId) {
                    // Existing answer - check by answer ID
                    isCorrect = answerId === correctAnswerValue;
                } else {
                    // New answer - check by index
                    isCorrect = answerIndex == correctAnswerValue;
                }
                
                // Only include answers that have text
                if (answerText) {
                    answers.push({
                        id: answerId || null,
                        text: answerText,
                        is_correct: isCorrect,
                        index: answerIndex
                    });
                }
            });
            
            // Show user-friendly error if required answers are blank
            if (hasEmptyRequiredAnswers) {
                alert('Please fill in answer choices A and B (required).');
                return;
            }
            
            // Ensure at least 2 answers are filled
            if (filledAnswersCount < 2) {
                alert('Please provide at least 2 answer choices.');
                return;
            }
            
            // Check if the selected correct answer has text
            const correctAnswerTextarea = answersContainer.querySelector(`textarea[data-answer-index="${correctAnswerValue}"]`) ||
                                        answersContainer.querySelector(`textarea[data-answer-id="${correctAnswerValue}"]`);
            if (correctAnswerTextarea && !correctAnswerTextarea.value.trim()) {
                alert('The selected correct answer must have text.');
                return;
            }

            // Save to server
            fetch(`/question/${currentQuestionId}/answers`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({ answers })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Changes saved successfully!');
                    closeModalHandler();
                } else {
                    alert('Error saving changes: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving changes');
            });
        });

        // Load question details for editing
        function loadQuestionForEdit(questionId) {
            fetch(`/question/${questionId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const question = data.question;
                        const selectedTopicIds = data.selected_topic_ids || [];
                        
                        // Populate form fields
                        document.getElementById('edit-question-text').value = question.QuestionText || '';
                        
                        // Clear existing selections
                        clearSelectedTopics();
                        
                        // Set selected topics (only one topic allowed)
                        if (selectedTopicIds.length > 0) {
                            const topicId = selectedTopicIds[0]; // Take only the first topic
                            const option = topicDropdown.querySelector(`[data-topic-id="${topicId}"]`);
                            if (option) {
                                const topicName = option.dataset.topicName;
                                
                                console.log('Loading preselected topic:', topicId, topicName); // Debug log
                                
                                // Add to selected topics set
                                selectedTopics.add(topicId);
                                
                                // Create the purple tag - ensure it's visible
                                addSelectedTopic(topicId, topicName);
                                
                                // Mark option as selected in dropdown
                                option.classList.add('selected');
                                
                                // Show the current topic in the input box
                                topicSearch.value = topicName;
                                
                                // Force visibility of the selected topics container
                                selectedTopicsContainer.style.display = 'flex';
                                
                                console.log('Selected topics container:', selectedTopicsContainer.innerHTML); // Debug log
                            } else {
                                console.log('Option not found for topic ID:', topicId); // Debug log
                            }
                        }
                        
                        // Ensure all topics are visible in dropdown for easy selection
                        const allOptions = topicDropdown.querySelectorAll('.topic-option');
                        allOptions.forEach(option => {
                            option.style.display = 'block';
                        });
                        
                        // Show modal after a brief delay to ensure DOM is ready
                        setTimeout(() => {
                            console.log('About to show modal, selected topics container content:', selectedTopicsContainer.innerHTML);
                            editModal.classList.remove('hidden');
                        }, 100);
                    } else {
                        alert('Error loading question details: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading question details');
                });
        }

        // Delete question
        function deleteQuestion(questionId) {
            if (!confirm('Are you sure you want to delete this question? This will also delete all associated answers.')) {
                return;
            }
            
            fetch(`/question/${questionId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Question deleted successfully!');
                    location.reload();
                } else {
                    alert('Error deleting question: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting question');
            });
        }

        // Save question edits
        saveEditBtn.addEventListener('click', function() {
            // Check if question text is filled
            const questionText = document.getElementById('edit-question-text').value.trim();
            if (!questionText) {
                alert('Please fill in your question.');
                return;
            }
            
            // Check if at least one topic is selected
            if (selectedTopics.size === 0) {
                alert('Please select a default topic before saving.');
                return;
            }
            
            const formData = new FormData(editQuestionForm);
            const data = {};
            
            // Get form data
            for (let [key, value] of formData.entries()) {
                if (key === 'selected_topic_ids[]') {
                    if (!data.selected_topic_ids) {
                        data.selected_topic_ids = [];
                    }
                    data.selected_topic_ids.push(value);
                } else {
                    data[key] = value;
                }
            }
            
            fetch(`/question/${currentEditQuestionId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Question updated successfully!');
                    closeEditModalHandler();
                    location.reload();
                } else {
                    // Handle specific validation errors with user-friendly messages
                    let errorMessage = data.message || 'Unknown error';
                    
                    if (errorMessage.includes('question text field is required') || 
                        errorMessage.includes('QuestionText') ||
                        errorMessage.includes('question text')) {
                        errorMessage = 'Please fill in your question.';
                    }
                    
                    alert('Error updating question: ' + errorMessage);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating question');
            });
        });

        // Save new question
        saveAddBtn.addEventListener('click', function() {
            // Check if at least one topic is selected
            if (addSelectedTopics.size === 0) {
                alert('Please select a default topic before saving.');
                return;
            }
            
            // Check if correct answer is selected
            const correctAnswerIndex = document.querySelector('input[name="add_correct_answer"]:checked')?.value;
            if (correctAnswerIndex === undefined) {
                alert('Please select a correct answer.');
                return;
            }
            
            // Get question text
            const questionText = document.getElementById('add-question-text').value.trim();
            if (!questionText) {
                alert('Please enter question text.');
                return;
            }
            
            // Get answers
            const answerTextareas = document.querySelectorAll('#add-answers-container textarea');
            const answers = [];
            let filledAnswers = 0;
            
            answerTextareas.forEach((textarea, index) => {
                const answerText = textarea.value.trim();
                if (answerText) {
                    answers.push({
                        text: answerText,
                        is_correct: index == correctAnswerIndex
                    });
                    filledAnswers++;
                }
            });
            
            // Require at least 2 answers
            if (filledAnswers < 2) {
                alert('Please provide at least 2 answer choices.');
                return;
            }
            
            // Check if the selected correct answer has text
            const correctAnswerTextarea = answerTextareas[correctAnswerIndex];
            if (!correctAnswerTextarea.value.trim()) {
                alert('The selected correct answer must have text.');
                return;
            }
            
            // Get selected topic ID
            const topicId = Array.from(addSelectedTopics)[0];
            
            const data = {
                QuestionText: questionText,
                selected_topic_ids: [topicId],
                answers: answers
            };
            
            fetch('/question', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Question added successfully!');
                    closeAddModalHandler();
                    location.reload();
                } else {
                    alert('Error adding question: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding question');
            });
        });
    });
</script>
