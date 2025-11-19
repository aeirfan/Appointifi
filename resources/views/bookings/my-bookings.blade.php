<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h2 class="font-semibold text-2xl text-gray-900 dark:text-white">My Appointments</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">View and manage your appointments</p>
            </div>

            @if(session('status'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-green-800 dark:text-green-200">{{ session('status') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Tabbed Interface -->
            <div x-data="{ tab: 'upcoming' }" class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                <!-- Tabs -->
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <nav class="flex -mb-px">
                        <button 
                            @click="tab = 'upcoming'" 
                            :class="{ 
                                'border-primary-500 text-primary-600 dark:text-primary-400': tab === 'upcoming', 
                                'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': tab !== 'upcoming' 
                            }" 
                            class="py-4 px-6 text-sm font-semibold border-b-2 focus:outline-none transition-colors duration-200"
                        >
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Upcoming ({{ $upcomingAppointments->count() }})
                            </span>
                        </button>
                        
                        <button 
                            @click="tab = 'completed'" 
                            :class="{ 
                                'border-primary-500 text-primary-600 dark:text-primary-400': tab === 'completed', 
                                'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': tab !== 'completed' 
                            }" 
                            class="py-4 px-6 text-sm font-semibold border-b-2 focus:outline-none transition-colors duration-200"
                        >
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Completed ({{ $completedAppointments->count() }})
                            </span>
                        </button>
                        
                        <button 
                            @click="tab = 'cancelled'" 
                            :class="{ 
                                'border-primary-500 text-primary-600 dark:text-primary-400': tab === 'cancelled', 
                                'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': tab !== 'cancelled' 
                            }" 
                            class="py-4 px-6 text-sm font-semibold border-b-2 focus:outline-none transition-colors duration-200"
                        >
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancelled ({{ $cancelledAppointments->count() }})
                            </span>
                        </button>
                    </nav>
                </div>

                <!-- Upcoming Appointments Tab -->
                <div x-show="tab === 'upcoming'" class="p-6">
                    @if($upcomingAppointments->isEmpty())
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="font-medium text-gray-900 dark:text-white">No upcoming appointments</p>
                            <p class="text-sm mt-1">Book your first appointment to get started</p>
                            <div class="mt-6">
                                <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 rounded-lg text-white transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Book Appointment
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($upcomingAppointments as $appointment)
                                @include('bookings.partials.appointment-card', ['appointment' => $appointment, 'showCancel' => true])
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Completed Appointments Tab -->
                <div x-show="tab === 'completed'" class="p-6">
                    @if($completedAppointments->isEmpty())
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="font-medium text-gray-900 dark:text-white">No completed appointments</p>
                            <p class="text-sm mt-1">Your completed appointments will appear here</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($completedAppointments as $appointment)
                                @include('bookings.partials.appointment-card', ['appointment' => $appointment, 'showCancel' => false])
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Cancelled Appointments Tab -->
                <div x-show="tab === 'cancelled'" class="p-6">
                    @if($cancelledAppointments->isEmpty())
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <p class="font-medium text-gray-900 dark:text-white">No cancelled appointments</p>
                            <p class="text-sm mt-1">Your cancelled appointments will appear here</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($cancelledAppointments as $appointment)
                                @include('bookings.partials.appointment-card', ['appointment' => $appointment, 'showCancel' => false])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
