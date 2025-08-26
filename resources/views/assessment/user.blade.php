@extends('layout.appMain')

@section('content')

<!-- CSRF token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Custom styles for checkboxes -->
<style>
.row-checkbox:checked,
#checkbox-all:checked {
	background-color: #7c3aed !important; /* violet-600 */
	border-color: #7c3aed !important;
}
.row-checkbox:checked:after,
#checkbox-all:checked:after {
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
#checkbox-all {
	position: relative;
	appearance: none;
	-webkit-appearance: none;
	-moz-appearance: none;
}

</style>
<script>
// Checkbox select-all and row logic
document.addEventListener('DOMContentLoaded', function() {

    function getRowCheckboxes() {
        const boxes = Array.from(document.querySelectorAll('tbody .row-checkbox'));
        boxes.forEach((cb, i) => console.log(`Checkbox ${i}: checked=${cb.checked}`));
        return boxes;
    }
    const selectAll = document.getElementById('checkbox-all');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

    function updateBulkDeleteVisibility() {
        const checkedCount = getRowCheckboxes().filter(cb => cb.checked).length;
        if (bulkDeleteBtn) {
            if (checkedCount > 0) {
                bulkDeleteBtn.style.removeProperty('display');
            } else {
                bulkDeleteBtn.style.display = 'none';
            }
            bulkDeleteBtn.innerHTML = 'Delete';
        }
    }

    function handleRowCheckboxChange() {
        const rowCheckboxes = getRowCheckboxes();
        if (rowCheckboxes.length === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
            return;
        }
        const checkedCount = rowCheckboxes.filter(cb => cb.checked).length;
        console.log('Handle row checkbox change, checked:', checkedCount);
        if (checkedCount === rowCheckboxes.length) {
            selectAll.checked = true;
            selectAll.indeterminate = false;
        } else if (checkedCount === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        } else {
            selectAll.checked = false;
            selectAll.indeterminate = true;
        }
        updateBulkDeleteVisibility();
    }

    // Event delegation for all row-checkboxes
    const tbody = document.querySelector('tbody');
    if (tbody) {
        tbody.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('row-checkbox')) {
                console.log('Row checkbox changed:', e.target.checked);
                handleRowCheckboxChange();
            }
        });
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            getRowCheckboxes().forEach(cb => {
                cb.checked = selectAll.checked;
            });
            handleRowCheckboxChange();
        });
    }

    // Initial state
    handleRowCheckboxChange();

    // If you use AJAX to update the table, call this after update:
    window.refreshUserCheckboxes = function() {
        handleRowCheckboxChange();
    };
});
</script>
<!-- User Checkbox Script -->

<!-- Breadcrumb -->
<div class="grid grid-cols-1 pb-6">
	<div class="md:flex items-center justify-between px-[2px]">
		<h4 class="text-[18px] font-medium text-gray-800 mb-sm-0 grow dark:text-gray-100 mb-2 md:mb-0">
			Users
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
						<i class="font-semibold text-gray-600 align-middle far fa-angle-right text-13 rtl:rotate-180 dark:text-zinc-100"></i>
						<a href="#"
							class="text-sm font-medium text-gray-500 ltr:ml-2 rtl:mr-2 hover:text-gray-900 ltr:md:ml-2 rtl:md:mr-2 dark:text-gray-100 dark:hover:text-white">
							Users
						</a>
					</div>
				</li>
			</ol>
		</nav>
	</div>
</div>

<!-- Main Content -->
<div class="col-span-12 xl:col-span-6">
	<div class="card dark:bg-zinc-800 dark:border-zinc-600">
		<!-- Header with search and buttons -->
		<div class="card-body border-b border-gray-100 dark:border-zinc-600 flex items-center justify-between">
			<!-- Search Bar -->
			<div class="flex items-center gap-3">
				<div class="relative">
					<input type="text" id="searchInput" placeholder="Search users by email..." 
						class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm w-64 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white dark:placeholder-gray-400">
					<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
						<i class="fas fa-search text-gray-400 text-sm"></i>
					</div>
				</div>
				<button type="button" id="performSearchBtn" 
					class="px-4 py-2 bg-violet-500 text-white rounded-lg text-sm hover:bg-violet-600 focus:ring-2 focus:ring-violet-500 focus:ring-offset-1 flex items-center gap-2">
					<i class="fas fa-search"></i>
					Search
				</button>
			</div>
            <div class="ml-auto flex items-center gap-3">
                <!-- Bulk Delete button -->
                <button id="bulk-delete-btn" type="button" onclick="performBulkDelete()" style="display:none;" class="px-6 py-1.5 text-white btn bg-red-600 border-red-600 hover:bg-red-700 hover:border-red-700 focus:bg-red-700 focus:border-red-700 focus:ring focus:ring-red-500/30 active:bg-red-700 active:border-red-700 text-sm">
                    Delete
                </button>
                <!-- Add button -->
                <button type="button" id="add-user-btn"
                    onclick="resetAddUserValidation(); document.getElementById('addUserModal').classList.remove('hidden'); document.getElementById('addUserForm').reset();"
                    class="px-6 py-1.5 text-white btn bg-violet-500 border-violet-500 hover:bg-violet-600 hover:border-violet-600 focus:bg-violet-600 focus:border-violet-600 focus:ring focus:ring-violet-500/30 active:bg-violet-600 active:border-violet-600 text-sm">
                    Add
                </button>
            </div>
		</div>

        <div class="card-body">
            <!-- Users Table -->
			<div class="isolate">
				<div class="relative rounded-lg" style="max-height: 500px; min-height: 350px; overflow-y: auto; overflow-x: auto; display: flex; flex-direction: column; justify-content: flex-start;">
					<table class="w-full min-w-[700px] text-xs text-center text-gray-500 leading-tight">
						<thead class="text-[11px] text-gray-700 uppercase dark:text-gray-100 bg-gray-50 dark:bg-zinc-700 sticky top-0 z-40 shadow-sm">
							<tr>
								<th class="p-3">
									<div class="flex items-center">
										<input id="checkbox-all" type="checkbox" class="w-4 h-4 border-gray-300 rounded bg-white">
										<label for="checkbox-all" class="sr-only">checkbox</label>
									</div>
								</th>
								<th class="px-2 py-1.5">Email</th>
								<th class="px-2 py-1.5">Roles</th>
								<th class="px-2 py-1.5">Date Created</th>
								<th class="px-2 py-1.5">Actions</th>
							</tr>
						</thead>
						<tbody>
                            @php $currentUser = auth()->user(); @endphp
                            @forelse($records as $user)
                                @if($currentUser && $user->id == $currentUser->id)
                                    @continue
                                @endif
                                <tr data-user-id="{{ $user->id }}" class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:hover:bg-zinc-700/50 dark:border-zinc-600">
                                    <td class="w-4 p-3">
                                        <div class="flex items-center">
                                            <input type="checkbox" class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                                        </div>
                                    </td>
                                    <td class="px-2 py-1.5">{{ $user->email }}</td>
                                    <td class="px-2 py-1.5">{{ $user->roles ?? '-' }}</td>
                                    <td class="px-2 py-1.5">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>
                                    <td class="px-2 py-1.5 text-center">
                                        <div class="relative inline-block dropdown">
                                           <button type="button" class="dropdown-toggle flex items-center justify-center w-7 h-7 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 focus:ring focus:ring-gray-200 dark:bg-zinc-600 dark:text-gray-100 dark:hover:bg-zinc-500">
                                               <i class="bx bx-dots-vertical text-base"></i>
                                           </button>
                                           <div class="dropdown-menu hidden absolute right-0 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 dark:bg-zinc-700 z-20">
                                               <div class="p-1 flex flex-col gap-1">
                                                   <button type="button" onclick="editUser({{ $user->id }})" class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                                       <i class="mdi mdi-pencil text-base"></i>
                                                       <span>Edit</span>
                                                   </button>
                                                   <button type="button" onclick="deleteUser({{ $user->id }}, '{{ $user->email }}')" class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                                                       <i class="mdi mdi-trash-can text-base"></i>
                                                       <span>Delete</span>
                                                   </button>
                                               </div>
                                           </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-2 py-1.5 text-center">No users found</td>
                                </tr>
                            @endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Pagination -->
<div class="mt-4">
	{{ $records->links('pagination::tailwind') }}
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center pt-4 px-4 pb-4 text-center sm:block sm:p-0"><!-- removed min-h-screen -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full h-[90vh] dark:bg-zinc-800">
            <form id="addUserForm" class="flex flex-col h-full">
                <div class="bg-white px-4 pt-4 pb-2 sm:p-4 dark:bg-zinc-800 flex-1 overflow-y-auto">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-2 text-center sm:mt-0 sm:ml-2 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">Add User</h3>
                            <div class="mt-2 space-y-3">
                                <div>
                                    <label for="add_user_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input type="email" id="add_user_email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                    <input type="hidden" id="current-user-email" value="{{ auth()->user()->email ?? '' }}">
                                    <ul class="mt-1 text-xs" id="addUserEmailValidation">
                                        <li id="email-format-status" class="text-red-600">• Must be a valid email address</li>
                                        <li id="email-unique-status" class="text-red-600" style="display:none;">• Email already being used</li>
                                    </ul>
                                    <div id="addUserEmailError" class="text-red-600 text-xs hidden"></div>
<script>
// Live email validation for Add User modal
let addUserLastCheckedEmail = '';
let addUserEmailCheckTimeout = null;

function resetAddUserValidation() {
    document.getElementById('email-format-status').className = 'text-red-600';
    const uniqueStatus = document.getElementById('email-unique-status');
    uniqueStatus.style.display = 'none';
    uniqueStatus.className = 'text-red-600';
    document.getElementById('addUserEmailError').classList.add('hidden');
    
    addUserLastCheckedEmail = '';
    if (addUserEmailCheckTimeout) {
        clearTimeout(addUserEmailCheckTimeout);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('add_user_email');
    const formatStatus = document.getElementById('email-format-status');
    const uniqueStatus = document.getElementById('email-unique-status');
    const currentUserEmail = document.getElementById('current-user-email')?.value?.trim().toLowerCase() || '';
    
    if (emailInput && formatStatus && uniqueStatus) {
        // Prevent space input at the keydown level
        emailInput.addEventListener('keydown', function(e) {
            if (e.key === ' ' || e.code === 'Space') {
                e.preventDefault();
            }
        });
        emailInput.addEventListener('input', function() {
            // Convert any uppercase to lowercase and restrict to allowed characters
            let filtered = emailInput.value
                .replace(/[^a-zA-Z0-9@._-]/g, '') // allow a-z, A-Z, 0-9, @ . _ -
                .replace(/\s+/g, '') // remove spaces (redundant, but keep for paste)
                .toLowerCase();
            if (emailInput.value !== filtered) {
                emailInput.value = filtered;
            }
            const email = emailInput.value.trim();
            // Stricter format: only lowercase, no spaces, valid email structure
            const validEmailRegex = /^[a-z0-9._-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
            if (validEmailRegex.test(email)) {
                formatStatus.classList.remove('text-red-600');
                formatStatus.classList.add('text-green-600');
            } else {
                formatStatus.classList.remove('text-green-600');
                formatStatus.classList.add('text-red-600');
            }
            // Uniqueness validation (debounced)
            uniqueStatus.style.display = 'none';
            uniqueStatus.classList.remove('text-green-600');
            uniqueStatus.classList.add('text-red-600');
            if (email && validEmailRegex.test(email)) {
                // Prevent using the current user's email
                if (email === currentUserEmail) {
                    uniqueStatus.style.display = '';
                    uniqueStatus.classList.remove('text-green-600');
                    uniqueStatus.classList.add('text-red-600');
                    return;
                }
                if (email === addUserLastCheckedEmail) return;
                addUserLastCheckedEmail = email;
                clearTimeout(addUserEmailCheckTimeout);
                addUserEmailCheckTimeout = setTimeout(async () => {
                    try {
                        const res = await fetch(`/users/search?query=${encodeURIComponent(email)}`);
                        const data = await res.json();
                        if (data.success && data.users && data.users.data && !data.users.data.some(u => u.email === email)) {
                            uniqueStatus.style.display = 'none';
                        } else if (data.success && data.users && data.users.data && data.users.data.some(u => u.email === email)) {
                            uniqueStatus.style.display = '';
                            uniqueStatus.classList.remove('text-green-600');
                            uniqueStatus.classList.add('text-red-600');
                        } else {
                            uniqueStatus.style.display = 'none';
                        }
                    } catch (err) {
                        uniqueStatus.style.display = 'none';
                    }
                }, 400);
            }
        });
    }
});
</script>
                                </div>
                                <div>
                                    <label for="add_user_roles" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</label>
                                    <select id="add_user_roles" name="roles" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                        <option value="admin" selected>Admin</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="add_user_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                    <div class="relative">
                                        <input type="password" id="add_user_password" name="password" required minlength="8" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white pr-10" oninput="updatePasswordValidation()">
                                        <button type="button" tabindex="-1" onclick="togglePasswordVisibility('add_user_password', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 focus:outline-none">
                                            <i class="fas fa-eye-slash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <ul id="passwordRequirementsList" class="mt-2 text-xs space-y-1">
                                        <li id="pw-length" class="text-red-600">• At least 8 characters</li>
                                        <li id="pw-upper" class="text-red-600">• Uppercase letter</li>
                                        <li id="pw-lower" class="text-red-600">• Lowercase letter</li>
                                        <li id="pw-number" class="text-red-600">• At least one number</li>
                                        <li id="pw-special" class="text-red-600">• At least one special character</li>
                                    </ul>
                                </div>
                                <div>
                                    <label for="add_user_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                                    <div class="relative">
                                        <input type="password" id="add_user_password_confirmation" name="password_confirmation" required minlength="8" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white pr-10" oninput="updatePasswordValidation()">
                                        <button type="button" tabindex="-1" onclick="togglePasswordVisibility('add_user_password_confirmation', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 focus:outline-none">
                                            <i class="fas fa-eye-slash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div id="confirmPasswordStatus" class="mt-2 text-xs text-red-600">• Passwords must match</div>
                                </div>
<script>
// Live password and confirm password validation
function updatePasswordValidation() {
    const password = document.getElementById('add_user_password').value;
    const confirmPassword = document.getElementById('add_user_password_confirmation').value;
    // Password requirements
    const length = password.length >= 8;
    const upper = /[A-Z]/.test(password);
    const lower = /[a-z]/.test(password);
    const number = /[0-9]/.test(password);
    const special = /[^A-Za-z0-9]/.test(password);
    document.getElementById('pw-length').className = length ? 'text-green-600' : 'text-red-600';
    document.getElementById('pw-upper').className = upper ? 'text-green-600' : 'text-red-600';
    document.getElementById('pw-lower').className = lower ? 'text-green-600' : 'text-red-600';
    document.getElementById('pw-number').className = number ? 'text-green-600' : 'text-red-600';
    document.getElementById('pw-special').className = special ? 'text-green-600' : 'text-red-600';
    // Confirm password
    const confirmStatus = document.getElementById('confirmPasswordStatus');
    if (confirmPassword.length > 0) {
        if (password === confirmPassword) {
            confirmStatus.textContent = '• Passwords match';
            confirmStatus.className = 'mt-2 text-xs text-green-600';
        } else {
            confirmStatus.textContent = '• Passwords must match';
            confirmStatus.className = 'mt-2 text-xs text-red-600';
        }
    } else {
        confirmStatus.textContent = '• Passwords must match';
        confirmStatus.className = 'mt-2 text-xs text-red-600';
    }
}
</script>
<script>
// Toggle password visibility for Add User modal (FontAwesome version)
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}
</script>
                                <div id="addUserError" class="text-red-600 text-xs hidden"></div>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="bg-gray-50 px-4 py-2 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700">
					<button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
						Add User
					</button>
					<button type="button" onclick="closeAddUserModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
						Cancel
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center pt-4 px-4 pb-4 text-center sm:block sm:p-0"><!-- removed min-h-screen to match Add Modal -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full h-[90vh] dark:bg-zinc-800">
			<form id="editUserForm" class="flex flex-col h-full">
				<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-zinc-800 flex-1 overflow-y-auto">
					<div class="sm:flex sm:items-start">
						<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
							<h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">Edit User</h3>
							<div class="mt-4 space-y-4">
								<div>
                                    <label for="edit_user_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input type="email" id="edit_user_email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                    <input type="hidden" id="current-user-email-edit" value="{{ auth()->user()->email ?? '' }}">
                                    <ul class="mt-1 text-xs" id="editUserEmailValidation">
                                        <li id="edit-email-format-status" class="text-red-600">• Must be a valid email address</li>
                                        <li id="edit-email-unique-status" class="text-red-600" style="display:none;">• Email already being used</li>
                                    </ul>
								</div>
								<div>
                                    <label for="edit_user_roles" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</label>
                                    <select id="edit_user_roles" name="roles" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                        <option value="admin" selected>Admin</option>
                                    </select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="bg-gray-50 px-4 py-2 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-zinc-700">
					<button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
						Update User
					</button>
					<button type="button" onclick="closeEditUserModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-zinc-600 dark:text-gray-200 dark:border-zinc-500 dark:hover:bg-zinc-500">
						Cancel
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Delete User Modal -->

<!-- No custom delete confirmation bar: using standard JS confirm() -->

@endsection

@section('scripts')
<script>
// Live email validation for Edit User modal
let editUserLastCheckedEmail = '';
let editUserEmailCheckTimeout = null;

function resetEditUserValidation() {
    document.getElementById('edit-email-format-status').className = 'text-red-600';
    const uniqueStatus = document.getElementById('edit-email-unique-status');
    uniqueStatus.style.display = 'none';
    uniqueStatus.className = 'text-red-600';
    
    editUserLastCheckedEmail = '';
    if (editUserEmailCheckTimeout) {
        clearTimeout(editUserEmailCheckTimeout);
    }
}

// (This script enables live format and uniqueness validation for the edit modal email field)
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('edit_user_email');
    const formatStatus = document.getElementById('edit-email-format-status');
    const uniqueStatus = document.getElementById('edit-email-unique-status');
    let originalEmail = '';
    const currentUserEmailEdit = document.getElementById('current-user-email-edit')?.value?.trim().toLowerCase() || '';
    // Set originalEmail when opening modal
    window.setEditUserOriginalEmail = function(email) { originalEmail = email; };
    if (emailInput && formatStatus && uniqueStatus) {
        // Prevent space input at the keydown level
        emailInput.addEventListener('keydown', function(e) {
            if (e.key === ' ' || e.code === 'Space') {
                e.preventDefault();
            }
        });
        emailInput.addEventListener('input', function() {
            // Convert any uppercase to lowercase and restrict to allowed characters
            let filtered = emailInput.value
                .replace(/[^a-zA-Z0-9@._-]/g, '') // allow a-z, A-Z, 0-9, @ . _ -
                .replace(/\s+/g, '') // remove spaces (redundant, but keep for paste)
                .toLowerCase();
            if (emailInput.value !== filtered) {
                emailInput.value = filtered;
            }
            const email = emailInput.value.trim();
            // Stricter format: only lowercase, no spaces, valid email structure
            const validEmailRegex = /^[a-z0-9._-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
            if (validEmailRegex.test(email)) {
                formatStatus.classList.remove('text-red-600');
                formatStatus.classList.add('text-green-600');
            } else {
                formatStatus.classList.remove('text-green-600');
                formatStatus.classList.add('text-red-600');
            }
            // Uniqueness validation (debounced)
            // If email matches original, always hide uniqueness error
            if (email === originalEmail) {
                uniqueStatus.style.display = 'none';
                uniqueStatus.classList.remove('text-red-600');
                uniqueStatus.classList.remove('text-green-600');
                return;
            }
            // Prevent using the current user's email
            if (email === currentUserEmailEdit) {
                uniqueStatus.style.display = '';
                uniqueStatus.classList.remove('text-green-600');
                uniqueStatus.classList.add('text-red-600');
                return;
            }
            uniqueStatus.style.display = 'none';
            uniqueStatus.classList.remove('text-green-600');
            uniqueStatus.classList.add('text-red-600');
            if (email && validEmailRegex.test(email)) {
                if (email === editUserLastCheckedEmail) return;
                editUserLastCheckedEmail = email;
                clearTimeout(editUserEmailCheckTimeout);
                editUserEmailCheckTimeout = setTimeout(async () => {
                    try {
                        const res = await fetch(`/users/search?query=${encodeURIComponent(email)}`);
                        const data = await res.json();
                        if (data.success && data.users && data.users.data && data.users.data.some(u => u.email === email)) {
                            uniqueStatus.style.display = '';
                            uniqueStatus.classList.remove('text-green-600');
                            uniqueStatus.classList.add('text-red-600');
                        } else {
                            uniqueStatus.style.display = 'none';
                        }
                    } catch (err) {
                        uniqueStatus.style.display = 'none';
                    }
                }, 400);
            }
        });
    }
});
</script>
<script>
// Global variables
// User Management JavaScript - Improved Version

// Global variables
let currentEditUserId = null;
let searchTimeout = null;

// CSRF token utility
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

// Utility functions
// ...existing code...

// Enhanced fetch wrapper with error handling
async function fetchWithErrorHandling(url, options = {}) {
    try {
        // Add CSRF token to headers if not present
        const headers = {
            'X-CSRF-TOKEN': getCsrfToken(),
            ...options.headers
        };
        
        const response = await fetch(url, { ...options, headers });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        return { data, error: null };
        
    } catch (error) {
        console.error('Fetch error:', error);
        return { data: null, error: error.message };
    }
}

// Modal Management
const ModalManager = {
    open(modalId, resetForm = true) {
        const modal = document.getElementById(modalId);
        const form = modal?.querySelector('form');
        
        if (!modal) {
            console.error(`Modal with ID '${modalId}' not found`);
            return;
        }
        
        modal.classList.remove('hidden');
        
        if (resetForm && form) {
            form.reset();
            this.clearFormErrors(form);
        }
        
        // Focus first input
        const firstInput = modal.querySelector('input, select, textarea');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    },
    
    close(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            const form = modal.querySelector('form');
            if (form) {
                this.clearFormErrors(form);
            }
        }
    },
    
    clearFormErrors(form) {
        const errorElements = form.querySelectorAll('.text-red-600:not([id*="pw-"]):not([id*="confirmPasswordStatus"])');
        errorElements.forEach(el => el.classList.add('hidden'));
    }
};

// Password validation utilities
const PasswordValidator = {
    validate(password, confirmPassword = null) {
        const rules = {
            length: password.length >= 8,
            upper: /[A-Z]/.test(password),
            lower: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password)
        };
        
        const isValid = Object.values(rules).every(rule => rule);
        const passwordsMatch = confirmPassword === null || password === confirmPassword;
        
        return {
            rules,
            isValid,
            passwordsMatch,
            overallValid: isValid && passwordsMatch
        };
    },
    
    updateUI(password, confirmPassword = '') {
        const validation = this.validate(password, confirmPassword);
        
        // Update requirement indicators
        Object.entries(validation.rules).forEach(([rule, isValid]) => {
            const element = document.getElementById(`pw-${rule}`);
            if (element) {
                element.className = isValid ? 'text-green-600' : 'text-red-600';
            }
        });
        
        // Update confirm password status
        const confirmStatus = document.getElementById('confirmPasswordStatus');
        if (confirmStatus && confirmPassword.length > 0) {
            if (validation.passwordsMatch) {
                confirmStatus.textContent = '• Passwords match';
                confirmStatus.className = 'mt-2 text-xs text-green-600';
            } else {
                confirmStatus.textContent = '• Passwords must match';
                confirmStatus.className = 'mt-2 text-xs text-red-600';
            }
        }
        
        return validation.overallValid;
    }
};

// User operations
const UserOperations = {
    async create(userData) {
        const { data, error } = await fetchWithErrorHandling('/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        });
        
        if (error) {
            throw new Error(error);
        }
        
        return data;
    },
    
    async update(id, userData) {
        const { data, error } = await fetchWithErrorHandling(`/users/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        });
        
        if (error) {
            throw new Error(error);
        }
        
        return data;
    },
    
    async delete(id) {
        const { data, error } = await fetchWithErrorHandling(`/users/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (error) {
            throw new Error(error);
        }
        
        return data;
    },
    
    async bulkDelete(ids) {
        const { data, error } = await fetchWithErrorHandling('/users/bulk-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ids })
        });
        
        if (error) {
            throw new Error(error);
        }
        
        return data;
    },
    
    async search(query) {
        const { data, error } = await fetchWithErrorHandling(`/users/search?query=${encodeURIComponent(query)}`);
        
        if (error) {
            throw new Error(error);
        }
        
        return data;
    },
    
    async getById(id) {
        const { data, error } = await fetchWithErrorHandling(`/users/${id}`);
        
        if (error) {
            throw new Error(error);
        }
        
        return data;
    }
};

// Checkbox management
const CheckboxManager = {
    init() {
        this.selectAllCheckbox = document.getElementById('checkbox-all');
        this.bulkDeleteBtn = document.getElementById('bulk-delete-btn');
        
        if (this.selectAllCheckbox) {
            this.selectAllCheckbox.addEventListener('change', () => this.handleSelectAll());
        }
        
        // Event delegation for row checkboxes
        const tbody = document.querySelector('tbody');
        if (tbody) {
            tbody.addEventListener('change', (e) => {
                if (e.target && e.target.classList.contains('row-checkbox')) {
                    this.handleRowCheckboxChange();
                }
            });
        }
        
        this.updateState();
    },
    
    getRowCheckboxes() {
        return Array.from(document.querySelectorAll('tbody .row-checkbox'));
    },
    
    getCheckedIds() {
        return this.getRowCheckboxes()
            .filter(cb => cb.checked)
            .map(cb => cb.closest('tr').getAttribute('data-user-id'))
            .filter(id => id); // Remove any null/undefined ids
    },
    
    handleSelectAll() {
        const checked = this.selectAllCheckbox.checked;
        this.getRowCheckboxes().forEach(cb => {
            cb.checked = checked;
        });
        this.updateState();
    },
    
    handleRowCheckboxChange() {
        const checkboxes = this.getRowCheckboxes();
        const checkedCount = checkboxes.filter(cb => cb.checked).length;
        
        if (checkboxes.length === 0) {
            this.selectAllCheckbox.checked = false;
            this.selectAllCheckbox.indeterminate = false;
        } else if (checkedCount === checkboxes.length) {
            this.selectAllCheckbox.checked = true;
            this.selectAllCheckbox.indeterminate = false;
        } else if (checkedCount === 0) {
            this.selectAllCheckbox.checked = false;
            this.selectAllCheckbox.indeterminate = false;
        } else {
            this.selectAllCheckbox.checked = false;
            this.selectAllCheckbox.indeterminate = true;
        }
        
        this.updateState();
    },
    
    updateState() {
        const checkedCount = this.getRowCheckboxes().filter(cb => cb.checked).length;
        
        if (this.bulkDeleteBtn) {
            if (checkedCount > 0) {
                this.bulkDeleteBtn.style.display = 'inline-flex';
                this.bulkDeleteBtn.textContent = `Delete (${checkedCount})`;
            } else {
                this.bulkDeleteBtn.style.display = 'none';
            }
        }
    },
    
    reset() {
        this.getRowCheckboxes().forEach(cb => cb.checked = false);
        if (this.selectAllCheckbox) {
            this.selectAllCheckbox.checked = false;
            this.selectAllCheckbox.indeterminate = false;
        }
        this.updateState();
    }
};


// Table management
const TableManager = {
    update(users) {
        const tbody = document.querySelector('tbody');
        if (!tbody) return;
        
        if (!users.data || users.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-2 py-1.5 text-center">No users found</td></tr>';
            CheckboxManager.reset();
            return;
        }
        
        tbody.innerHTML = users.data.map(user => this.createUserRow(user)).join('');
        CheckboxManager.updateState();
    },
    
    createUserRow(user) {
        const formattedDate = user.created_at 
            ? new Date(user.created_at).toLocaleDateString('en-US', { 
                day: '2-digit', 
                month: 'short', 
                year: 'numeric' 
              })
            : '-';
            
        return `
            <tr data-user-id="${user.id}" class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:hover:bg-zinc-700/50 dark:border-zinc-600">
                <td class="w-4 p-3">
                    <div class="flex items-center">
                        <input type="checkbox" class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                    </div>
                </td>
                <td class="px-2 py-1.5">${this.escapeHtml(user.email)}</td>
                <td class="px-2 py-1.5">${this.escapeHtml(user.roles || '-')}</td>
                <td class="px-2 py-1.5">${formattedDate}</td>
                <td class="px-2 py-1.5 text-center">
                    ${this.createActionDropdown(user.id, user.email)}
                </td>
            </tr>
        `;
    },
    
    createActionDropdown(userId, userEmail) {
        return `
            <div class="relative inline-block dropdown">
                <button type="button" class="dropdown-toggle flex items-center justify-center w-7 h-7 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 focus:ring focus:ring-gray-200 dark:bg-zinc-600 dark:text-gray-100 dark:hover:bg-zinc-500">
                    <i class="bx bx-dots-vertical text-base"></i>
                </button>
                <div class="dropdown-menu hidden absolute right-0 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 dark:bg-zinc-700 z-20">
                    <div class="p-1 flex flex-col gap-1">
                        <button type="button" onclick="editUser(${userId})" class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                            <i class="mdi mdi-pencil text-base"></i>
                            <span>Edit</span>
                        </button>
                        <button type="button" onclick="deleteUser(${userId}, '${this.escapeHtml(userEmail)}')" class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                            <i class="mdi mdi-trash-can text-base"></i>
                            <span>Delete</span>
                        </button>
                    </div>
                </div>
            </div>
        `;
    },
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Form handlers
function setupFormHandlers() {
    // Add User Form
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const errorDiv = document.getElementById('addUserError');
            const emailErrorDiv = document.getElementById('addUserEmailError');
            const formatStatus = document.getElementById('email-format-status');
            const uniqueStatus = document.getElementById('email-unique-status');
            const originalText = submitBtn.textContent;
            // Prepare data
            let userData = {
                email: formData.get('email')?.trim(),
                roles: formData.get('roles')?.trim(),
                password: formData.get('password') || '',
                password_confirmation: formData.get('password_confirmation') || ''
            };
            // Client-side validation
            errorDiv.classList.add('hidden');
            errorDiv.textContent = '';
            emailErrorDiv.classList.add('hidden');
            emailErrorDiv.textContent = '';
            let hasError = false;
            // Email format validation
            if (!userData.email || !/^\S+@\S+\.\S+$/.test(userData.email)) {
                formatStatus.classList.remove('text-green-600');
                formatStatus.classList.add('text-red-600');
                hasError = true;
            } else {
                formatStatus.classList.remove('text-red-600');
                formatStatus.classList.add('text-green-600');
            }
            // Required fields
            if (!userData.roles || !userData.password || !userData.password_confirmation) {
                errorDiv.textContent = 'All fields are required.';
                errorDiv.classList.remove('hidden');
                hasError = true;
            }
            // Password match
            if (userData.password !== userData.password_confirmation) {
                errorDiv.textContent = 'Passwords do not match.';
                errorDiv.classList.remove('hidden');
                hasError = true;
            }
            // Validate password strength
            const passwordValid = PasswordValidator.validate(userData.password, userData.password_confirmation);
            if (!passwordValid.overallValid) {
                errorDiv.textContent = 'Password does not meet requirements.';
                errorDiv.classList.remove('hidden');
                hasError = true;
            }
            if (hasError) {
                return;
            }
            submitBtn.disabled = true;
            submitBtn.textContent = 'Adding...';
            // Check if email is already used (AJAX call)
            try {
                const checkResponse = await fetch(`/users/search?query=${encodeURIComponent(userData.email)}`);
                const checkData = await checkResponse.json();
                if (checkData.success && checkData.users && checkData.users.data && checkData.users.data.some(u => u.email === userData.email)) {
                    uniqueStatus.classList.remove('text-green-600');
                    uniqueStatus.classList.add('text-red-600');
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    return;
                } else {
                    uniqueStatus.classList.remove('text-red-600');
                    uniqueStatus.classList.add('text-green-600');
                }
            } catch (err) {
                uniqueStatus.classList.remove('text-green-600');
                uniqueStatus.classList.add('text-red-600');
                // If search fails, allow backend to handle duplicate error
            }
            try {
                const response = await UserOperations.create(userData);
                if (response.success) {
                    ModalManager.close('addUserModal');
                    refreshUserTable();
                } else {
                    // If backend says email is already used, show that message
                    if (response.errors && response.errors.email && response.errors.email[0].includes('already registered')) {
                        emailErrorDiv.textContent = 'This email address is already registered.';
                        emailErrorDiv.classList.remove('hidden');
                    } else {
                        errorDiv.textContent = response.message || 'Failed to add user';
                        errorDiv.classList.remove('hidden');
                    }
                }
            } catch (error) {
                // If backend says email is already used, show that message
                if (error && error.message && error.message.includes('already registered')) {
                    emailErrorDiv.textContent = 'This email address is already registered.';
                    emailErrorDiv.classList.remove('hidden');
                } else {
                    errorDiv.textContent = error.message || 'An error occurred while adding user';
                    errorDiv.classList.remove('hidden');
                }
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }
    
    // Edit User Form
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!currentEditUserId) {
                return;
            }
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            const emailInput = document.getElementById('edit_user_email');
            const rolesInput = document.getElementById('edit_user_roles');
            const email = emailInput.value.trim();
            const roles = rolesInput.value.trim();
            // Validation UI elements
            const formatStatus = document.getElementById('edit-email-format-status');
            const uniqueStatus = document.getElementById('edit-email-unique-status');
            // Get the original email from the live validation script
            let originalEmail = '';
            if (window.setEditUserOriginalEmail) {
                originalEmail = window.originalEmailForEditUser || '';
                if (!originalEmail && emailInput.dataset.originalEmail) {
                    originalEmail = emailInput.dataset.originalEmail;
                }
            }
            // Remove previous error styles
            emailInput.classList.remove('border-red-500');
            rolesInput.classList.remove('border-red-500');
            let hasError = false;
            // Email format validation
            if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
                if (formatStatus) {
                    formatStatus.classList.remove('text-green-600');
                    formatStatus.classList.add('text-red-600');
                }
                emailInput.classList.add('border-red-500');
                hasError = true;
            } else {
                if (formatStatus) {
                    formatStatus.classList.remove('text-red-600');
                    formatStatus.classList.add('text-green-600');
                }
            }
            // Roles validation
            if (!roles || (roles !== 'admin' && roles !== 'user')) {
                rolesInput.classList.add('border-red-500');
                hasError = true;
            }
            if (hasError) {
                return;
            }
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
            // Check if email is already used (AJAX call, only if changed)
            let emailIsUnique = true;
            if (email !== originalEmail) {
                try {
                    const checkResponse = await fetch(`/users/search?query=${encodeURIComponent(email)}`);
                    const checkData = await checkResponse.json();
                    if (checkData.success && checkData.users && checkData.users.data && checkData.users.data.some(u => u.email === email)) {
                        if (uniqueStatus) {
                            uniqueStatus.style.display = '';
                            uniqueStatus.classList.remove('text-green-600');
                            uniqueStatus.classList.add('text-red-600');
                        }
                        emailInput.classList.add('border-red-500');
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                        return;
                    } else {
                        if (uniqueStatus) {
                            uniqueStatus.style.display = 'none';
                        }
                    }
                } catch (err) {
                    if (uniqueStatus) {
                        uniqueStatus.style.display = 'none';
                    }
                }
            }
            // If all validation passes, submit
            const userData = { email, roles };
            try {
                const response = await UserOperations.update(currentEditUserId, userData);
                if (response.success) {
                    ModalManager.close('editUserModal');
                    currentEditUserId = null;
                    refreshUserTable();
                } else {
                    // ...error notification removed...
                }
            } catch (error) {
                // ...error notification removed...
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }
}

// Main functions (called from HTML)
async function editUser(id) {
	resetEditUserValidation();
    try {
        currentEditUserId = id;
        const response = await UserOperations.getById(id);
        
        if (response.success) {
            const user = response.user;
            
            // Set the email value
            const emailInput = document.getElementById('edit_user_email');
            if (emailInput) {
                emailInput.value = user.email || '';
                // Set the original email for validation
                if (window.setEditUserOriginalEmail) {
                    window.setEditUserOriginalEmail(user.email || '');
                }
            }
            
            // Set roles dropdown
            const rolesSelect = document.getElementById('edit_user_roles');
            if (rolesSelect && user.roles) {
                const roleValue = user.roles.toLowerCase();
                for (let option of rolesSelect.options) {
                    if (option.value.toLowerCase() === roleValue) {
                        option.selected = true;
                        break;
                    }
                }
            }
            
            ModalManager.open('editUserModal', false);
        } else {
            // Handle error
            console.error('Failed to load user data');
        }
    } catch (error) {
        console.error('Error loading user:', error);
    }
}

async function deleteUser(id, email) {
    if (!window.confirm(`Are you sure you want to delete user ${email}?`)) {
        return;
    }
    
    try {
        const response = await UserOperations.delete(id);
        
        if (response.success) {
            // ...success notification removed...
            refreshUserTable();
        } else {
            // ...error notification removed...
        }
    } catch (error) {
    // ...error notification removed...
    }
}

async function performBulkDelete() {
    const ids = CheckboxManager.getCheckedIds();
    
    if (ids.length === 0) {
    // ...error notification removed...
        return;
    }
    
    if (!window.confirm(`Are you sure you want to delete ${ids.length} user(s)?`)) {
        return;
    }
    
    try {
        const response = await UserOperations.bulkDelete(ids);
        
        if (response.success) {
            // ...success notification removed...
            CheckboxManager.reset();
            refreshUserTable();
        } else {
            // ...error notification removed...
        }
    } catch (error) {
    // ...error notification removed...
    }
}

// Search functionality
function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('performSearchBtn');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            searchTimeout = setTimeout(async () => {
                if (query.length > 0) {
                    await searchUsers(query);
                } else {
                    refreshUserTable();
                }
            }, 300);
        });
    }
    
    if (searchBtn) {
        searchBtn.addEventListener('click', async function() {
            const query = searchInput?.value?.trim() || '';
            if (query.length > 0) {
                await searchUsers(query);
            } else {
                refreshUserTable();
            }
        });
    }
}

async function searchUsers(query) {
    try {
        const response = await UserOperations.search(query);
        
        if (response.success) {
            TableManager.update(response.users);
            // Re-initialize dropdowns after updating the table
            setupDropdowns();
        } else {
            // ...error notification removed...
        }
    } catch (error) {
    // ...error notification removed...
    }
}

// Utility functions
function refreshUserTable() {
    window.location.reload();
}

function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input || !btn) return;
    
    const icon = btn.querySelector('i');
    if (!icon) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

function updatePasswordValidation() {
    const password = document.getElementById('add_user_password')?.value || '';
    const confirmPassword = document.getElementById('add_user_password_confirmation')?.value || '';
    return PasswordValidator.updateUI(password, confirmPassword);
}

// Dropdown functionality
function setupDropdowns() {
    // Remove previous listeners by cloning toggles (prevents stacking)
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        const newToggle = toggle.cloneNode(true);
        toggle.parentNode.replaceChild(newToggle, toggle);
    });

    // Attach click to each dropdown-toggle
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        if (toggle && menu) {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                // Close all other dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(m => {
                    if (m !== menu) m.classList.add('hidden');
                });
                menu.classList.toggle('hidden');
            });
        }
    });
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
    });
}

// Modal close handlers
function setupModalHandlers() {
    // Close modals when clicking backdrop
    document.addEventListener('click', function(e) {
        if (e.target.matches('.fixed.inset-0')) {
            const modalId = e.target.closest('[id$="Modal"]')?.id;
            if (modalId) {
                ModalManager.close(modalId);
                if (modalId === 'editUserModal') {
                    currentEditUserId = null;
                }
            }
        }
    });
    
    // ESC key to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const visibleModal = document.querySelector('[id$="Modal"]:not(.hidden)');
            if (visibleModal) {
                ModalManager.close(visibleModal.id);
                if (visibleModal.id === 'editUserModal') {
                    currentEditUserId = null;
                }
            }
        }
    });
}

// Global modal functions (called from HTML)
function openAddUserModal() {
    ModalManager.open('addUserModal');
}

function closeAddUserModal() {
    ModalManager.close('addUserModal');
}

function closeEditUserModal() {
    ModalManager.close('editUserModal');
    currentEditUserId = null;
}

// Initialize everything
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    CheckboxManager.init();
    setupFormHandlers();
    setupSearch();
    setupDropdowns();
    setupModalHandlers();
    
    console.log('User Management System initialized successfully');
});
</script>
