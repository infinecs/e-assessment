@extends('layout.appMain')

@section('content')
<div class="col-span-12 xl:col-span-6">
    <div class="card dark:bg-zinc-800 dark:border-zinc-600">
        <div class="card-body border-b border-gray-100 dark:border-zinc-600 flex items-center justify-between">
            <h6 class="mb-1 text-gray-700 text-15 dark:text-gray-100">Assessment Results</h6>

            <!-- Bulk Delete button -->
            <button id="bulk-delete-btn" type="button"
                class="hidden px-6 py-2 text-white bg-red-500 rounded hover:bg-red-600">
                Delete
            </button>
        </div>
        <div class="card-body">
            <div class="relative rounded-lg" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                <table class="w-full min-w-[1100px] text-sm text-center text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase dark:text-gray-100 bg-gray-50 dark:bg-zinc-700/50 sticky top-0 z-10">
                        <tr>
                            <th class="p-4">
                                <div class="flex items-center justify-center">
                                    <input id="checkbox-all" type="checkbox"
                                        class="w-4 h-4 border-gray-300 rounded 
                                        dark:bg-zinc-700 dark:border-zinc-500 
                                        checked:bg-blue-600 dark:checked:bg-blue-600">
                                    <label for="checkbox-all" class="sr-only">checkbox</label>
                                </div>
                            </th>
                            <th class="px-6 py-3">Assessment ID</th>
                            <th class="px-6 py-3">Question ID</th>
                            <th class="px-6 py-3">Answer ID</th>
                            <th class="px-6 py-3">Date Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $row)
                            <tr class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:border-zinc-600">
                                <td class="w-4 p-4">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox"
                                            class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white
                                            checked:bg-blue-600 checked:border-blue-600
                                            dark:bg-zinc-700 dark:border-zinc-500
                                            dark:checked:bg-blue-600 dark:checked:border-blue-600">
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $row->AssessmentID }}</td>
                                <td class="px-6 py-4">{{ $row->QuestionID }}</td>
                                <td class="px-6 py-4">{{ $row->AnswerID ?? '-' }}</td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($row->DateCreate)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            
        </div>
        
    </div>
    {{-- Pagination --}}
            @if(method_exists($records, 'links'))
                <div class="mt-4 flex">
                    {{ $records->links('pagination::tailwind') }}
                </div>
            @endif
</div>

@endsection


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('checkbox-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

        function updateBulkDeleteVisibility() {
            const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
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
    });
</script>

