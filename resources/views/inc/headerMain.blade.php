<!-- ========== Header Main Start ========== -->
<nav class="fixed top-0 left-0 right-0 z-10 flex items-center bg-white dark:bg-zinc-800 print:hidden border-b border-gray-100 dark:border-zinc-600 ltr:pr-6 rtl:pl-6">
    <div class="flex justify-between w-full">

        <!-- Left: Logo & Menu Toggle -->
        <div class="flex items-center topbar-brand">
            <div class="hidden lg:flex navbar-brand items-center justify-between shrink px-6 h-[70px] ltr:border-r rtl:border-l bg-[#fbfaff] border-gray-50 dark:border-zinc-700 dark:bg-zinc-800 shadow-none">
                <a href="#" class="flex items-center text-lg flex-shrink-0 font-bold dark:text-white leading-[69px]">
                    <img src="images/logo-sm.svg" alt="Logo" class="inline-block w-6 h-6 align-middle ltr:xl:mr-2 rtl:xl:ml-2">
                    <span class="hidden font-bold text-gray-700 align-middle xl:block dark:text-gray-100 leading-[69px]">Minia</span>
                </a>
            </div>
        </div>

        <!-- Right: Profile Dropdown -->
        <div class="flex items-center ltr:ml-auto rtl:mr-auto">
            <div class="relative dropdown">
                <button type="button"
                    class="flex items-center px-3 py-2 h-[70px] border-x border-gray-50 bg-gray-50/30 dropdown-toggle dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100"
                    id="page-header-user-dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    onclick="document.getElementById('profile/log').classList.toggle('hidden')">
                    <img class="border-[3px] border-gray-700 dark:border-zinc-400 rounded-full w-9 h-9 ltr:xl:mr-2 rtl:xl:ml-2"
                        src="images/avatar-1.jpg" alt="Header Avatar">
                    <span class="hidden font-medium xl:block">Shawn L.</span>
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
