@extends('layout.appMain')

@section('content')
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
            <div class="relative rounded-lg" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                <table class="w-full min-w-[1100px] text-xs text-center text-gray-500 leading-tight">
                    <thead
                        class="text-[11px] text-gray-700 uppercase dark:text-gray-100 bg-gray-50 dark:bg-zinc-700/50 sticky top-0 z-10">
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
                                                    class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                                    <i class="mdi mdi-pencil text-base"></i>
                                                    <span>Edit</span>
                                                </button>
                                                <button type="button"
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
    });
</script>
