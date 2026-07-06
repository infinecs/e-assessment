<!-- ========== Header Main Start ========== -->
<nav class="fixed top-0 left-0 right-0 z-10 flex items-center bg-white dark:bg-zinc-800 print:hidden border-b border-gray-100 dark:border-zinc-600 ltr:pr-6 rtl:pl-6">
    <div class="flex justify-between w-full">

        <!-- Left: Logo & Menu Toggle -->
        <div class="flex items-center topbar-brand">
            <!-- Responsive Logo -->
            <div class="hidden lg:flex navbar-brand items-center justify-center shrink px-6 h-[70px] ltr:border-r rtl:border-l bg-[#fbfaff] border-gray-50 dark:border-zinc-700 dark:bg-zinc-800 shadow-none transition-all duration-300 ease-in-out w-64 group-data-[sidebar-size=sm]:w-[70px] group-data-[sidebar-size=sm]:px-0">
                <a href="{{ route('assessment.index') }}">
                    <img src="{{ asset('images/Infinecs-with-slogan.png') }}" alt="Full logo"
                         class="h-9 w-auto group-data-[sidebar-size=sm]:hidden">
                    <img src="{{ asset('images/Infinecs-logo icon.png') }}" alt="Icon"
                         class="hidden group-data-[sidebar-size=sm]:inline h-10 w-10 object-contain">
</a>
            </div>
            <!-- Sidebar Toggle Button -->
            <button id="sidebar-toggle" 
                class="flex items-center px-3 py-2 h-[70px] text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors"
                aria-label="Toggle sidebar"
                title="Toggle Sidebar">
                <i data-feather="menu" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Right: Dark Mode Toggle + Profile Dropdown -->
        <div class="flex items-center ltr:ml-auto rtl:mr-auto">
<!-- Dark Mode Toggle -->
            <button id="dark-mode-toggle"
                class="flex items-center justify-center w-9 h-9 mx-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                aria-label="Toggle dark mode"
                title="Toggle Dark Mode">
                <i id="icon-moon" data-feather="moon" class="w-4 h-4"></i>
                <i id="icon-sun" data-feather="sun" class="w-4 h-4 hidden"></i>
            </button>

<div class="relative dropdown">
                <button type="button"
                    class="flex items-center px-3 py-2 h-[70px] border-x border-gray-50 bg-gray-50/30 dropdown-toggle dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100"
                    id="page-header-user-dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    onclick="document.getElementById('profile/log').classList.toggle('hidden')">
                    <span class="hidden font-medium xl:block">{{ Auth::user()->email ?? '' }}</span>
                    <i class="hidden align-bottom mdi mdi-chevron-down xl:block"></i>
                </button>

                <!-- Dropdown Menu -->
                <div class="absolute right-0 top-full mt-2 hidden w-40 list-none bg-white dropdown-menu dropdown-animation rounded shadow dark:bg-zinc-800 z-50"
                    id="profile/log">
                    <div class="border border-gray-50 dark:border-zinc-600" aria-labelledby="page-header-user-dropdown">

                        <div class="dropdown-item dark:text-gray-100">
                            <a class="block px-3 py-2 hover:bg-gray-50/50 dark:hover:bg-zinc-700/50" href="{{ route('settings') }}">
                                <i class="mr-1 align-middle  mdi mdi-cog text-16"></i> Settings
                            </a>
                        </div>
                    
                        <hr class="border-gray-50 dark:border-gray-700">
                        <div class="dropdown-item dark:text-gray-100">
                            <a class="block p-3 hover:bg-gray-50/50 dark:hover:bg-zinc-700/50" href="{{ route('logout') }}">
                                <i class="mr-1 align-middle mdi mdi-logout text-16"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Dropdown Menu -->
            </div>
        </div>
    </div>
</nav>


<!-- ========== Header Main End ========== -->
