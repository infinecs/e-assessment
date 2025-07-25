<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Starter Page | Minia - Admin & Dashboard Template</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Tailwind Admin & Dashboard Template" name="description">
        <meta content="Themesbrand" name="author">
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
        <!-- Tailwind CSS -->
        <link rel="stylesheet" href="{{ asset('css/tailwind2.css') }}">
    </head>

    <body data-mode="light" data-sidebar-size="lg" class="group min-h-screen flex flex-col">
        <!-- Header always on top -->
        @include('inc.headerMain')

        <div class="flex flex-1">
            <!-- Sidebar -->
            @include('inc.sidebarMain')

            <!-- Main Content -->
            <div class="main-content group-data-[sidebar-size=sm]:ml-[70px] flex flex-col flex-1 bg-white dark:bg-zinc-800">
                <div class="flex-1 page-content dark:bg-zinc-700">
                    <div class="container-fluid px-[0.625rem] py-2">
                        @yield('content')
                    </div>
                </div>
                <!-- Footer -->
                @include('inc.footerMain')
            </div>
        </div>

        @include('inc.scripts')
    </body>
</html>
