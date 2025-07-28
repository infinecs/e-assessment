@extends('layout.appMain')

@section('content')
<div class="card dark:bg-zinc-800 dark:border-zinc-600">
    <div class="border-b card-body border-gray-50 dark:border-zinc-600">
        <h5 class="text-gray-700 text-15 dark:text-gray-100">Settings</h5>
    </div>

    <div class="card-body">

        <!-- Tabs -->
        <div class="nav-tabs border-b-tabs">
            <ul class="flex flex-wrap w-full text-sm font-medium text-center text-violet-500 nav border-t border-gray-50 pt-5 mt-2 dark:border-zinc-600" id="settings-tab" role="tablist">
                <li>
                    <a href="javascript:void(0);" data-tw-toggle="tab" data-tw-target="tab-account"
                       class="px-3 pt-5 pb-[1.4rem] font-medium active">
                        Account
                    </a>
                </li>
            </ul>
        </div>

     <!-- Tab Contents -->
        <div class="tab-content mt-6">

            <!-- Account Tab -->
<div class="tab-pane" id="tab-account" >
    <form action="{{ route('settings.updateAccount') }}" method="POST" class="space-y-6">
        @csrf
        <!-- Email -->
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-2">
                <label class="font-medium text-gray-700 text-15 dark:text-gray-100">Email :</label>
            </div>
            <div class="col-span-12 md:col-span-10">
                <input type="email" name="email"
       value="{{ auth()->user()->email }}"
        class="w-full p-2 border rounded 
              bg-gray-50 dark:bg-zinc-600 
              text-gray-600 dark:text-gray-300 
              cursor-not-allowed"
       readonly>

            </div>
        </div>

        <!-- New Password -->
<div class="grid grid-cols-12 gap-4">
    <div class="col-span-12 md:col-span-2">
        <label class="font-medium text-gray-700 text-15 dark:text-gray-100">New Password :</label>
    </div>
    <div class="col-span-12 md:col-span-10">
        <div class="flex rounded-md shadow-sm">
            <input type="password" id="password" name="password"
                   placeholder="Enter password"
                   class="w-full p-2 border rounded dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100 pr-10">
            <button type="button" id="password-addon"
                    class="inline-flex items-center px-3 rounded border border-l-0 border-gray-300 bg-gray-50 text-gray-500 dark:bg-zinc-600 dark:text-gray-200">
                <i class="mdi mdi-eye-off-outline"></i>
            </button>
        </div>
         <!-- Checklist -->
        <ul id="passwordChecklist" class="mt-2 text-sm space-y-1">
            <li id="length" class="text-red-500">• At least 8 characters</li>
            <li id="upperlower" class="text-red-500">• Uppercase and lowercase letters</li>
            <li id="number" class="text-red-500">• At least one number</li>
            <li id="special" class="text-red-500">• At least one special character</li>
        </ul>
    </div>
</div>

       <!-- Confirm Password -->
<div class="grid grid-cols-12 gap-4">
    <div class="col-span-12 md:col-span-2">
        <label class="font-medium text-gray-700 text-15 dark:text-gray-100">Confirm Password :</label>
    </div>
    <div class="col-span-12 md:col-span-10">
        <div class="flex rounded-md shadow-sm">
            <input type="password" name="password_confirmation" id="password_confirmation"
                   placeholder="Enter password"
                   class="w-full p-2 border rounded dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100 pr-10">
            <button type="button" id="confirm-password-addon"
                    class="inline-flex items-center px-3 rounded border border-l-0 border-gray-300 bg-gray-50 text-gray-500 dark:bg-zinc-600 dark:text-gray-200">
                <i class="mdi mdi-eye-off-outline"></i>
            </button>
        </div>
        <p id="confirmError" class="mt-1 text-sm text-red-500 hidden">Passwords do not match.</p>
    </div>
</div>
        <div>
            <button type="submit" id="accountSubmit"
                    class="px-4 py-2 bg-violet-500 text-white rounded opacity-50 cursor-not-allowed"
                    disabled>
                Update Account
            </button>
        </div>
    </form>
</div>

        </div>

    </div>
</div>

<script>
    // Simple tab switcher (if no framework JS)
    document.querySelectorAll('[data-tw-toggle="tab"]').forEach(tab => {
        tab.addEventListener('click', () => {
            // remove active from all tabs
            document.querySelectorAll('[data-tw-toggle="tab"]').forEach(el => el.classList.remove('active'));
            // hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.add('hidden'));
            // add active to clicked tab
            tab.classList.add('active');
            // show target
            document.getElementById(tab.getAttribute('data-tw-target')).classList.remove('hidden');
            document.getElementById(tab.getAttribute('data-tw-target')).classList.add('block');
        });
    });
</script>
<script>
function toggleRequirement(el, passed) {
    el.classList.toggle('text-green-500', passed);
    el.classList.toggle('text-red-500', !passed);
}

function validateAccountForm() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    const submitBtn = document.getElementById('accountSubmit');
    const confirmError = document.getElementById('confirmError');

    const lengthEl = document.getElementById('length');
    const upperlowerEl = document.getElementById('upperlower');
    const numberEl = document.getElementById('number');
    const specialEl = document.getElementById('special');

    const hasLength = password.length >= 8;
    const hasUpperLower = /[A-Z]/.test(password) && /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSpecial = /[@$!%*?&]/.test(password);

    toggleRequirement(lengthEl, hasLength);
    toggleRequirement(upperlowerEl, hasUpperLower);
    toggleRequirement(numberEl, hasNumber);
    toggleRequirement(specialEl, hasSpecial);

    let valid = hasLength && hasUpperLower && hasNumber && hasSpecial;

    if (confirmPassword.length === 0) {
        confirmError.textContent = "Confirm password cannot be empty.";
        confirmError.classList.remove('hidden');
        valid = false;
    } else if (password !== confirmPassword) {
        confirmError.textContent = "Passwords do not match.";
        confirmError.classList.remove('hidden');
        valid = false;
    } else {
        confirmError.classList.add('hidden');
    }

    submitBtn.disabled = !valid;
    submitBtn.classList.toggle('opacity-50', !valid);
    submitBtn.classList.toggle('cursor-not-allowed', !valid);
}

['input','keyup'].forEach(evt => {
    document.getElementById('password').addEventListener(evt, validateAccountForm);
    document.getElementById('password_confirmation').addEventListener(evt, validateAccountForm);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function setupToggle(buttonId, inputId) {
        var input = document.getElementById(inputId);
        var button = document.getElementById(buttonId);
        if (input && button) {
            var icon = button.querySelector('i');
            button.addEventListener('click', function () {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('mdi-eye-off-outline');
                    icon.classList.add('mdi-eye-outline');
                } else {
                    input.type = 'password';
                    icon.classList.remove('mdi-eye-outline');
                    icon.classList.add('mdi-eye-off-outline');
                }
            });
        }
    }

    // Setup for both fields
    setupToggle('password-addon', 'password');
    setupToggle('confirm-password-addon', 'password_confirmation');
});
</script>

@endsection
