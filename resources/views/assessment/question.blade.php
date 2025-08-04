@extends('layout.appMain')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
        <!-- Header with buttons only -->
        <div class="card-body border-b border-gray-100 dark:border-zinc-600 flex items-center justify-end">
            <div class="flex items-center gap-3">
                <!-- Delete button (hidden by default) -->
                <button id="bulk-delete-btn" type="button"
                    class="hidden px-4 py-1.5 text-white bg-red-500 rounded hover:bg-red-600 text-sm">
                    Delete
                </button>

                <!-- Add button -->
                <button type="button"
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
                                        class="w-4 h-4 border-gray-300 rounded dark:bg-zinc-700 dark:border-zinc-500 checked:bg-blue-600 dark:checked:bg-blue-600">
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
                            <tr class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:hover:bg-zinc-700/50 dark:border-zinc-600">
                                <td class="w-4 p-3">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            class="row-checkbox w-4 h-4 border-gray-300 rounded dark:bg-zinc-700 dark:border-zinc-500 dark:checked:bg-violet-500">
                                    </div>
                                </td>
                                <td class="px-2 py-1.5 text-left">
                                    <button type="button" 
                                        class="text-blue-600 hover:text-blue-800 hover:underline text-left question-btn"
                                        data-question-id="{{ $row->QuestionID }}"
                                        data-question-text="{{ $row->QuestionText }}">
                                        {{ Str::limit($row->QuestionText, 50) }}
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
        
        // Edit modal elements
        const editModal = document.getElementById('edit-question-modal');
        const closeEditModal = document.getElementById('close-edit-modal');
        const cancelEditBtn = document.getElementById('cancel-edit-btn');
        const saveEditBtn = document.getElementById('save-edit-btn');
        const editQuestionForm = document.getElementById('edit-question-form');
        
        // Topic dropdown elements
        const topicSearch = document.getElementById('topic-search');
        const topicDropdown = document.getElementById('topic-dropdown');
        const selectedTopicsContainer = document.getElementById('selected-topics');
        const selectedTopicsInputs = document.getElementById('selected-topics-inputs');
        
        let currentQuestionId = null;
        let currentEditQuestionId = null;
        let selectedTopics = new Set();

        function updateBulkDeleteVisibility() {
            const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            if (bulkDeleteBtn) {
                bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', () => {
                rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                updateBulkDeleteVisibility();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkDeleteVisibility);
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
            topicDropdown.classList.add('hidden');
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
                selectedTopics.add(topicId);
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
            selectedTopics.delete(topicId);
            
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
            selectedTopics.clear();
            selectedTopicsContainer.innerHTML = '';
            selectedTopicsInputs.innerHTML = '';
            
            // Remove selected class from all options
            document.querySelectorAll('.topic-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Clear the input box
            topicSearch.value = '';
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
            
            answers.forEach((answer, index) => {
                const optionLetter = String.fromCharCode(65 + index); // A, B, C, D...
                const isCorrect = answer.ExpectedAnswer === 'Y';
                
                const answerDiv = document.createElement('div');
                answerDiv.className = `border rounded-lg p-3 ${isCorrect ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700'}`;
                
                answerDiv.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="flex items-center">
                            <input type="radio" name="correct_answer" value="${answer.AnswerID}" 
                                   ${isCorrect ? 'checked' : ''} 
                                   class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-medium text-sm">${optionLetter}.</span>
                                ${isCorrect ? '<span class="text-xs bg-green-500 text-white px-2 py-1 rounded">Correct</span>' : ''}
                            </div>
                            <textarea class="w-full p-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                                      rows="2" data-answer-id="${answer.AnswerID}">${answer.AnswerText}</textarea>
                        </div>
                    </div>
                `;
                
                answersContainer.appendChild(answerDiv);
            });
        }

        // Save changes
        saveBtn.addEventListener('click', function() {
            const answers = [];
            const correctAnswerId = document.querySelector('input[name="correct_answer"]:checked')?.value;
            
            answersContainer.querySelectorAll('textarea').forEach(textarea => {
                const answerId = textarea.dataset.answerId;
                const answerText = textarea.value.trim();
                const isCorrect = answerId === correctAnswerId;
                
                answers.push({
                    id: answerId,
                    text: answerText,
                    is_correct: isCorrect
                });
            });

            if (!correctAnswerId) {
                alert('Please select a correct answer');
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
                    alert('Error updating question: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating question');
            });
        });
    });
</script>
