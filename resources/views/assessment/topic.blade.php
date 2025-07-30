@extends('layout.appMain')

@section('content')
    <div class="col-span-12 xl:col-span-6">
        <div class="card dark:bg-zinc-800 dark:border-zinc-600">
            <!-- Header with title and buttons -->
            <div class="card-body border-b border-gray-100 dark:border-zinc-600 flex items-center justify-between">
                <h6 class="mb-1 text-gray-700 text-15 dark:text-gray-100">Assessment Topics</h6>

                <div class="flex items-center gap-3">
                    <!-- Delete button (hidden by default) -->
                    <button id="bulk-delete-btn" type="button"
                        class="hidden px-6 py-2 text-white bg-red-500 rounded hover:bg-red-600">
                        Delete
                    </button>

                    <!-- Add button -->
                    <button type="button"
                        class="px-8 py-2 text-white btn bg-violet-500 border-violet-500 hover:bg-violet-600 hover:border-violet-600 focus:bg-violet-600 focus:border-violet-600 focus:ring focus:ring-violet-500/30 active:bg-violet-600 active:border-violet-600">
                        Add
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="relative rounded-lg" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                    <table class="w-full min-w-[900px] text-xs text-center text-gray-500">
                        <thead
                            class="text-xs text-gray-700 uppercase dark:text-gray-100 bg-gray-50 dark:bg-zinc-700/50 sticky top-0 z-10">
                            <tr>
                                <th class="p-4">
                                    <div class="flex items-center">
                                        <input id="checkbox-all" type="checkbox"
                                            class="w-4 h-4 border-gray-300 rounded dark:bg-zinc-700 dark:border-zinc-500 checked:bg-blue-600 dark:checked:bg-blue-600">
                                        <label for="checkbox-all" class="sr-only">checkbox</label>
                                    </div>
                                </th>
                                <th class="px-6 py-3">Topic Name</th>
                                <th class="px-6 py-3">Actions</th>
                                <th class="px-6 py-3">Date Created</th>
                                <th class="px-6 py-3">Date Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $row)
                                <tr class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:hover:bg-zinc-700/50 dark:border-zinc-600">
                                    <td class="w-4 p-4">
                                        <div class="flex items-center">
                                            <input type="checkbox"
                                                class="row-checkbox w-4 h-4 border-gray-300 rounded dark:bg-zinc-700 dark:border-zinc-500 dark:checked:bg-violet-500">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">{{ $row->TopicName }}</td>

                                    <!-- Action buttons -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="relative inline-block dropdown">
                                            <button type="button"
                                                class="dropdown-toggle flex items-center justify-center w-8 h-8 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 focus:ring focus:ring-gray-200 dark:bg-zinc-600 dark:text-gray-100 dark:hover:bg-zinc-500">
                                                <i class="bx bx-dots-vertical text-lg"></i>
                                            </button>
                                            <div
                                                class="dropdown-menu hidden absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 dark:bg-zinc-700 z-20 ">
                                                <div class="p-1 flex flex-col gap-2">
                                                    <button type="button"
                                                        class="w-full flex items-center justify-center gap-1 px-3 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                                        <i class="mdi mdi-pencil text-base"></i>
                                                        <span>Edit</span>
                                                    </button>
                                                    <button type="button"
                                                        class="w-full flex items-center justify-center gap-1 px-3 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                                        <i class="mdi mdi-trash-can text-base"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-7 py-4">{{ \Carbon\Carbon::parse($row->DateCreate)->format('d M Y') }}</td>
                                    <td class="px-7 py-4">{{ \Carbon\Carbon::parse($row->DateUpdate)->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">No topics found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                
            </div>
        </div>
        {{-- Pagination --}}
                <div class="mt-4">
                    {{ $records->links('pagination::tailwind') }}
                </div>
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

        // Dropdown toggle for actions
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
