<!-- ========== Left Sidebar Start ========== -->
<aside
    class="fixed bottom-0 z-10 h-screen ltr:border-r rtl:border-l vertical-menu rtl:right-0 ltr:left-0 top-[70px] bg-slate-50 border-gray-50 print:hidden dark:bg-zinc-800 dark:border-neutral-700"
    role="navigation" aria-label="Sidebar Navigation">
    <div data-simplebar class="h-full">
        <div class="metismenu pb-10 pt-2.5" id="sidebar-menu">
            <ul id="side-menu">
                <li class="px-5 py-3 text-xs font-medium text-gray-500 cursor-default leading-[18px] group-data-[sidebar-size=sm]:hidden block"
                    data-key="t-menu">Menu</li>
                <li>
                    <a href="{{ url('/admin') }}"
                        class="block py-2.5 px-6 text-sm font-medium text-gray-950 transition-all duration-150 ease-linear hover:text-violet-500 dark:text-gray-300 dark:active:text-white dark:hover:text-white">
                        <i data-feather="home" fill="#545a6d33"></i>
                        <span data-key="t-dashboard"> Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="javascript: void(0);" aria-expanded="false"
                        class="block py-2.5 px-6 text-sm font-medium text-gray-950 transition-all duration-150 ease-linear nav-menu hover:text-violet-500 dark:text-gray-300 dark:active:text-white dark:hover:text-white">
                        <i data-feather="file-text" class="align-middle" fill="#545a6d33"></i>
                        <span data-key="t-apps"> Assessment</span>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ url('/events') }}"
                                class="pl-[52.8px] pr-6 py-[6.4px] block text-[13.5px] font-medium text-gray-950 transition-all duration-150 ease-linear hover:text-violet-500 dark:text-gray-300 dark:active:text-white dark:hover:text-white">Events</a>
                        </li>
                        <li>
                            <a href="{{ url('/results') }}"
                                class="pl-[52.8px] pr-6 py-[6.4px] block text-[13.5px] font-medium text-gray-950 transition-all duration-150 ease-linear hover:text-violet-500 dark:text-gray-300 dark:active:text-white dark:hover:text-white">Results</a>
                        </li>
                        <li>
                            <a href="{{ url('/category') }}"
                                class="pl-[52.8px] pr-6 py-[6.4px] block text-[13.5px] font-medium text-gray-950 transition-all duration-150 ease-linear hover:text-violet-500 dark:text-gray-300 dark:active:text-white dark:hover:text-white">Category</a>
                        </li>
                        <li>
                            <a href="{{ url('/topic') }}"
                                class="pl-[52.8px] pr-6 py-[6.4px] block text-[13.5px] font-medium text-gray-950 transition-all duration-150 ease-linear hover:text-violet-500 dark:text-gray-300 dark:active:text-white dark:hover:text-white">Topic</a>
                        </li>
                        <li>
                            <a href="{{ url('/question') }}"
                                class="pl-[52.8px] pr-6 py-[6.4px] block text-[13.5px] font-medium text-gray-950 transition-all duration-150 ease-linear hover:text-violet-500 dark:text-gray-300 dark:active:text-white dark:hover:text-white">Question</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</aside>
<!-- ========== Left Sidebar End ========== -->
