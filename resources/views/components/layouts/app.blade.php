<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js with Persist Plugin -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-slate-900"
          x-data
          x-init="
            // Initialize Alpine store for sidebar
            $store.sidebar = {
              collapsed: Alpine.$persist(false).as('sidebar-collapsed'),
              toggle() {
                this.collapsed = !this.collapsed;
              }
            };
          ">

        <div class="min-h-screen flex">
            <!-- Desktop Sidebar -->
            <aside
                x-data
                :class="{'w-64': !$store.sidebar.collapsed, 'w-20': $store.sidebar.collapsed}"
                class="hidden md:flex fixed inset-y-0 z-30 flex-col bg-slate-900 border-r border-slate-800 transition-all duration-300"
            >
                <div class="h-16 flex items-center px-6 border-b border-slate-800">
                    <a href="/" class="flex items-center gap-3">
                        <svg class="w-8 h-8 text-teal-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke-width="2" stroke-linejoin="round" />
                            <path d="M2 17L12 22L22 17" stroke-width="2" stroke-linejoin="round" />
                            <path d="M2 12L12 17L22 12" stroke-width="2" stroke-linejoin="round" />
                        </svg>
                        <span x-show="!$store.sidebar.collapsed" class="text-xl font-bold text-white transition-all duration-300">CinchCal</span>
                    </a>
                </div>

                <nav class="flex-1 overflow-y-auto py-6 px-4">
                    <div class="space-y-1">
                        <x-sidebar-link
                            :href="route('dashboard')"
                            :active="request()->routeIs('dashboard')"
                            :icon="
                                '<svg class=\'w-5 h-5 text-inherit\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6\'></path></svg>'
                            "
                        >
                            Dashboard
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('appointments.index')"
                            :active="request()->routeIs('appointments.*')"
                            :icon="
                                '<svg class=\'w-5 h-5 text-inherit\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg>'
                            "
                        >
                            Appointments
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('customers.index')"
                            :active="request()->routeIs('customers.*')"
                            :icon="
                                '<svg class=\'w-5 h-5 text-inherit\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z\'></path></svg>'
                            "
                        >
                            Customers
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('settings.index')"
                            :active="request()->routeIs('settings.*')"
                            :icon="
                                '<svg class=\'w-5 h-5 text-inherit\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z\'></path><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M15 12a3 3 0 11-6 0 3 3 0 016 0z\'></path></svg>'
                            "
                        >
                            Settings
                        </x-sidebar-link>
                    </div>
                </nav>

                <div class="p-4 border-t border-slate-800">
                    <button
                        @click="$store.sidebar.toggle()"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition-colors duration-200"
                    >
                        <svg class="w-5 h-5 text-inherit" :class="{'rotate-180': $store.sidebar.collapsed}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span x-show="!$store.sidebar.collapsed" class="font-medium transition-all duration-300 overflow-hidden whitespace-nowrap">Collapse</span>
                    </button>
                </div>
            </aside>

            <!-- Mobile Menu Button -->
            <div class="md:hidden fixed top-4 left-4 z-20">
                <button
                    @click="showMobileMenu = true"
                    class="p-2 rounded-lg bg-slate-800 text-slate-200 hover:bg-slate-700"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Sidebar Overlay -->
            <div
                x-show="showMobileMenu"
                @click="showMobileMenu = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden"
                style="display: none;"
            ></div>

            <!-- Mobile Sidebar -->
            <aside
                x-show="showMobileMenu"
                @click.outside="showMobileMenu = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="fixed top-0 left-0 z-50 h-full w-64 bg-slate-900 border-r border-slate-800 flex flex-col md:hidden"
            >
                <div class="h-16 flex items-center px-6 border-b border-slate-800">
                    <a href="/" class="flex items-center gap-3">
                        <svg class="w-8 h-8 text-teal-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke-width="2" stroke-linejoin="round" />
                            <path d="M2 17L12 22L22 17" stroke-width="2" stroke-linejoin="round" />
                            <path d="M2 12L12 17L22 12" stroke-width="2" stroke-linejoin="round" />
                        </svg>
                        <span class="text-xl font-bold text-white">CinchCal</span>
                    </a>
                </div>

                <nav class="flex-1 overflow-y-auto py-6 px-4">
                    <div class="space-y-1">
                        <x-sidebar-link
                            :href="route('dashboard')"
                            :active="request()->routeIs('dashboard')"
                            :icon="
                                '<svg class=\'w-5 h-5 text-inherit\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6\'></path></svg>'
                            "
                            @click="showMobileMenu = false"
                        >
                            Dashboard
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('appointments.index')"
                            :active="request()->routeIs('appointments.*')"
                            :icon="
                                '<svg class=\'w-5 h-5 text-inherit\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg>'
                            "
                            @click="showMobileMenu = false"
                        >
                            Appointments
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('customers.index')"
                            :active="request()->routeIs('customers.*')"
                            :icon="
                                '<svg class=\'w-5 h-5 text-inherit\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z\'></path></svg>'
                            "
                            @click="showMobileMenu = false"
                        >
                            Customers
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('settings.index')"
                            :active="request()->routeIs('settings.*')"
                            :icon="
                                '<svg class=\'w-5 h-5 text-inherit\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z\'></path><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M15 12a3 3 0 11-6 0 3 3 0 016 0z\'></path></svg>'
                            "
                            @click="showMobileMenu = false"
                        >
                            Settings
                        </x-sidebar-link>
                    </div>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 md:ml-0 transition-all duration-300" :class="{'md:ml-20': $store.sidebar.collapsed, 'md:ml-64': !$store.sidebar.collapsed}">
                <!-- Mobile header to provide space for mobile menu button -->
                <div class="md:hidden h-16"></div>

                <div class="py-6 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <script>
            // Add Alpine store initialization
            document.addEventListener('alpine:init', () => {
                Alpine.store('sidebar', {
                    collapsed: Alpine.$persist(false).as('sidebar-collapsed'),
                    toggle() {
                        this.collapsed = !this.collapsed;
                    }
                });
            });
        </script>
    </body>
</html>