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
                    onclick="document.getElementById('addUserModal').classList.remove('hidden'); document.getElementById('addUserForm').reset();"
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
							@forelse($records as $user)
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
                                </div>
                                <div>
                                    <label for="add_user_roles" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</label>
                                    <select id="add_user_roles" name="roles" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                        <option value="" disabled selected>Select a role</option>
                                        <option value="admin">Admin</option>
                                        <option value="user">User</option>
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
								</div>
								<div>
                                    <label for="edit_user_roles" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</label>
                                    <select id="edit_user_roles" name="roles" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                        <option value="" disabled selected>Select a role</option>
                                        <option value="admin">Admin</option>
                                        <option value="user">User</option>
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
// Global variables
let currentEditUserId = null;

// Utility functions
function showMessageBar(message, type = 'success') {
    alert(message);
}

// Modal functions
function openAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
    document.getElementById('addUserForm').reset();
}

function closeAddUserModal() {
    document.getElementById('addUserModal').classList.add('hidden');
    document.getElementById('addUserForm').reset();
}

function editUser(id) {
    currentEditUserId = id;
    
    // Fetch user details
    fetch(`/users/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('edit_user_email').value = data.user.email;
                // Set the dropdown value for roles
                const rolesSelect = document.getElementById('edit_user_roles');
                if (rolesSelect) {
                    // Try to match value case-insensitively
                    const roleValue = (data.user.roles || '').toLowerCase();
                    for (let i = 0; i < rolesSelect.options.length; i++) {
                        if (rolesSelect.options[i].value.toLowerCase() === roleValue) {
                            rolesSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
                document.getElementById('editUserModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessageBar('Failed to load user details', 'error');
        });
}

function closeEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
    currentEditUserId = null;
}

function deleteUser(id, email) {
    if (!window.confirm(`Are you sure you want to delete user ${email}?`)) {
        return;
    }
    fetch(`/users/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessageBar(`User ${email} deleted successfully`, 'success');
            refreshUserTable();
        } else {
            showMessageBar(data.message || 'Failed to delete user', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessageBar('An error occurred while deleting user', 'error');
    });
}

// Form submission handlers
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    console.log('Form submitted!');
    
    const email = document.getElementById('add_user_email').value.trim();
    const roles = document.getElementById('add_user_roles').value.trim();
    const password = document.getElementById('add_user_password').value;
    const passwordConfirm = document.getElementById('add_user_password_confirmation').value;
    const errorDiv = document.getElementById('addUserError');
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;

    console.log('Form data:', { email, roles, password: password ? 'HAS_PASSWORD' : 'NO_PASSWORD' });

    // Basic validation
    if (!email || !roles || !password || !passwordConfirm) {
        errorDiv.textContent = 'All fields are required.';
        errorDiv.classList.remove('hidden');
        return;
    }

    if (password !== passwordConfirm) {
        errorDiv.textContent = 'Passwords do not match.';
        errorDiv.classList.remove('hidden');
        return;
    }

    errorDiv.classList.add('hidden');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding...';

    // Use the EXACT same format that worked in our test
    fetch('/users', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            email: email,
            roles: roles,
            password: password,
            password_confirmation: passwordConfirm
        })
    })
    .then(response => {
        console.log('Form response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Form response data:', data);
        if (data.success) {
            showMessageBar(data.message || 'User added', 'success');
            closeAddUserModal();
            refreshUserTable();
        } else {
            errorDiv.textContent = data.message || 'Failed to add user';
            errorDiv.classList.remove('hidden');
            showMessageBar(data.message || 'Failed to add user', 'error');
        }
    })
    .catch(error => {
        console.error('Form error:', error);
        errorDiv.textContent = 'An error occurred while adding user';
        errorDiv.classList.remove('hidden');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    fetch(`/users/${currentEditUserId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            email: document.getElementById('edit_user_email').value,
            roles: document.getElementById('edit_user_roles').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessageBar(data.message || 'User updated', 'success');
            closeEditUserModal();
            refreshUserTable();
        } else {
            showMessageBar(data.message || 'Failed to update user', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    showMessageBar('An error occurred while updating user', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});


// No custom delete confirmation bar handlers needed

// Search functionality
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value;
    
    searchTimeout = setTimeout(() => {
        if (query.length > 0) {
            searchUsers(query);
        } else {
            refreshUserTable();
        }
    }, 300);
});

document.getElementById('performSearchBtn').addEventListener('click', function() {
    const query = document.getElementById('searchInput').value;
    if (query.length > 0) {
        searchUsers(query);
    } else {
        refreshUserTable();
    }
});

function searchUsers(query) {
    fetch(`/users/search?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateUserTable(data.users);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Search failed', 'error');
        });
}

// Bulk delete functionality
function performBulkDelete() {
    const checkedBoxes = document.querySelectorAll('tbody .row-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.closest('tr').getAttribute('data-user-id'));
    if (ids.length === 0) {
        showToast('Please select at least one user', 'error');
        return;
    }
    if (!window.confirm(`Are you sure you want to delete ${ids.length} user(s)?`)) {
        return;
    }
    fetch('/users/bulk-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessageBar(data.message || 'Users deleted', 'success');
            refreshUserTable();
            document.getElementById('checkbox-all').checked = false;
        } else {
            showMessageBar(data.message || 'Failed to delete users', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    showMessageBar('An error occurred while deleting users', 'error');
    });
}

// Refresh user table
function refreshUserTable() {
    window.location.reload();
}

function updateUserTable(users) {
    const tbody = document.querySelector('tbody');
    if (!users.data || users.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-2 py-1.5 text-center">No users found</td></tr>';
        return;
    }
    
    tbody.innerHTML = users.data.map(user => `
        <tr data-user-id="${user.id}" class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:hover:bg-zinc-700/50 dark:border-zinc-600">
            <td class="w-4 p-3">
                <div class="flex items-center">
                    <input type="checkbox" class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                </div>
            </td>
            <td class="px-2 py-1.5">${user.created_at ? new Date(user.created_at).toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'}</td>
            <td class="px-2 py-1.5 text-center">
                <div class="relative inline-block dropdown">
                   <button type="button" class="dropdown-toggle flex items-center justify-center w-7 h-7 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 focus:ring focus:ring-gray-200 dark:bg-zinc-600 dark:text-gray-100 dark:hover:bg-zinc-500">
                       <i class="bx bx-dots-vertical text-base"></i>
                   </button>
                   <div class="dropdown-menu hidden absolute right-0 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 dark:bg-zinc-700 z-20">
                       <div class="p-1 flex flex-col gap-1">
                           <button type="button" onclick="editUser(${user.id})" class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                               <i class="mdi mdi-pencil text-base"></i>
                               <span>Edit</span>
                           </button>
                           <button type="button" onclick="deleteUser(${user.id}, '${user.email}')" class="w-full flex items-center justify-center gap-1 px-2 py-1 text-xs text-white bg-gray-300 rounded hover:bg-gray-700">
                               <i class="mdi mdi-trash-can text-base"></i>
                               <span>Delete</span>
                           </button>
                       </div>
                   </div>
                </div>
            </td>
        </tr>
    `).join('');
    
    // Re-initialize checkbox logic
    initUserCheckboxLogic();
    // Ensure bulk delete button visibility is updated after table update
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    if (bulkDeleteBtn) {
        bulkDeleteBtn.style.display = 'none';
    }
}

// Checkbox and bulk delete logic
function initUserCheckboxLogic() {
    const selectAll = document.getElementById('checkbox-all');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    
    function getRowCheckboxes() {
        return Array.from(document.querySelectorAll('tbody .row-checkbox'));
    }
    
    function updateBulkDeleteVisibility() {
        const checkedCount = getRowCheckboxes().filter(cb => cb.checked).length;
        if (bulkDeleteBtn) {
            if (checkedCount > 0) {
                bulkDeleteBtn.style.display = 'inline-flex';
            } else {
                bulkDeleteBtn.style.display = 'none';
            }
            bulkDeleteBtn.innerHTML = 'Delete';
        }
    }
    
    if (selectAll) {
        // Remove previous event listener if any
        selectAll.onchange = null;
        selectAll.addEventListener('change', function() {
            getRowCheckboxes().forEach(cb => {
                cb.checked = selectAll.checked;
            });
            handleRowCheckboxChange();
        });
    }
    
    function handleRowCheckboxChange() {
        const rowCheckboxes = getRowCheckboxes();
        if (rowCheckboxes.length === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
            return;
        }
        const checkedCount = rowCheckboxes.filter(cb => cb.checked).length;
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
    
    // Remove previous listeners to avoid duplicates
    getRowCheckboxes().forEach(cb => {
        cb.onchange = null;
        cb.addEventListener('change', handleRowCheckboxChange);
    });

    // Event delegation for dynamic content (if table is updated via AJAX)
    const tbody = document.querySelector('tbody');
    if (tbody) {
        tbody.removeEventListener('change', tbody._checkboxDelegate);
        tbody._checkboxDelegate = function(e) {
            if (e.target && e.target.classList.contains('row-checkbox')) {
                handleRowCheckboxChange();
            }
        };
        tbody.addEventListener('change', tbody._checkboxDelegate);
    }
    
    // Initial state
    handleRowCheckboxChange();
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    initUserCheckboxLogic();
    
    // Event listeners
    const addBtn = document.getElementById('add-user-btn');
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            console.log('Add button clicked');
            openAddUserModal();
        });
    } else {
        console.log('Add button NOT FOUND in DOM');
    }
    
    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
            if (!e.target.closest('#addUserModal')) {
                closeAddUserModal();
            }
            if (!e.target.closest('#editUserModal')) {
                closeEditUserModal();
            }
            if (!e.target.closest('#deleteUserModal')) {
                closeDeleteUserModal();
            }
        }
    });
    
    // Dropdown functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.dropdown-toggle')) {
            e.preventDefault();
            const dropdown = e.target.closest('.dropdown');
            const menu = dropdown.querySelector('.dropdown-menu');
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                if (otherMenu !== menu) {
                    otherMenu.classList.add('hidden');
                }
            });
            
            // Toggle current dropdown
            menu.classList.toggle('hidden');
        } else {
            // Close all dropdowns when clicking outside
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
});
</script>
