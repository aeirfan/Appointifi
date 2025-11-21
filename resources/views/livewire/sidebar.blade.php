<div
    x-data="{
        isOpen: @entangle('isOpen')
    }"
    @toggle-sidebar.window="isOpen = !isOpen"
    class="relative"
>
    <!-- Mobile overlay backdrop -->
    <div 
        x-show="isOpen" 
        @click="isOpen = false"
        x-transition.opacity
        class="fixed inset-0 bg-black/50 z-40 md:hidden"
    ></div>
    <!-- Sidebar -->
    <aside
        class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out flex flex-col h-screen overflow-y-auto"
        :class="{
            // Mobile: slide off-screen when closed, full width when open
            '-translate-x-full md:translate-x-0': !isOpen,
            'translate-x-0': isOpen,
            'w-64': !isOpen || isOpen,
            // Desktop: collapse width but always visible
            'md:w-20': $store.sidebar?.collapsed ?? false,
            'md:w-64': !($store.sidebar?.collapsed ?? false)
        }"
    >
        <!-- Header -->
        <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white" x-show="!($store.sidebar?.collapsed ?? false)">
                Appointifi
            </h2>
            
            <button
                @click="$store.sidebar?.toggle()"
                class="hidden md:block text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                :class="{'mx-auto': $store.sidebar?.collapsed ?? false, 'ml-auto': !($store.sidebar?.collapsed ?? false)}"
                aria-label="Toggle sidebar"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!($store.sidebar?.collapsed ?? false)">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="$store.sidebar?.collapsed ?? false">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-2">
            @auth
                @if(auth()->user()->role === 'owner')
                    <a href="{{ route('business.dashboard') }}" :class="{'justify-center': $store.sidebar?.collapsed ?? false}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('business.dashboard') ? 'bg-primary-50 dark:bg-primary-900 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <template x-if="!($store.sidebar?.collapsed ?? false)">
                            <span class="font-medium">Dashboard</span>
                        </template>
                    </a>
                    <a href="{{ route('business.appointments') }}" :class="{'justify-center': $store.sidebar?.collapsed ?? false}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('business.appointments') ? 'bg-primary-50 dark:bg-primary-900 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <template x-if="!($store.sidebar?.collapsed ?? false)">
                            <span class="font-medium">Appointments</span>
                        </template>
                    </a>
                    <a href="{{ route('business.services.index') }}" :class="{'justify-center': $store.sidebar?.collapsed ?? false}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('business.services.*') ? 'bg-primary-50 dark:bg-primary-900 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <template x-if="!($store.sidebar?.collapsed ?? false)">
                            <span class="font-medium">Services</span>
                        </template>
                    </a>
                    <a href="{{ route('business.profile.edit') }}" :class="{'justify-center': $store.sidebar?.collapsed ?? false}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('business.profile.edit') ? 'bg-primary-50 dark:bg-primary-900 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/></svg>
                        <template x-if="!($store.sidebar?.collapsed ?? false)">
                            <span class="font-medium">Profile & Hours</span>
                        </template>
                    </a>
                @elseif(auth()->user()->role === 'customer')
                    <a href="{{ route('bookings.index') }}" :class="{'justify-center': $store.sidebar?.collapsed ?? false}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('bookings.index') ? 'bg-primary-50 dark:bg-primary-900 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <template x-if="!($store.sidebar?.collapsed ?? false)">
                            <span class="font-medium">Home</span>
                        </template>
                    </a>
                    <a href="{{ route('bookings.my-bookings') }}" :class="{'justify-center': $store.sidebar?.collapsed ?? false}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('bookings.my-bookings') ? 'bg-primary-50 dark:bg-primary-900 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <template x-if="!($store.sidebar?.collapsed ?? false)">
                            <span class="font-medium">My Appointments</span>
                        </template>
                    </a>
                    <a href="{{ route('bookings.index') }}" :class="{'justify-center': $store.sidebar?.collapsed ?? false}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <template x-if="!($store.sidebar?.collapsed ?? false)">
                            <span class="font-medium">Book New</span>
                        </template>
                    </a>
                @endif
            @endauth
        </nav>

        <!-- Footer section: Dark mode + user info -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4 space-y-3">
            <!-- Dark Mode Toggle -->
            <button
                @click="$store.darkMode = !$store.darkMode; document.documentElement.classList.toggle('dark'); $wire.toggleDarkMode()"
                :class="{'justify-center': $store.sidebar?.collapsed ?? false}"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
                <template x-if="!$store.darkMode">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </template>
                <template x-if="$store.darkMode">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </template>
                <template x-if="!($store.sidebar?.collapsed ?? false)">
                    <span class="font-medium" x-text="$store.darkMode ? 'Light Mode' : 'Dark Mode'"></span>
                </template>
            </button>

            @auth
                <template x-if="!($store.sidebar?.collapsed ?? false)">
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Logged in as</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ ucfirst(auth()->user()->role ?? 'user') }}</p>
                    </div>
                </template>

                <a href="{{ route('profile.edit') }}" :class="{'justify-center': $store.sidebar?.collapsed ?? false}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <template x-if="!($store.sidebar?.collapsed ?? false)">
                        <span class="font-medium">Profile</span>
                    </template>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" :class="{'justify-center': $store.sidebar?.collapsed ?? false}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <template x-if="!($store.sidebar?.collapsed ?? false)">
                            <span class="font-medium">Log Out</span>
                        </template>
                    </button>
                </form>
            @endauth
        </div>
    </aside>
</div>
