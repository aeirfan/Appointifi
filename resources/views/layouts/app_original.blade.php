<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Appointifi</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('avatar.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Dark Mode Script (must run before body renders) -->
        <script>
            if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && @json(session('dark_mode', false)))) {
                document.documentElement.classList.add('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' || @json(session('dark_mode', false)) }" x-init="$watch('darkMode', value => localStorage.setItem('darkMode', value))">
        <div class="min-h-screen flex">
            <!-- Sidebar Component -->
            <livewire:sidebar />

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Unified Header (visible on all devices, but with different content) -->
                <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-40 flex items-center justify-between p-4">
                    <!-- Mobile menu button (only visible on mobile) -->
                    <div class="md:hidden">
                        <button @click="$dispatch('toggle-sidebar')" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Desktop sidebar toggle button (only visible on desktop) -->
                    <div class="hidden md:block">
                        <button @click="$dispatch('toggle-desktop-sidebar')" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>

                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white flex-grow md:flex-grow-0 md:ml-4">Appointifi</h1>

                    <div class="flex items-center space-x-4">
                        <!-- Additional header content can go here -->
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
                    <div class="max-w-7xl mx-auto">
                        @isset($header)
                            <div class="mb-6">
                                {{ $header }}
                            </div>
                        @endisset

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
