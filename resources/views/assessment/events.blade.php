@extends('layout.appMain')

@section('content')
<!-- CSRF token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

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
            <div class="ml-auto flex items-center gap-3">
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
                                class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:hover:bg-zinc-700/50 dark:border-zinc-600">
                                <td class="w-4 p-3">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            class="row-checkbox w-4 h-4 border-gray-300 rounded dark:bg-zinc-700 dark:border-zinc-500 dark:checked:bg-violet-500">
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
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-zinc-800">
            <form id="editEventForm">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-zinc-800">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                Edit Event
                            </h3>
                            <div class="mt-4 space-y-4">
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
                                
                                <!-- Category Selection -->
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
                                
                                <!-- Topic Selection -->
                                <div id="topics-section" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Select Topics for Event</label>
                                    <div class="max-h-32 overflow-y-auto border border-gray-300 rounded-md p-3 dark:border-zinc-600 dark:bg-zinc-700" id="eventTopicsContainer">
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

<div class="mt-4">
    {{ $records->links('pagination::tailwind') }}
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('checkbox-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

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
                                       name="selected_topic_ids[]" 
                                       value="${topic.TopicID}"
                                       ${isSelected ? 'checked' : ''}
                                       class="event-topic-checkbox w-4 h-4 border-gray-300 rounded bg-white accent-violet-600">
                                <label for="event_topic_${topic.TopicID}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    ${topic.TopicName} (ID: ${topic.TopicID})
                                </label>
                            `;
                            container.appendChild(div);
                        });
                        document.getElementById('topics-section').classList.remove('hidden');
                    } else {
                        container.innerHTML = '<div class="text-center py-2 text-gray-500">No topics available for this category</div>';
                        document.getElementById('topics-section').classList.remove('hidden');
                    }
                } else {
                    alert('Error loading category topics: ' + data.message);
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

    function updateEvent() {
        if (!currentEventId) return;

        const formData = new FormData(document.getElementById('editEventForm'));
        const data = Object.fromEntries(formData);
        
        // Get selected topic IDs
        const selectedTopics = [];
        document.querySelectorAll('.event-topic-checkbox:checked').forEach(checkbox => {
            selectedTopics.push(checkbox.value);
        });
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
                location.reload(); // Refresh the page to show updated data
            } else {
                alert('Error: ' + data.message);
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
        const modal = document.getElementById('editEventModal');
        if (event.target === modal) {
            closeEditModal();
        }
    });
</script>
