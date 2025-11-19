<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h2 class="font-semibold text-2xl text-gray-900 dark:text-white">Manage Appointments</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Track and manage all customer appointments</p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-400 rounded-lg flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <div x-data="{ tab: 'upcoming' }" class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                <!-- Tabs -->
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <nav class="flex -mb-px">
                        <button @click="tab = 'upcoming'" :class="{ 'border-primary-500 text-primary-600 dark:text-primary-400': tab === 'upcoming', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': tab !== 'upcoming' }" class="py-4 px-6 text-sm font-semibold border-b-2 focus:outline-none transition-colors duration-200">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Upcoming ({{ $upcomingAppointments->count() }})
                            </span>
                        </button>
                        <button @click="tab = 'past'" :class="{ 'border-primary-500 text-primary-600 dark:text-primary-400': tab === 'past', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': tab !== 'past' }" class="py-4 px-6 text-sm font-semibold border-b-2 focus:outline-none transition-colors duration-200">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Past ({{ $pastAppointments->count() }})
                            </span>
                        </button>
                    </nav>
                </div>

                <!-- Upcoming Appointments Tab -->
                <div x-show="tab === 'upcoming'" class="p-6">
                    @if($upcomingAppointments->isEmpty())
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="font-medium">No upcoming appointments</p>
                            <p class="text-sm mt-1">New bookings will appear here</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($upcomingAppointments as $appointment)
                                <div class="border-2 border-primary-200 dark:border-primary-800 rounded-lg p-5 bg-gradient-to-r from-primary-50/50 to-transparent dark:from-primary-900/20 dark:to-transparent">
                                    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-start gap-4">
                                                <!-- Customer Avatar -->
                                                <div class="flex-shrink-0">
                                                    <div class="w-12 h-12 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-lg">
                                                        {{ strtoupper(substr($appointment->customer->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                
                                                <div class="flex-1">
                                                    <div class="flex flex-wrap items-center gap-3 mb-2">
                                                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ $appointment->customer->name }}</h3>
                                                        <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                                            {{ $appointment->status === 'confirmed' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400' }}">
                                                            {{ ucfirst($appointment->status) }}
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-3">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                        </svg>
                                                        {{ $appointment->customer->email }}
                                                    </div>
                                                    
                                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 mb-3">
                                                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $appointment->service->name }}</span>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                                        <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                            </svg>
                                                            <span class="font-medium">{{ $appointment->start_time->format('l, M j, Y') }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <span class="font-medium">{{ $appointment->start_time->format('g:i A') }} - {{ $appointment->end_time->format('g:i A') }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <span>{{ $appointment->service->duration }} minutes</span>
                                                        </div>
                                                        @if($appointment->service->price)
                                                            <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                <span class="font-semibold">RM {{ number_format($appointment->service->price, 2) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="flex flex-row lg:flex-col gap-2 lg:min-w-[160px]">
                                            <form method="POST" action="{{ route('business.appointments.update-status', $appointment) }}" class="flex-1 lg:flex-none">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="arrival">
                                                <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 border border-transparent rounded-lg font-semibold text-xs text-white shadow-md transition-colors duration-200 flex items-center justify-center gap-2" onclick="return confirm('Mark this customer as Arrived?');">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                    Arrival
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('business.appointments.update-status', $appointment) }}" class="flex-1 lg:flex-none">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 border border-transparent rounded-lg font-semibold text-xs text-white shadow-md transition-colors duration-200 flex items-center justify-center gap-2" onclick="return confirm('Mark this customer as Completed?');">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Complete
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('business.appointments.update-status', $appointment) }}" class="flex-1 lg:flex-none">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="no_show">
                                                <button type="submit" class="w-full px-4 py-2.5 bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white shadow-md transition-colors duration-200 flex items-center justify-center gap-2" onclick="return confirm('Mark this customer as No Show?');">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    No Show
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Past Appointments Tab -->
                <div x-show="tab === 'past'" class="p-6">
                    @if($pastAppointments->isEmpty())
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="font-medium">No past appointments</p>
                            <p class="text-sm mt-1">Completed appointments will appear here</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($pastAppointments as $appointment)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 bg-white dark:bg-gray-800 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-start gap-4">
                                        <!-- Customer Avatar -->
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-gray-400 dark:bg-gray-600 text-white flex items-center justify-center font-bold">
                                                {{ strtoupper(substr($appointment->customer->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        
                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">{{ $appointment->customer->name }}</h3>
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                                    {{ $appointment->status === 'completed' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400' : '' }}
                                                    {{ $appointment->status === 'cancelled' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400' : '' }}
                                                    {{ $appointment->status === 'no_show' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400' : '' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                                </span>
                                            </div>
                                            
                                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $appointment->customer->email }}
                                            </div>
                                            
                                            <p class="text-gray-900 dark:text-white font-medium mb-2">{{ $appointment->service->name }}</p>
                                            
                                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $appointment->start_time->format('l, M j, Y \a\t g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
