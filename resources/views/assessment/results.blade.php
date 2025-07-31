@extends('layout.appMain')

@section('content')
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
        <!-- Header with right-aligned Delete button -->
        <div class="card-body border-b border-gray-100 dark:border-zinc-600 flex items-center">
            

            <div class="ml-auto">
                <!-- Bulk Delete button -->
                <button id="bulk-delete-btn" type="button"
                    class="hidden px-6 py-2 text-white bg-red-500 rounded hover:bg-red-600">
                    Delete
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="relative rounded-lg" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                <table class="w-full min-w-[1100px] text-sm text-center text-gray-500">
    <thead class="text-xs text-gray-700 uppercase dark:text-gray-100 bg-gray-50 dark:bg-zinc-700/50 sticky top-0 z-10">
        <tr>
            <th class="p-3">
                <div class="flex items-center justify-center">
                    <input id="checkbox-all" type="checkbox"
                        class="w-4 h-4 border-gray-300 rounded 
                        dark:bg-zinc-700 dark:border-zinc-500 
                        checked:bg-blue-600 dark:checked:bg-blue-600">
                    <label for="checkbox-all" class="sr-only">checkbox</label>
                </div>
            </th>
            <th class="px-3 py-2">Assessment ID</th>
            <th class="px-3 py-2">Participant ID</th>
            <th class="px-3 py-2">Total Score</th>
            <th class="px-3 py-2">Total Questions</th>
            <th class="px-3 py-2">Date Created</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $row)
            <tr class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:border-zinc-600">
                <td class="w-4 p-3">
                    <div class="flex items-center justify-center">
                        <input type="checkbox"
                            class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white
                            checked:bg-blue-600 checked:border-blue-600
                            dark:bg-zinc-700 dark:border-zinc-500
                            dark:checked:bg-blue-600 dark:checked:border-blue-600">
                    </div>
                </td>
                <td class="px-3 py-2">{{ $row->AssessmentID }}</td>
                <td class="px-3 py-2">{{ $row->ParticipantID }}</td>
                <td class="px-3 py-2">{{ $row->TotalScore }}</td>
                <td class="px-3 py-2">{{ $row->TotalQuestion }}</td>
                <td class="px-3 py-2">{{ \Carbon\Carbon::parse($row->DateCreate)->format('d M Y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-3 py-2 text-center">No records found</td>
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
