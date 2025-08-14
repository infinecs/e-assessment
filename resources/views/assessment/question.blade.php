           
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit modal: update preview when a new question image is uploaded
    const editQuestionImageUpload = document.getElementById('edit-question-image-upload');
    const editQuestionImagePreview = document.getElementById('edit-question-image-preview');
    const editQuestionImagePreviewImg = document.getElementById('edit-question-image-preview-img');
    const editRemoveQuestionImageBtn = document.getElementById('edit-remove-question-image');
    let editRemoveQuestionImageFlag = false;
    if (editQuestionImageUpload && editQuestionImagePreview && editQuestionImagePreviewImg) {
        editQuestionImageUpload.addEventListener('change', function(e) {
            const file = this.files && this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    editQuestionImagePreviewImg.src = ev.target.result;
                    editQuestionImagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
                editRemoveQuestionImageFlag = false;
            } else {
                editQuestionImagePreviewImg.src = '';
                editQuestionImagePreview.classList.add('hidden');
            }
        });
    }
    if (editRemoveQuestionImageBtn) {
        editRemoveQuestionImageBtn.addEventListener('click', function() {
            if (editQuestionImageUpload) editQuestionImageUpload.value = '';
            if (editQuestionImagePreview) editQuestionImagePreview.classList.add('hidden');
            if (editQuestionImagePreviewImg) editQuestionImagePreviewImg.src = '';
            editRemoveQuestionImageFlag = true;
        });
    }
    // Edit modal: answer type switching and image preview for answers
    document.querySelectorAll('#edit-answers-container .answer-choice').forEach(function(choice) {
        const radios = choice.querySelectorAll('.edit-answer-type-radio');
        radios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                const textContainer = radio.closest('.answer-choice').querySelector('.edit-answer-text-container');
                const imageContainer = radio.closest('.answer-choice').querySelector('.edit-answer-image-container');
                if (this.value === 'text') {
                    textContainer.classList.remove('hidden');
                    imageContainer.classList.add('hidden');
                } else {
                    textContainer.classList.add('hidden');
                    imageContainer.classList.remove('hidden');
                }
            });
        });

        // Image upload and preview
        const imageInput = choice.querySelector('input[type="file"]');
        const previewDiv = choice.querySelector('[id^="edit-answer-image-preview-"]');
        const previewImg = previewDiv ? previewDiv.querySelector('img') : null;
        const removeBtn = previewDiv ? previewDiv.querySelector('.edit-remove-answer-image') : null;

        if (imageInput && previewDiv && previewImg && removeBtn) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        previewImg.src = event.target.result;
                        previewDiv.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
            removeBtn.addEventListener('click', function() {
                imageInput.value = '';
                previewDiv.classList.add('hidden');
                previewImg.src = '';
            });
        }
    });
    // Question type toggle
    document.querySelectorAll('.question-type-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'text') {
                document.getElementById('question-text-container').classList.remove('hidden');
                document.getElementById('question-image-container').classList.add('hidden');
            } else {
                document.getElementById('question-text-container').classList.add('hidden');
                document.getElementById('question-image-container').classList.remove('hidden');
            }
        });
    });

    // Handle question image upload
    const questionImageUpload = document.getElementById('question-image-upload');
    if (questionImageUpload) {
        questionImageUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const previewImg = document.getElementById('question-image-preview-img');
                    previewImg.src = event.target.result;
                    document.getElementById('question-image-preview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Remove question image
    const removeQuestionImageBtn = document.getElementById('remove-question-image');
    if (removeQuestionImageBtn) {
        removeQuestionImageBtn.addEventListener('click', function() {
            document.getElementById('question-image-upload').value = '';
            document.getElementById('question-image-preview').classList.add('hidden');
        });
    }

    // Answer type switching and image preview for answers
    document.querySelectorAll('.answer-choice').forEach(function(choice) {
        const radios = choice.querySelectorAll('.answer-type-radio');
        radios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                // Only toggle containers within this answer block
                const textContainer = radio.closest('.answer-choice').querySelector('.answer-text-container');
                const imageContainer = radio.closest('.answer-choice').querySelector('.answer-image-container');
                if (this.value === 'text') {
                    textContainer.classList.remove('hidden');
                    imageContainer.classList.add('hidden');
                } else {
                    textContainer.classList.add('hidden');
                    imageContainer.classList.remove('hidden');
                }
            });
        });

        // Image upload and preview
        const imageInput = choice.querySelector('input[type="file"]');
        const previewDiv = choice.querySelector('[id^="answer-image-preview-"]');
        const previewImg = previewDiv ? previewDiv.querySelector('img') : null;
        const removeBtn = previewDiv ? previewDiv.querySelector('.remove-answer-image') : null;

        if (imageInput && previewDiv && previewImg && removeBtn) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        previewImg.src = event.target.result;
                        previewDiv.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
            removeBtn.addEventListener('click', function() {
                imageInput.value = '';
                previewDiv.classList.add('hidden');
                previewImg.src = '';
            });
        }
    });
        // Add modal elements
        const addModal = document.getElementById('add-question-modal');
        const addQuestionBtn = document.getElementById('add-question-btn');
        const closeAddModal = document.getElementById('close-add-modal');
        const cancelAddBtn = document.getElementById('cancel-add-btn');

        // Add Question button in Add Modal
        const saveAddBtn = document.getElementById('save-add-btn');
        if (saveAddBtn) {
            saveAddBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const addForm = document.getElementById('add-question-form');
                if (!addForm) return;

                // Build answers array from modal, only include filled answers
                const answers = [];
                document.querySelectorAll('#add-answers-container .answer-choice').forEach((choice, idx) => {
                    const type = choice.querySelector('input[name="answer_type_' + idx + '"]:checked')?.value;
                    const isCorrect = choice.querySelector('input[name="add_correct_answer"]:checked')?.value == idx;
                    let text = '';
                    let answerImage = null;
                    if (type === 'text') {
                        text = choice.querySelector('.answer-text-container textarea')?.value?.trim() || '';
                        if (!text) return; // skip empty text answer
                    } else if (type === 'image') {
                        answerImage = choice.querySelector('input[type="file"]')?.files[0] || null;
                        if (!answerImage) return; // skip empty image answer
                    } else {
                        return; // skip if neither type
                    }
                    answers.push({ type, is_correct: isCorrect, text, answer_image: answerImage });
                });

                // Validate at least 2 answers (text or image)
                if (answers.length < 2) {
                    alert('Please provide at least 2 answer choices.');
                    return;
                }

                // Validate at least one correct answer
                if (!answers.some(a => a.is_correct)) {
                    alert('Please mark at least one correct answer.');
                    return;
                }

                // Prepare FormData
                const formData = new FormData(addForm);
                // Remove any default answer fields
                for (let pair of Array.from(formData.keys())) {
                    if (pair.startsWith('answer_type_') || pair.startsWith('answer_image_')) {
                        formData.delete(pair);
                    }
                }
                // Append answers as indexed fields
                answers.forEach((ans, i) => {
                    formData.append(`answers[${i}][type]`, ans.type);
                    formData.append(`answers[${i}][is_correct]`, ans.is_correct ? '1' : '0');
                    formData.append(`answers[${i}][text]`, ans.text);
                    if (ans.type === 'image' && ans.answer_image) {
                        formData.append(`answers[${i}][answer_image]`, ans.answer_image);
                    }
                });

                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Disable button to prevent double submit
                saveAddBtn.disabled = true;
                saveAddBtn.textContent = 'Saving...';

                fetch('/question', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    saveAddBtn.disabled = false;
                    saveAddBtn.textContent = 'Add Question';
                    if (data.success) {
                        if (addModal) addModal.classList.add('hidden');
                        addForm.reset();
                        alert('Question added successfully!');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to add question.');
                    }
                })
                .catch(error => {
                    saveAddBtn.disabled = false;
                    saveAddBtn.textContent = 'Add Question';
                    alert('An error occurred while adding the question.');
                    console.error(error);
                });
            });
        }
});
</script>
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
                <!-- Export to Excel Button -->
                <button id="export-excel-btn" type="button"
                    class="px-4 py-1.5 text-white bg-green-500 rounded hover:bg-green-600 text-sm">
                    <i class="fas fa-file-csv"></i>
                    Export to CSV
                </button>

                <!-- Delete button (hidden by default) -->
                <button id="bulk-delete-btn" type="button"
                    class="hidden px-4 py-1.5 text-white bg-red-500 rounded hover:bg-red-600 text-sm">
                    Delete
                </button>

                <!-- Add button -->
                <button type="button" id="add-question-btn"
                    class="px-6 py-1.5 bg-violet-500 text-white text-sm rounded hover:bg-violet-600 focus:ring-2 focus:ring-violet-500 focus:ring-offset-1 flex items-center gap-2">
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
                                        class="text-violet-600 hover:text-violet-800 hover:underline text-left question-btn"
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
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Question Answer</h3>
            <button type="button" id="close-modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="mdi mdi-close text-2xl"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="px-6 py-4 overflow-y-auto" style="max-height: 70vh;">
            <div class="mb-2">
                <label class="block text-base font-semibold text-gray-800 dark:text-gray-200 mb-1">Question:</label>
                <div class="bg-white dark:bg-zinc-700 rounded-lg shadow-sm p-2 border border-gray-200 dark:border-zinc-600">
                    <p id="modal-question-text" class="text-gray-900 dark:text-white text-base"></p>
                </div>
            </div>
            <div class="mb-2">
                <label class="block text-base font-semibold text-gray-800 dark:text-gray-200 mb-1">Answer Choices:</label>
                <div id="answers-container" class="grid grid-cols-1 gap-2">
                    <!-- Answers will be loaded here -->
                </div>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700 border-t border-gray-200 dark:border-zinc-600">
            <button type="button" id="cancel-btn" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div id="edit-question-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl w-full max-w-4xl mx-4 overflow-hidden flex flex-col h-full" style="max-height: 90vh;">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-600 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Question</h3>
            <button type="button" id="close-edit-modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="mdi mdi-close text-xl"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="px-6 py-4 flex-1 overflow-y-auto">
            <form id="edit-question-form" class="space-y-6">
                <!-- Question Text (always visible and required) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text <span class="text-red-500">*</span></label>
                    <textarea id="edit-question-text" name="QuestionText" rows="4" 
                        class="w-full p-3 border border-violet-400 dark:border-violet-600 rounded-lg text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-violet-500" 
                        placeholder="Enter question text" required></textarea>
                </div>
                <!-- Question Image (always visible) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Image</label>
                    <div class="flex flex-col items-center justify-center border-2 border-dashed border-violet-400 rounded-lg p-6 bg-violet-50 hover:bg-violet-100 transition cursor-pointer">
                        <label for="edit-question-image-upload" class="text-violet-600 font-semibold cursor-pointer mb-1">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M4 12l4-4a2 2 0 012.828 0l2.344 2.344a2 2 0 002.828 0L20 8M4 12v4a2 2 0 002 2h12a2 2 0 002-2v-4"></path>
                            </svg>
                            Upload an image
                        </label>
                        <input id="edit-question-image-upload" name="edit_question_image" type="file" accept="image/png, image/jpeg, image/gif" class="hidden" />
                        <span class="text-gray-500 text-sm">PNG, JPG, GIF up to 2MB</span>
                    </div>
                    <div id="edit-question-image-preview" class="mt-2 hidden">
                        <img id="edit-question-image-preview-img" class="max-h-40 mx-auto rounded shadow" src="" alt="Question image preview">
                        <button type="button" id="edit-remove-question-image" class="mt-2 text-sm text-red-600">Remove Image</button>
                    </div>
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Answer Choices</label>
                    <div id="edit-answers-container" class="space-y-3">
                        @foreach(['A','B','C','D'] as $i => $label)
                        <div class="answer-choice border rounded-lg p-3 bg-gray-50 dark:bg-zinc-700">
                            <div class="flex items-start gap-3">
                                <div class="flex items-center">
                                    <input type="radio" name="edit_correct_answer" value="{{$i}}" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-medium text-sm">{{$label}}.</span>
                                        <span class="text-xs text-gray-500">Mark as correct answer</span>
                                        <span class="text-xs {{ $i < 2 ? 'text-red-500' : 'text-violet-500' }} font-medium">{{ $i < 2 ? 'Required' : 'Optional' }}</span>
                                    </div>
                                    <div class="mb-2 flex gap-4 items-center">
                                        <label class="flex items-center gap-1 text-xs font-medium">
                                            <input type="radio" name="edit_answer_type_{{$i}}" value="text" class="edit-answer-type-radio text-violet-600 focus:ring-violet-500" checked>
                                            Text
                                        </label>
                                        <label class="flex items-center gap-1 text-xs font-medium">
                                            <input type="radio" name="edit_answer_type_{{$i}}" value="image" class="edit-answer-type-radio text-violet-600 focus:ring-violet-500">
                                            Image
                                        </label>
                                    </div>
                                    <div class="edit-answer-text-container">
                                        <textarea class="w-full p-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                                                  rows="2" placeholder="Enter answer choice {{$label}}" {{ $i < 2 ? 'required' : '' }}></textarea>
                                    </div>
                                    <div class="edit-answer-image-container hidden">
                                        <div class="flex flex-col items-center justify-center border-2 border-dashed border-violet-400 rounded-lg p-3 bg-violet-50 hover:bg-violet-100 transition cursor-pointer">
                                            <label for="edit-answer-image-upload-{{$i}}" class="text-violet-600 font-semibold cursor-pointer mb-1">
                                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M4 12l4-4a2 2 0 012.828 0l2.344 2.344a2 2 0 002.828 0L20 8M4 12v4a2 2 0 002 2h12a2 2 0 002-2v-4"></path>
                                                </svg>
                                                Upload an image
                                            </label>
                                            <input id="edit-answer-image-upload-{{$i}}" name="edit_answer_image_{{$i}}" type="file" accept="image/png, image/jpeg, image/gif" class="hidden" />
                                            <span class="text-gray-500 text-sm">PNG, JPG, GIF up to 2MB</span>
                                        </div>
                                        <div id="edit-answer-image-preview-{{$i}}" class="mt-2 hidden">
                                            <img class="max-h-32 mx-auto" src="" alt="Answer image preview">
                                            <button type="button" class="mt-2 text-xs text-red-600 edit-remove-answer-image">Remove Image</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Modal Footer -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700 border-t border-gray-200 dark:border-zinc-600">
            <button type="button" id="save-edit-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                Save Changes
            </button>
            <button type="button" id="cancel-edit-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
                Cancel
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
                <!-- Question Text (always visible and required) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text <span class="text-red-500">*</span></label>
                    <textarea id="add-question-text" name="QuestionText" rows="4" 
                        class="w-full p-3 border border-gray-300 dark:border-zinc-600 rounded-lg text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                        placeholder="Enter question text" required></textarea>
                </div>
                <!-- Question Image (always visible) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Image</label>
                    <div class="flex flex-col items-center justify-center border-2 border-dashed border-violet-400 rounded-lg p-6 bg-violet-50 hover:bg-violet-100 transition cursor-pointer">
                        <label for="question-image-upload" class="text-violet-600 font-semibold cursor-pointer mb-1">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M4 12l4-4a2 2 0 012.828 0l2.344 2.344a2 2 0 002.828 0L20 8M4 12v4a2 2 0 002 2h12a2 2 0 002-2v-4"></path>
                            </svg>
                            Upload an image
                        </label>
                        <input id="question-image-upload" name="question_image" type="file" accept="image/png, image/jpeg, image/gif" class="hidden" />
                        <span class="text-gray-500 text-sm">PNG, JPG, GIF up to 2MB</span>
                    </div>
                    <div id="question-image-preview" class="mt-2 hidden">
                        <img id="question-image-preview-img" class="max-h-40 mx-auto" src="" alt="Question image preview">
                        <button type="button" id="remove-question-image" class="mt-2 text-sm text-red-600">Remove Image</button>
                    </div>
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
                            @foreach(['A','B','C','D'] as $i => $label)
                            <div class="answer-choice border rounded-lg p-3 bg-gray-50 dark:bg-zinc-700">
                                <div class="flex items-start gap-3">
                                    <div class="flex items-center">
                                        <input type="radio" name="add_correct_answer" value="{{$i}}" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-medium text-sm">{{$label}}.</span>
                                            <span class="text-xs text-gray-500">Mark as correct answer</span>
                                            <span class="text-xs {{ $i < 2 ? 'text-red-500' : 'text-violet-500' }} font-medium">{{ $i < 2 ? 'Required' : 'Optional' }}</span>
                                        </div>
                                        <div class="mb-2 flex gap-4 items-center">
                                            <label class="flex items-center gap-1 text-xs font-medium">
                                                <input type="radio" name="answer_type_{{$i}}" value="text" class="answer-type-radio text-violet-600 focus:ring-violet-500" checked>
                                                Text
                                            </label>
                                            <label class="flex items-center gap-1 text-xs font-medium">
                                                <input type="radio" name="answer_type_{{$i}}" value="image" class="answer-type-radio text-violet-600 focus:ring-violet-500">
                                                Image
                                            </label>
                                        </div>
                                        <div class="answer-text-container">
                                            <textarea class="w-full p-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                                                      rows="2" placeholder="Enter answer choice {{$label}}" {{ $i < 2 ? 'required' : '' }}></textarea>
                                        </div>
                                        <div class="answer-image-container hidden">
                                            <div class="flex flex-col items-center justify-center border-2 border-dashed border-violet-400 rounded-lg p-3 bg-violet-50 hover:bg-violet-100 transition cursor-pointer">
                                                <label for="answer-image-upload-{{$i}}" class="text-violet-600 font-semibold cursor-pointer mb-1">
                                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M4 12l4-4a2 2 0 012.828 0l2.344 2.344a2 2 0 002.828 0L20 8M4 12v4a2 2 0 002 2h12a2 2 0 002-2v-4"></path>
                                                    </svg>
                                                    Upload an image
                                                </label>
                                                <input id="answer-image-upload-{{$i}}" name="answer_image_{{$i}}" type="file" accept="image/png, image/jpeg, image/gif" class="hidden" />
                                                <span class="text-gray-500 text-sm">PNG, JPG, GIF up to 2MB</span>
                                            </div>
                                            <div id="answer-image-preview-{{$i}}" class="mt-2 hidden">
                                                <img class="max-h-32 mx-auto" src="" alt="Answer image preview">
                                                <button type="button" class="mt-2 text-xs text-red-600 remove-answer-image">Remove Image</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Modal Footer -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700 border-t border-gray-200 dark:border-zinc-600">
            <button type="button" id="save-add-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                Add Question
            </button>
            <button type="button" id="cancel-add-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
                Cancel
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
            }, 800); // Increased delay for server requests
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

        // Perform search with filters - SERVER SIDE (AJAX - no page refresh)
        function performFilteredSearch() {
            const searchTerm = searchInput.value.trim();
            const selectedTopicIds = Array.from(selectedTopicsForSearch);
            
            // Build query parameters for server-side filtering
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (selectedTopicIds.length > 0) params.append('topics', selectedTopicIds.join(','));
            params.append('ajax', '1'); // Add AJAX flag
            
            // Show loading state
            const tbody = document.querySelector('tbody');
            tbody.innerHTML = '<tr><td colspan="7" class="px-2 py-1.5 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            
            // Make AJAX request to get filtered results
            fetch(`/question?${params.toString()}`, {
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
                    const newParams = new URLSearchParams();
                    if (searchTerm) newParams.append('search', searchTerm);
                    if (selectedTopicIds.length > 0) newParams.append('topics', selectedTopicIds.join(','));
                    currentUrl.search = newParams.toString();
                    window.history.pushState({}, '', currentUrl.toString());
                    
                    // Re-initialize row checkboxes and buttons for new content
                    initializeRowCheckboxes(); // Must run after filtering completes
                    initializeQuestionButtons();
                    
                    // Update filter displays
                    updateTopicDisplay();
                    updateClearAllButton();
                    
                    console.log(`AJAX search completed: ${data.total || 0} questions found`);
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="px-2 py-1.5 text-center text-red-500">Error loading results</td></tr>';
                    console.error('Search error:', data.message);
                }
            })
            .catch(error => {
                console.error('AJAX error:', error);
                tbody.innerHTML = '<tr><td colspan="7" class="px-2 py-1.5 text-center text-red-500">Error loading results</td></tr>';
            });
        }

        // Clear all filters - SERVER SIDE (AJAX - no page refresh)
        function clearAllFiltersAction() {
            // Clear text search
            searchInput.value = '';
            
            // Clear topic selections  
            selectedTopicsForSearch.clear();
            document.querySelectorAll('.topic-filter-checkbox').forEach(cb => cb.checked = false);
            updateTopicDisplay();
            
            // Close dropdowns
            topicDropdownFilter.classList.add('hidden');
            
            // Show loading state
            const tbody = document.querySelector('tbody');
            tbody.innerHTML = '<tr><td colspan="7" class="px-2 py-1.5 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            
            // Make AJAX request to get all results (no filters)
            fetch('/question?ajax=1', {
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
                    
                    // Re-initialize row checkboxes and buttons for new content
                    initializeRowCheckboxes();
                    initializeQuestionButtons();
                    
                    console.log('Filters cleared successfully');
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="px-2 py-1.5 text-center text-red-500">Error loading results</td></tr>';
                    console.error('Clear filters error:', data.message);
                }
            })
            .catch(error => {
                console.error('AJAX error:', error);
                tbody.innerHTML = '<tr><td colspan="7" class="px-2 py-1.5 text-center text-red-500">Error loading results</td></tr>';
            });
        }

        // Remove the showNoResultsMessage function since server-side filtering handles this

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
                // Remove previous listeners by cloning
                const newCb = cb.cloneNode(true);
                cb.parentNode.replaceChild(newCb, cb);
                newCb.addEventListener('change', function() {
                    handleRowCheckboxChange.call(this);
                    updateBulkDeleteVisibility();
                });
            });

            // Update select all functionality
            const selectAllElement = document.getElementById('checkbox-all');
            if (selectAllElement) {
                // Remove previous listeners by cloning
                const newSelectAll = selectAllElement.cloneNode(true);
                selectAllElement.parentNode.replaceChild(newSelectAll, selectAllElement);
                newSelectAll.addEventListener('change', () => {
                    const checkboxes = document.querySelectorAll('.row-checkbox');
                    checkboxes.forEach(cb => cb.checked = newSelectAll.checked);
                    updateBulkDeleteVisibility();
                    updateSelectAllState();
                });
            }

            // Update select all state and bulk delete button visibility
            updateSelectAllState();
            updateBulkDeleteVisibility();

            // Re-initialize dropdown menus for new content
            document.querySelectorAll('.dropdown').forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                if (toggle) {
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

        // Initialize question buttons after AJAX content update
        function initializeQuestionButtons() {
            // Remove old event listeners and add new ones for question view buttons
            document.querySelectorAll('.question-btn').forEach(btn => {
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                newBtn.addEventListener('click', function() {
                    currentQuestionId = this.dataset.questionId;
                    // Fetch question details and answers immediately
                    fetch(`/question/${currentQuestionId}/details`)
                        .then(res => res.json())
                        .then(qData => {
                            if (qData.success && qData.question) {
                                // Display both question text and image if both exist
                                let questionHtml = '';
                                if (qData.question.QuestionText) {
                                    questionHtml += `<div class='mb-2 text-base text-gray-900 dark:text-white'>${qData.question.QuestionText}</div>`;
                                }
                                if (qData.question.QuestionImage) {
                                    questionHtml += `<img src='${qData.question.QuestionImage}' class='max-h-40 mx-auto rounded shadow' alt='Question image'>`;
                                }
                                modalQuestionText.innerHTML = questionHtml;
                                answersContainer.innerHTML = '<div id="on-demand-answers"></div>';
                                fetch(`/question/${currentQuestionId}/answers`)
                                    .then(res => res.json())
                                    .then(aData => {
                                        const answerDiv = document.getElementById('on-demand-answers');
                                        answerDiv.innerHTML = '';
                                        if (aData.success && aData.answers) {
                                            aData.answers.forEach((ans, idx) => {
                                                let isCorrect = ans.ExpectedAnswer === 'Y';
                                                let correctClass = isCorrect ? 'border-green-500 border-4 bg-green-50 dark:bg-green-900 shadow-md' : 'border-gray-200 shadow-sm';
                                                let answerContent = '';
                                                if (ans.AnswerType === 'I' && ans.AnswerImage) {
                                                    let imgSrc = ans.AnswerImage;
                                                    if (!/^https?:\/\//.test(imgSrc)) {
                                                        imgSrc = imgSrc.replace(/^\//, '');
                                                        imgSrc = window.location.origin + '/' + imgSrc;
                                                    }
                                                    answerContent = `<img src='${imgSrc}' class='max-h-24 mx-auto mb-1 rounded' alt='Answer image'>`;
                                                } else {
                                                    answerContent = `<div class='p-1 text-base'>${ans.AnswerText || ''}</div>`;
                                                }
                                                answerDiv.innerHTML += `
                                                    <div class='answer-choice border rounded-lg p-2 ${correctClass} bg-white dark:bg-zinc-700 flex flex-col mb-2'>
                                                        <div class='flex items-center gap-2 mb-1'>
                                                            <span class='font-bold text-base'>${String.fromCharCode(65+idx)}.</span>
                                                            ${isCorrect ? `<span class='inline-flex items-center px-2 py-0.5 bg-green-200 text-green-800 text-xs font-semibold rounded shadow'><svg class='w-4 h-4 mr-1 text-green-500' fill='none' stroke='currentColor' stroke-width='2' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' d='M5 13l4 4L19 7'/></svg>Correct</span>` : ''}
                                                        </div>
                                                        ${answerContent}
                                                    </div>
                                                `;
                                            });
                                        } else {
                                            answerDiv.innerHTML = '<div class="text-red-500">No answers found.</div>';
                                        }
                                    });
                                modal.classList.remove('hidden');
                            }
                        });
                });
            });
            
            // Reinitialize edit question button handlers
            document.querySelectorAll('.edit-question-btn').forEach(btn => {
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                newBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    currentEditQuestionId = this.dataset.questionId;
                    loadQuestionForEdit(currentEditQuestionId); // only edit modal logic here
                });
            });
        // New function: loadAnswersReadOnly
        function loadAnswersReadOnly(questionId) {
            // Fetch answers for the question (AJAX or from page data)
            // For demo, assume answers are available in a JS object: window.answersByQuestionId
            const answers = window.answersByQuestionId ? window.answersByQuestionId[questionId] : [];
            answersContainer.innerHTML = '';
                                            aData.answers.forEach((ans, idx) => {
                                                let isCorrect = ans.ExpectedAnswer === 'Y';
                                                let correctClass = isCorrect ? 'border-green-500 bg-green-50 dark:bg-green-900' : 'border-gray-200';
                                                div.className = `answer-choice border rounded-lg p-3 ${correctClass} bg-gray-50 dark:bg-zinc-700`;
                                                let answerContent = '';
                                                if (ans.AnswerType === 'I') {
                                                    answerContent = `<img src='${ans.AnswerText}' class='max-h-32 mx-auto mb-2' alt='Answer image'>`;
                                                } else {
                                                    answerContent = `<div class='p-2'>${ans.AnswerText}</div>`;
                                                }
                                                div.innerHTML = `
                                                    <div class='flex items-center gap-2 mb-2'>
                                                        <span class='font-medium text-sm'>${String.fromCharCode(65+idx)}.</span>
                                                        ${isCorrect ? `<span class='inline-flex items-center px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded'><svg class='w-4 h-4 mr-1 text-green-500' fill='none' stroke='currentColor' stroke-width='2' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' d='M5 13l4 4L19 7'/></svg>Correct Answer</span>` : ''}
                                                    </div>
                                                    ${answerContent}
                                                `;
                                                answersContainer.appendChild(div);
                                            });
        }

            // Reinitialize delete question button handlers
            document.querySelectorAll('.delete-question-btn').forEach(btn => {
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                
                newBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const questionId = this.dataset.questionId;
                    deleteQuestion(questionId);
                });
            });
        }

        // Handle row checkbox change
        function handleRowCheckboxChange() {
            updateSelectAllState();
            updateBulkDeleteVisibility();
        }

        function updateSelectAllState() {
            // With AJAX content, work with current checkboxes on page
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

        function updateBulkDeleteVisibility() {
            // With AJAX content, work with current checkboxes on page
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const currentRowCheckboxes = document.querySelectorAll('.row-checkbox');
            const anyChecked = Array.from(currentRowCheckboxes).some(cb => cb.checked);
            
            if (bulkDeleteBtn) {
                bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
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
        performSearchBtn.addEventListener('click', function() {
            const searchTerm = searchInput.value.trim();
            const selectedTopicIds = Array.from(selectedTopicsForSearch);
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (selectedTopicIds.length > 0) params.append('topics', selectedTopicIds.join(','));
            window.location.href = '/question?' + params.toString();
        });
        clearAllFilters.addEventListener('click', function() {
            window.location.href = '/question';
        });
        
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
        initializeFilters(); // Restore filters from URL
        console.log('Search initialization complete'); // Debug log

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
            
            // Restore topic selections
            const topicsParam = urlParams.get('topics');
            if (topicsParam) {
                const topicIds = topicsParam.split(',');
                
                // Check the appropriate checkboxes when they're rendered and sync selectedTopicsForSearch
                setTimeout(() => {
                    selectedTopicsForSearch.clear(); // Clear first to avoid duplicates
                    topicIds.forEach(topicId => {
                        selectedTopicsForSearch.add(topicId);
                        const checkbox = document.getElementById(`topic_${topicId}`);
                        if (checkbox) checkbox.checked = true;
                    });
                    updateTopicDisplay();
                }, 100);
            }
        }
        
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

        // Note: selectAll and initial rowCheckboxes event listeners are now handled in initializeRowCheckboxes()
        // This ensures they work properly after AJAX content updates

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
                        // Refresh current view with AJAX instead of page reload
                        performFilteredSearch();
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

        // Dropdown toggle and menu actions using event delegation
        document.addEventListener('click', function(e) {
            // Toggle dropdown if dropdown-toggle is clicked
            if (e.target.closest('.dropdown-toggle')) {
                e.stopPropagation();
                const dropdown = e.target.closest('.dropdown');
                const menu = dropdown.querySelector('.dropdown-menu');

                // Hide all menus first
                document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));

                // Toggle the clicked one
                menu.classList.toggle('hidden');
            } 
            else {
                // Click outside - hide all dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
                if (typeof topicDropdown !== 'undefined') topicDropdown.classList.add('hidden');
                if (typeof addTopicDropdown !== 'undefined') addTopicDropdown.classList.add('hidden');
            }
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
        
        // Export to Excel functionality (uses current filters)
        const exportExcelBtn = document.getElementById('export-excel-btn');
        exportExcelBtn.addEventListener('click', function() {
            // Get current filter values
            const searchTerm = searchInput.value.trim();
            const selectedTopics = Array.from(selectedTopicsForSearch);
            
            // Build query parameters
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (selectedTopics.length > 0) params.append('topics', selectedTopics.join(','));
            
            // Create download URL using Laravel route
            const exportUrl = `/question/export-excel?${params.toString()}`;
            
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

        // Always update bulk delete button visibility on page load
        updateBulkDeleteVisibility();

        // Question click handler - initialize for existing content
        initializeRowCheckboxes();
        initializeQuestionButtons();

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
            // Reset remove image flag when modal closes
            if (typeof editRemoveQuestionImageFlag !== 'undefined') {
                editRemoveQuestionImageFlag = false;
            }
        }

        // Note: Edit and delete question button handlers are now in initializeQuestionButtons()
        // This ensures they work properly after AJAX content updates

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
        const answer = answers[index] || { AnswerID: '', AnswerText: '', ExpectedAnswer: 'N', AnswerImage: '' };
        const isCorrect = answer.ExpectedAnswer === 'Y';
        const isRequired = index < 2; // First 2 (A and B) are required

        const answerDiv = document.createElement('div');
        answerDiv.className = `answer-choice border rounded-lg p-3 ${isCorrect ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'bg-gray-50 dark:bg-zinc-700'}`;

    let answerContent = '';
    // Debug: log answer type and image
    console.log(answer.AnswerType, answer.AnswerImage);
    if (answer.AnswerImage) {
        // If AnswerImage is a relative path, prepend the site origin
        let imgSrc = answer.AnswerImage;
        if (!/^https?:\/\//.test(imgSrc)) {
            // Remove leading slash if present to avoid double slashes
            imgSrc = imgSrc.replace(/^\//, '');
            imgSrc = window.location.origin + '/' + imgSrc;
        }
        answerContent = `<div class="flex flex-col items-center mb-2"><img src="${imgSrc}" alt="Answer image" class="max-h-32 object-contain mb-1" onerror="this.style.display='none'" /><span class="text-xs text-gray-500">Answer image</span></div>`;
    } else {
        // Show text if no image
        answerContent = `<textarea class="w-full p-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
            rows="2" data-answer-id="${answer.AnswerID}" data-answer-index="${index}" 
            placeholder="Enter answer choice ${optionLetter}${isRequired ? '' : ' (optional)'}" 
            ${isRequired ? 'required' : ''} readonly>${answer.AnswerText || ''}</textarea>`;
    }

        answerDiv.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="flex items-center">
                    <input type="radio" name="correct_answer" value="${answer.AnswerID || index}" class="text-green-600 focus:ring-green-500" 
                        ${isCorrect ? 'checked' : ''} 
                        class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500" disabled>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-medium text-sm">${optionLetter}.</span>
                        <span class="text-xs text-gray-500">Mark as correct answer</span>
                        <span class="text-xs ${isRequired ? 'text-red-500' : 'text-violet-500'} font-medium">${isRequired ? 'Required' : 'Optional'}</span>
                    </div>
                    ${answerContent}
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
            // Check for at least one image answer
            let hasImageAnswer = false;
            document.querySelectorAll('#add-answers-container .answer-choice').forEach((choice, index) => {
                const imageRadio = choice.querySelector('input[type="radio"][value="image"]');
                const imageInput = choice.querySelector('input[type="file"]');
                if (imageRadio && imageRadio.checked && imageInput && imageInput.files.length > 0) {
                    hasImageAnswer = true;
                }
            });
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

            // Require at least one image answer
            if (!hasImageAnswer) {
                alert('Please provide at least one answer choice as an image.');
                return;
            }
                
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
       // Enhanced edit modal functionality with proper data population

// Load question details for editing with answers
function loadQuestionForEdit(questionId) {
    console.log('Loading question details for ID:', questionId);
    
    // Show loading state in modal
    const editQuestionText = document.getElementById('edit-question-text');
    const editAnswersContainer = document.getElementById('edit-answers-container');
    
    if (editQuestionText) {
        editQuestionText.value = 'Loading...';
    }
    if (editAnswersContainer) {
        editAnswersContainer.innerHTML = '<div class="text-center py-4">Loading answers...</div>';
    }
    
    // Show modal first
    const editModal = document.getElementById('edit-question-modal');
    editModal.classList.remove('hidden');
    
    // Load question details
    Promise.all([
        fetch(`/question/${questionId}/details`),
        fetch(`/question/${questionId}/answers`)
    ])
    .then(responses => {
        if (!responses[0].ok || !responses[1].ok) {
            throw new Error('Failed to load question data');
        }
        return Promise.all([responses[0].json(), responses[1].json()]);
    })
    .then(([questionData, answersData]) => {
        console.log('Question data:', questionData);
        console.log('Answers data:', answersData);
        
        if (questionData.success && answersData.success) {
            populateEditModal(questionData.question, questionData.selected_topic_ids || [], answersData.answers || []);
        } else {
            throw new Error(questionData.message || answersData.message || 'Failed to load data');
        }
    })
    .catch(error => {
        console.error('Error loading question:', error);
        alert('Error loading question details: ' + error.message);
        closeEditModalHandler();
    });
}

// Populate the edit modal with question data
function populateEditModal(question, selectedTopicIds, answers) {
    console.log('Populating modal with:', question, selectedTopicIds, answers);
    
    // Clear previous data
    clearSelectedTopics();
    
    // Set question text
    const editQuestionText = document.getElementById('edit-question-text');
    if (editQuestionText) {
        editQuestionText.value = question.QuestionText || '';
    }
    
    // Handle question image
    const editQuestionImagePreview = document.getElementById('edit-question-image-preview');
    const editQuestionImagePreviewImg = document.getElementById('edit-question-image-preview-img');
    
    if (question.QuestionImage && editQuestionImagePreview && editQuestionImagePreviewImg) {
        let imageSrc = question.QuestionImage;
        if (!/^https?:\/\//.test(imageSrc)) {
            imageSrc = imageSrc.replace(/^\//, '');
            imageSrc = window.location.origin + '/' + imageSrc;
        }
        editQuestionImagePreviewImg.src = imageSrc;
        editQuestionImagePreview.classList.remove('hidden');
    } else if (editQuestionImagePreview) {
        editQuestionImagePreview.classList.add('hidden');
    }
    
    // Set selected topic (only one topic allowed)
    if (selectedTopicIds.length > 0) {
        const topicId = selectedTopicIds[0];
        const topicDropdown = document.getElementById('topic-dropdown');
        const option = topicDropdown.querySelector(`[data-topic-id="${topicId}"]`);
        
        if (option) {
            const topicName = option.dataset.topicName;
            console.log('Setting topic:', topicId, topicName);
            
            selectedTopicsForEdit.add(topicId);
            addSelectedTopic(topicId, topicName);
            option.classList.add('selected');
            
            const topicSearch = document.getElementById('topic-search');
            if (topicSearch) {
                topicSearch.value = topicName;
            }
        }
    }
    
    // Populate answers
    populateAnswersInEditModal(answers);
    // Remove any previous click event listeners and attach the main save logic directly
    const saveEditBtn = document.getElementById('save-edit-btn');
    if (saveEditBtn) {
        // Remove all previous click listeners by replacing the button with itself (preserves attributes, removes listeners)
        const newBtn = saveEditBtn.cloneNode(true);
        saveEditBtn.parentNode.replaceChild(newBtn, saveEditBtn);
        newBtn.addEventListener('click', function() {
            // --- Begin main save logic with answer validation ---
            const questionText = document.getElementById('edit-question-text').value.trim();
            if (!questionText) {
                alert('Please fill in your question.');
                return;
            }
            if (selectedTopicsForEdit.size === 0) {
                alert('Please select a default topic before saving.');
                return;
            }
            const formData = new FormData();
            formData.append('QuestionText', questionText);
            selectedTopicsForEdit.forEach(topicId => {
                formData.append('selected_topic_ids[]', topicId);
            });


            // Validate answers: A and B required, C and D optional
            let hasError = false;
            let errorMsg = '';
            document.querySelectorAll('#edit-answers-container .answer-choice').forEach((choice, idx) => {
                const answerTypeRadio = choice.querySelector('input[type="radio"][name="edit_answer_type_' + idx + '"]:checked');
                const isImage = answerTypeRadio && answerTypeRadio.value === 'image';
                let answerText = '';
                let answerImage = null;
                let hasExistingImage = false;
                let existingImageUrl = '';
                // Always get the text value
                answerText = choice.querySelector('.edit-answer-text-container textarea').value.trim();
                // Always check for existing image in preview
                const previewImg = choice.querySelector(`#edit-answer-image-preview-${idx} img`);
                if (previewImg && previewImg.src && !previewImg.classList.contains('hidden') && previewImg.src !== window.location.href && previewImg.src !== '') {
                    hasExistingImage = true;
                    existingImageUrl = previewImg.src;
                }
                // Always check for new file
                answerImage = choice.querySelector('input[type="file"][name="edit_answer_image_' + idx + '"]').files[0] || null;

                // A and B required, C and D optional but if filled must be valid
                if (idx < 2) {
                    if (!answerText && !answerImage && !hasExistingImage) {
                        hasError = true;
                        errorMsg = `Answer ${String.fromCharCode(65 + idx)} must have either text or an image.`;
                        return;
                    }
                } else {
                    // For C and D, skip if all fields are empty
                    if (!answerText && !answerImage && !hasExistingImage) {
                        return; // skip appending this answer
                    }
                }
                // Only append if required or has content
                // Always append a value for answer_image if there is an existing image and no new file
                if (isImage) {
                    if (answerImage) {
                        // If a new file is selected, use it
                        formData.append(`answers[${idx}][answer_image]`, answerImage);
                    } else if (hasExistingImage && existingImageUrl) {
                        // If no new file, but there is an existing image, send the path
                        formData.append(`answers[${idx}][answer_image]`, existingImageUrl);
                    }
                }
                // Append the rest of the answer fields as before
                const answerIdInput = choice.querySelector('input[type="hidden"][name="edit_answer_id_' + idx + '"]');
                if (answerIdInput) {
                    formData.append(`answers[${idx}][id]`, answerIdInput.value);
                }
                formData.append(`answers[${idx}][type]`, isImage ? 'image' : 'text');
                formData.append(`answers[${idx}][is_correct]`, document.querySelector('input[type="radio"][name="edit_correct_answer"]:checked') && document.querySelector('input[type="radio"][name="edit_correct_answer"]:checked').value == idx ? '1' : '0');
                formData.append(`answers[${idx}][text]`, answerText);
            });
            if (hasError) {
                alert(errorMsg);
                return;
            }

            // If validation passes, append answers to formData
            document.querySelectorAll('#edit-answers-container .answer-choice').forEach((choice, idx) => {
                const answerTypeRadio = choice.querySelector('input[type="radio"][name="edit_answer_type_' + idx + '"]:checked');
                const isImage = answerTypeRadio && answerTypeRadio.value === 'image';
                let answerText = '';
                let answerImage = null;
                if (isImage) {
                    answerImage = choice.querySelector('input[type="file"][name="edit_answer_image_' + idx + '"]').files[0] || null;
                } else {
                    answerText = choice.querySelector('.edit-answer-text-container textarea').value.trim();
                }
                const answerIdInput = choice.querySelector('input[type="hidden"][name="edit_answer_id_' + idx + '"]');
                if (answerIdInput) {
                    formData.append(`answers[${idx}][id]`, answerIdInput.value);
                }
                formData.append(`answers[${idx}][type]`, isImage ? 'image' : 'text');
                formData.append(`answers[${idx}][is_correct]`, document.querySelector('input[type="radio"][name="edit_correct_answer"]:checked') && document.querySelector('input[type="radio"][name="edit_correct_answer"]:checked').value == idx ? '1' : '0');
                formData.append(`answers[${idx}][text]`, answerText);
                if (isImage && answerImage) {
                    formData.append(`answers[${idx}][answer_image]`, answerImage);
                }
            });
            const questionImageInput = document.getElementById('edit-question-image-upload');
            if (questionImageInput && questionImageInput.files.length > 0) {
                formData.append('question_image', questionImageInput.files[0]);
            }
            // Add remove_image flag if user clicked remove
            if (typeof editRemoveQuestionImageFlag !== 'undefined' && editRemoveQuestionImageFlag) {
                formData.append('remove_image', '1');
            }
            fetch(`/question/${currentEditQuestionId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: (() => { formData.append('_method', 'PUT'); return formData; })()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Question updated successfully!');
                    // Reset remove image flag after successful save
                    if (typeof editRemoveQuestionImageFlag !== 'undefined') {
                        editRemoveQuestionImageFlag = false;
                    }
                    closeEditModalHandler();
                    location.reload();
                } else {
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
            // --- End main save logic ---
        });
    }
}

// Populate answers in the edit modal
function populateAnswersInEditModal(answers) {
    const editAnswersContainer = document.getElementById('edit-answers-container');
    if (!editAnswersContainer) return;
    
    // Clear existing answers
    editAnswersContainer.innerHTML = '';
    
    // Create 4 answer slots (A, B, C, D)
    const answerLabels = ['A', 'B', 'C', 'D'];
    
    for (let i = 0; i < 4; i++) {
        const label = answerLabels[i];
        const answer = answers[i] || { AnswerID: '', AnswerText: '', ExpectedAnswer: 'N', AnswerImage: '', AnswerType: 'T' };
        const isCorrect = answer.ExpectedAnswer === 'Y';
        const isRequired = i < 2; // First 2 are required
        const isImage = answer.AnswerType === 'I' || answer.AnswerImage;
        
        const answerDiv = document.createElement('div');
        answerDiv.className = 'answer-choice border rounded-lg p-3 bg-gray-50 dark:bg-zinc-700';
        
        answerDiv.innerHTML = `
            <input type="hidden" name="edit_answer_id_${i}" value="${answer.AnswerID || ''}">
            <div class="flex items-start gap-3">
                <div class="flex items-center">
                    <input type="radio" name="edit_correct_answer" value="${i}" 
                           class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500"
                           ${isCorrect ? 'checked' : ''}>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-medium text-sm">${label}.</span>
                        <span class="text-xs text-gray-500">Mark as correct answer</span>
                        <span class="text-xs ${isRequired ? 'text-red-500' : 'text-violet-500'} font-medium">
                            ${isRequired ? 'Required' : 'Optional'}
                        </span>
                    </div>
                    <div class="mb-2 flex gap-4 items-center">
                        <label class="flex items-center gap-1 text-xs font-medium">
                            <input type="radio" name="edit_answer_type_${i}" value="text" 
                                   class="edit-answer-type-radio text-violet-600 focus:ring-violet-500"
                                   ${!isImage ? 'checked' : ''}>
                            Text
                        </label>
                        <label class="flex items-center gap-1 text-xs font-medium">
                            <input type="radio" name="edit_answer_type_${i}" value="image" 
                                   class="edit-answer-type-radio text-violet-600 focus:ring-violet-500"
                                   ${isImage ? 'checked' : ''}>
                            Image
                        </label>
                    </div>
                    
                    <!-- Text Container -->
                    <div class="edit-answer-text-container ${isImage ? 'hidden' : ''}">
                        <textarea class="w-full p-2 border border-gray-300 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-800 text-gray-900 dark:text-white" 
                                  rows="2" 
                                  placeholder="Enter answer choice ${label}${isRequired ? '' : ' (optional)'}" 
                                  data-answer-id="${answer.AnswerID}"
                                  ${isRequired ? 'required' : ''}>${answer.AnswerText || ''}</textarea>
                    </div>
                    
                    <!-- Image Container -->
                    <div class="edit-answer-image-container ${!isImage ? 'hidden' : ''}">
                        <div class="flex flex-col items-center justify-center border-2 border-dashed border-violet-400 rounded-lg p-3 bg-violet-50 hover:bg-violet-100 transition cursor-pointer">
                            <input type="hidden" name="edit_answer_id_${i}" value="${answer.AnswerID || ''}">
                            <label for="edit-answer-image-upload-${i}" class="text-violet-600 font-semibold cursor-pointer mb-1">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M4 12l4-4a2 2 0 012.828 0l2.344 2.344a2 2 0 002.828 0L20 8M4 12v4a2 2 0 002 2h12a2 2 0 002-2v-4"></path>
                                </svg>
                                Upload an image
                            </label>
                            <input id="edit-answer-image-upload-${i}" 
                                   name="edit_answer_image_${i}" 
                                   type="file" 
                                   accept="image/png, image/jpeg, image/gif" 
                                   class="hidden" />
                            <span class="text-gray-500 text-sm">PNG, JPG, GIF up to 2MB</span>
                        </div>
                        <div id="edit-answer-image-preview-${i}" class="mt-2 ${answer.AnswerImage ? '' : 'hidden'}">
                            <img class="max-h-32 mx-auto rounded shadow" 
                                 src="${answer.AnswerImage ? (answer.AnswerImage.startsWith('http') ? answer.AnswerImage : window.location.origin + '/' + answer.AnswerImage.replace(/^\//, '')) : ''}" 
                                 alt="Answer image preview">
                            <button type="button" class="mt-2 text-xs text-red-600 edit-remove-answer-image">Remove Image</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        editAnswersContainer.appendChild(answerDiv);
    }
    
    // Reinitialize answer type switching and image preview handlers
    initializeEditAnswerHandlers();
}

// Initialize edit answer handlers (type switching, image preview)
function initializeEditAnswerHandlers() {
    // Prevent selecting correct answer for empty inputs
    document.querySelectorAll('#edit-answers-container .answer-choice').forEach(function(choice, idx) {
        const correctRadio = choice.querySelector('input[type="radio"][name="edit_correct_answer"]');
        if (!correctRadio) return;
        correctRadio.addEventListener('click', function(e) {
            // Check if this answer has text or image
            const textArea = choice.querySelector('.edit-answer-text-container textarea');
            const imageInput = choice.querySelector('input[type="file"]');
            const imagePreview = choice.querySelector('img');
            let hasContent = false;
            if (!choice.classList.contains('hidden')) {
                if (!choice.querySelector('.edit-answer-text-container').classList.contains('hidden')) {
                    hasContent = textArea && textArea.value.trim().length > 0;
                } else if (!choice.querySelector('.edit-answer-image-container').classList.contains('hidden')) {
                    hasContent = (imageInput && imageInput.files.length > 0) || (imagePreview && imagePreview.src && !imagePreview.classList.contains('hidden'));
                }
            }
            if (!hasContent) {
                e.preventDefault();
                alert('You can only mark a filled answer as correct. Please enter text or upload an image first.');
                correctRadio.checked = false;
            }
        });
    });
    // Answer type switching
    document.querySelectorAll('#edit-answers-container .answer-choice').forEach(function(choice) {
        const radios = choice.querySelectorAll('.edit-answer-type-radio');
        radios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                const textContainer = radio.closest('.answer-choice').querySelector('.edit-answer-text-container');
                const imageContainer = radio.closest('.answer-choice').querySelector('.edit-answer-image-container');
                if (this.value === 'text') {
                    textContainer.classList.remove('hidden');
                    imageContainer.classList.add('hidden');
                } else {
                    textContainer.classList.add('hidden');
                    imageContainer.classList.remove('hidden');
                }
            });
        });

        // Image upload and preview
        const imageInput = choice.querySelector('input[type="file"]');
        const previewDiv = choice.querySelector('[id^="edit-answer-image-preview-"]');
        const previewImg = previewDiv ? previewDiv.querySelector('img') : null;
        const removeBtn = previewDiv ? previewDiv.querySelector('.edit-remove-answer-image') : null;

        if (imageInput && previewDiv && previewImg && removeBtn) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        previewImg.src = event.target.result;
                        previewDiv.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Track image removal for backend
            removeBtn.addEventListener('click', function() {
                imageInput.value = '';
                previewDiv.classList.add('hidden');
                previewImg.src = '';
                // Set a flag on the answer div to indicate removal
                choice.setAttribute('data-remove-image', '1');
            });
        }
    });
}

// Enhanced save edit function to handle both text and images
function saveQuestionEdit() {
    const questionText = document.getElementById('edit-question-text').value.trim();
    if (!questionText) {
        alert('Please fill in your question.');
        return;
    }
    
    if (selectedTopicsForEdit.size === 0) {
        alert('Please select a default topic before saving.');
        return;
    }
    
    // Collect answer data
    const answers = [];
    const correctAnswerRadio = document.querySelector('input[name="edit_correct_answer"]:checked');
    
    if (!correctAnswerRadio) {
        alert('Please select a correct answer.');
        return;
    }
    
    const correctAnswerIndex = parseInt(correctAnswerRadio.value);
    
    // Collect all answers
    document.querySelectorAll('#edit-answers-container .answer-choice').forEach((choice, index) => {
        const answerIdInput = choice.querySelector('textarea[data-answer-id]');
        const answerId = answerIdInput ? answerIdInput.dataset.answerId : null;
        
        const typeRadio = choice.querySelector(`input[name="edit_answer_type_${index}"]:checked`);
        const isImage = typeRadio && typeRadio.value === 'image';
        
        let answerText = '';
        let hasContent = false;
        
        if (isImage) {
            const imageInput = choice.querySelector('input[type="file"]');
            const existingImage = choice.querySelector('img');
            
            if (imageInput && imageInput.files.length > 0) {
                hasContent = true;
                answerText = ''; // Will be handled separately for file upload
            } else if (existingImage && existingImage.src) {
                hasContent = true;
                answerText = ''; // Keep existing image
            }
        } else {
            const textarea = choice.querySelector('textarea');
            answerText = textarea ? textarea.value.trim() : '';
            hasContent = answerText.length > 0;
        }
        
        // Include answer if it has content or if it's required (first 2)
        if (hasContent || index < 2) {
            answers.push({
                id: answerId,
                text: answerText,
                is_correct: index === correctAnswerIndex,
                type: isImage ? 'image' : 'text',
                index: index
            });
        }
    });
    
    // Validate at least 2 answers
    const filledAnswers = answers.filter(a => a.text || a.type === 'image');
    if (filledAnswers.length < 2) {
        alert('Please provide at least 2 answer choices.');
        return;
    }
    
    // Prepare form data
    const formData = new FormData();
    formData.append('QuestionText', questionText);
    formData.append('selected_topic_ids', Array.from(selectedTopicsForEdit));
    
    // Add question image if updated
    const questionImageInput = document.getElementById('edit-question-image-upload');
    if (questionImageInput && questionImageInput.files.length > 0) {
        formData.append('question_image', questionImageInput.files[0]);
    }
    
    // Save question first
    fetch(`/question/${currentEditQuestionId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({
            QuestionText: questionText,
            selected_topic_ids: Array.from(selectedTopicsForEdit)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Now update answers
            return fetch(`/question/${currentEditQuestionId}/answers`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({ answers })
            });
        } else {
            throw new Error(data.message || 'Failed to update question');
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Question updated successfully!');
            closeEditModalHandler();
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to update answers');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating question: ' + error.message);
    });
}

// Add this to your existing DOMContentLoaded event listener
document.addEventListener('DOMContentLoaded', function() {
    // The saveEditBtn click handler is now defined with enhanced logic below. Do not re-add here.
    
    // Make sure the edit modal handlers are properly initialized
    document.querySelectorAll('.edit-question-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            currentEditQuestionId = this.dataset.questionId;
            loadQuestionForEdit(currentEditQuestionId);
        });
    });
});

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

        // Enhanced save function with image upload support
        saveEditBtn.addEventListener('click', function() {
            // Check if question text is filled
            const questionText = document.getElementById('edit-question-text').value.trim();
            if (!questionText) {
                alert('Please fill in your question.');
                return;
            }
            
            // Check if at least one topic is selected
            if (selectedTopicsForEdit.size === 0) {
                alert('Please select a default topic before saving.');
                return;
            }
            
            // Build FormData for edit (including answers array)
            const formData = new FormData();
            // Question text
            formData.append('QuestionText', document.getElementById('edit-question-text').value.trim());
            // Selected topics
            selectedTopicsForEdit.forEach(topicId => {
                formData.append('selected_topic_ids[]', topicId);
            });

            // Build answers array from modal
            document.querySelectorAll('#edit-answers-container .answer-choice').forEach((choice, idx) => {
                const answerTypeRadio = choice.querySelector('input[type="radio"][name="edit_answer_type_' + idx + '"]:checked');
                const isImage = answerTypeRadio && answerTypeRadio.value === 'image';
                let answerText = '';
                let answerImage = null;
                let hasContent = false;
                if (isImage) {
                    answerImage = choice.querySelector('input[type="file"][name="edit_answer_image_' + idx + '"]').files[0] || null;
                    // Check for existing image preview (for already saved answers)
                    const existingImage = choice.querySelector('.edit-answer-image-preview-img')?.getAttribute('src');
                    hasContent = !!answerImage || (existingImage && !existingImage.includes('placeholder'));
                } else {
                    answerText = choice.querySelector('.edit-answer-text-container textarea').value.trim();
                    hasContent = answerText.length > 0;
                }
                // Always include A and B (idx 0,1), only include C/D (idx 2,3) if they have content
                if (idx < 2 || hasContent) {
                    // Only append answer ID if there is content or it's A/B
                    if (idx < 2 || hasContent) {
                        const answerIdInput = choice.querySelector('input[type="hidden"][name="edit_answer_id_' + idx + '"]');
                        if (answerIdInput && answerIdInput.value) {
                            formData.append(`answers[${idx}][id]`, answerIdInput.value);
                        }
                    }
                    formData.append(`answers[${idx}][type]`, isImage ? 'image' : 'text');
                    formData.append(`answers[${idx}][is_correct]`, choice.querySelector('input[type="radio"][name="edit_correct_answer"]:checked') && choice.querySelector('input[type="radio"][name="edit_correct_answer"]:checked').value == idx ? '1' : '0');
                    formData.append(`answers[${idx}][text]`, answerText);
                    if (isImage && answerImage) {
                        formData.append(`answers[${idx}][answer_image]`, answerImage);
                    }
                    // If image was removed, send a flag for backend
                    if (choice.getAttribute('data-remove-image') === '1') {
                        formData.append(`answers[${idx}][remove_image]`, '1');
                    }
                }
            });

            // Add question image if present
            const questionImageInput = document.getElementById('edit-question-image-upload');
            if (questionImageInput && questionImageInput.files.length > 0) {
                formData.append('question_image', questionImageInput.files[0]);
            }

            fetch(`/question/${currentEditQuestionId}`, {
                method: 'POST', // Use POST with _method=PUT for Laravel compatibility with FormData
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: (() => { formData.append('_method', 'PUT'); return formData; })()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Question updated successfully!');
                    closeEditModalHandler();
                    location.reload();
                } else {
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
            

            document.querySelectorAll('#add-answers-container .answer-choice').forEach((choice, index) => {
                const answerTypeRadio = choice.querySelector('input[type="radio"][name="answer_type_' + index + '"]:checked');
                if (answerTypeRadio) {
                    if (answerTypeRadio.value === 'text') {
                        const textarea = choice.querySelector('textarea');
                        const answerText = textarea ? textarea.value.trim() : '';
                        if (answerText) {
                            answers.push({
                                text: answerText,
                                type: 'text',
                                is_correct: index == correctAnswerIndex
                            });
                            filledAnswers++;
                        }
                    } else if (answerTypeRadio.value === 'image') {
                        const imageInput = choice.querySelector('input[type="file"]');
                        if (imageInput && imageInput.files.length > 0) {
                            answers.push({
                                text: '',
                                type: 'image',
                                is_correct: index == correctAnswerIndex,
                                image: imageInput.files[0]
                            });
                            filledAnswers++;
                        }
                    }
                }
            });
            
            // Require at least 2 answers
            if (filledAnswers < 2) {
                alert('Please provide at least 2 answer choices.');
                return;
            }
            

            // Check if the selected correct answer is filled (text or image)
            const correctChoice = document.querySelectorAll('#add-answers-container .answer-choice')[correctAnswerIndex];
            let isCorrectFilled = false;
            if (correctChoice) {
                const answerTypeRadio = correctChoice.querySelector('input[type="radio"][name="answer_type_' + correctAnswerIndex + '"]:checked');
                if (answerTypeRadio) {
                    if (answerTypeRadio.value === 'text') {
                        const textarea = correctChoice.querySelector('textarea');
                        if (textarea && textarea.value.trim()) {
                            isCorrectFilled = true;
                        }
                    } else if (answerTypeRadio.value === 'image') {
                        const imageInput = correctChoice.querySelector('input[type="file"]');
                        if (imageInput && imageInput.files.length > 0) {
                            isCorrectFilled = true;
                        }
                    }
                }
            }
            if (!isCorrectFilled) {
                alert('The selected correct answer must have text or an image.');
                return;
            }
            
            // Get selected topic ID
            const topicId = Array.from(addSelectedTopics)[0];
            
            // Use FormData to send text and image answers
            const formData = new FormData();
            formData.append('QuestionText', questionText);
            formData.append('selected_topic_ids[]', topicId);
            answers.forEach((ans, idx) => {
                formData.append(`answers[${idx}][is_correct]`, ans.is_correct ? '1' : '0');
                if (ans.type === 'text') {
                    formData.append(`answers[${idx}][text]`, ans.text);
                    formData.append(`answers[${idx}][type]`, 'text');
                } else if (ans.type === 'image') {
                    formData.append(`answers[${idx}][text]`, '');
                    formData.append(`answers[${idx}][type]`, 'image');
                    if (ans.image) {
                        formData.append(`answers[${idx}][answer_image]`, ans.image);
                    }
                }
            });
            // Add question image if present
            const questionImageInput = document.getElementById('question-image-upload');
            if (questionImageInput && questionImageInput.files.length > 0) {
                formData.append('question_image', questionImageInput.files[0]);
            }

            fetch('/question', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: formData
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
