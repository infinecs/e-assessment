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
            <div class="card-body border-b border-gray-100 dark:border-zinc-600 flex items-center">
                <div class="ml-auto">
                    <button id="bulk-delete-btn" type="button"
                        class="hidden px-6 py-2 text-white bg-red-500 rounded hover:bg-red-600">
                        Delete
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="isolate">
                    <div class="relative rounded-lg" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                        <table class="w-full min-w-[1100px] text-sm text-center text-gray-500">
                            <thead
                                class="text-xs text-gray-700 uppercase dark:text-gray-100 bg-gray-50 dark:bg-zinc-700 sticky top-0 z-40 shadow-sm">
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
                                <th class="px-3 py-2">Name</th>
                                <th class="px-3 py-2">Phone Number</th>
                                <th class="px-3 py-2">Email</th>
                                <th class="px-3 py-2">Score</th>
                                <th class="px-3 py-2">Date Answered</th>
                                <th class="px-3 py-2">Actions</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $row)
                                <tr data-id="{{ $row->AssessmentID }}"
                                    class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:border-zinc-600">
                                    <td class="w-4 p-3">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox"
                                                class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white
                                                    checked:bg-blue-600 checked:border-blue-600
                                                    dark:bg-zinc-700 dark:border-zinc-500
                                                    dark:checked:bg-blue-600 dark:checked:border-zinc-500">
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">{{ $row->participant->name ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $row->participant->phone_number ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $row->participant->email ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $row->TotalScore }} / {{ $row->TotalQuestion }}</td>
                                    <td class="px-3 py-2">{{ \Carbon\Carbon::parse($row->DateCreate)->format('d M Y') }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <button type="button"
                                            class="view-details px-4 py-1 text-sm bg-blue-500 text-blue-600 rounded hover:underline"
                                            data-id="{{ $row->AssessmentID }}">
                                            View
                                        </button>
                                    </td>
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
        </div>

        @if (method_exists($records, 'links'))
    <div class="mt-4 flex">
        {{ $records->links('pagination::tailwind') }}
    </div>
@endif
</div>

<!-- Modal -->

<div id="details-modal"
    class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 transition">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-2xl mx-2 transform scale-100 transition-all overflow-hidden border border-gray-200 dark:border-zinc-700 flex flex-col" style="max-height:90vh;">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700 flex justify-between items-center bg-gray-50 dark:bg-zinc-800">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">
                <i class="fas fa-file-alt mr-2 text-blue-500"></i> Assessment Details
            </h2>
            <button id="close-modal"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white text-2xl font-bold">
                &times;
            </button>
        </div>
        <!-- Body -->
        <div class="p-6 overflow-y-auto flex-1 space-y-4 bg-white dark:bg-zinc-900" id="modal-content" style="min-height:100px; max-height:60vh;">
            <div class="text-center text-gray-600 dark:text-gray-300">Loading...</div>
        </div>
        <!-- Footer -->
        <div class="px-6 py-3 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-right">
            <button id="close-modal-footer"
                class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-base font-semibold shadow">
                Close
            </button>
        </div>
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
            bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
        }

        if (selectAll) {
            selectAll.addEventListener('change', () => {
                rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                updateBulkDeleteVisibility();
            });
        }

        rowCheckboxes.forEach(cb => cb.addEventListener('change', updateBulkDeleteVisibility));

        bulkDeleteBtn.addEventListener('click', () => {
            const selectedIds = Array.from(rowCheckboxes)
                .map(cb => cb.checked ? cb.closest('tr').dataset.id : null)
                .filter(id => id !== null);

            if (selectedIds.length === 0) return;
            if (!confirm(`Delete ${selectedIds.length} records?`)) return;

            fetch('{{ route('assessment.bulkDelete') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ids: selectedIds
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert('Failed to delete records.');
                    }
                })
                .catch(err => alert('Error deleting records.'));
        });
    });

    
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('details-modal');
    const modalContent = document.getElementById('modal-content');
    const closeModal = document.getElementById('close-modal');
    const closeModalFooter = document.getElementById('close-modal-footer'); // optional if footer button exists

    // Function to open modal and load details
    function openModal(id) {
        modal.classList.remove('hidden');
        modalContent.innerHTML = 'Loading...';

        fetch(`/assessment/${id}/details`)
            .then(res => res.json())
            .then(data => {
    if (data.status === 'success') {
        let html = '';


        data.results.forEach(item => {
            const isCorrect = item.participantAnswer === item.correctAnswer;
            const correctClass = isCorrect
                ? 'bg-green-50 border-green-400 text-green-700'
                : 'bg-red-50 border-red-400 text-red-700';

            html += `
                <div class="border rounded-lg p-4 mb-4 shadow-sm bg-gray-50 dark:bg-zinc-800">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 text-xs md:text-sm">
                        <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 rounded mr-2 text-xs">Q</span>
                        ${item.question}
                    </h3>
                    <div class="space-y-2">
                        <div class="p-2 border rounded ${correctClass} text-xs md:text-sm">
                            <span class="font-semibold">Your Answer:</span>
                            ${item.participantAnswer || '-'}
                        </div>
                        ${!isCorrect ? `
                        <div class="p-2 border rounded bg-green-50 border-green-400 text-green-700 text-xs md:text-sm">
                            <span class="font-semibold">Correct Answer:</span>
                            ${item.correctAnswer || '-'}
                        </div>` : ''}
                    </div>
                </div>
            `;
        });

        if (!data.results.length) {
            html = '<div class="text-center text-gray-600 dark:text-gray-300 text-sm">No details found.</div>';
        }

        document.getElementById('modal-content').innerHTML = html;
    } else {
        document.getElementById('modal-content').innerHTML = '<div class="text-sm text-gray-600">Failed to load details.</div>';
    }
})
            .catch(() => {
                modalContent.innerHTML = 'Error fetching data.';
            });
    }

    // Attach click to all "View Details" buttons
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', () => {
            openModal(btn.dataset.id);
        });
    });

    // Close modal on clicking close buttons
    closeModal?.addEventListener('click', () => modal.classList.add('hidden'));
    closeModalFooter?.addEventListener('click', () => modal.classList.add('hidden'));

});
</script>
