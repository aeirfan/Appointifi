<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h2 class="font-semibold text-2xl text-gray-900 dark:text-white">Book: {{ $service->name }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Select a date and time for your appointment</p>
            </div>
            @if (session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            @endif
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
                <div class="p-6">
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-2">{{ $business->name }}</h3>
                    <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $service->duration }} minutes</span>
                        </div>
                        @if($service->price)
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-semibold text-gray-900 dark:text-white">RM {{ number_format($service->price, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-4">Select Date</h3>
                    <form method="GET" action="{{ route('bookings.availability', [$business, $service]) }}" class="mb-6">
                        <div class="flex gap-2">
                            <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" min="{{ now()->addDay()->format('Y-m-d') }}" class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 rounded-lg text-white font-medium">Check</button>
                        </div>
                    </form>
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-4">Available Times on {{ $date->format('l, F j, Y') }}</h3>
                    @if($slots->isEmpty())
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                        <strong>No available time slots on this date.</strong>
                                        @if($unavailabilityReason)
                                            <br>
                                            <span class="font-medium">Reason: {{ $unavailabilityReason }}</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-yellow-600 dark:text-yellow-300 mt-1">Please try selecting another day.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach($slots as $slot)
                                <form method="POST" action="{{ route('bookings.store') }}">
                                    @csrf
                                    <input type="hidden" name="business_id" value="{{ $business->id }}">
                                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                                    <input type="hidden" name="start_time" value="{{ $slot['start_time']->toIso8601String() }}">
                                    <button type="submit" class="w-full px-4 py-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-primary-500 dark:hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition text-center font-medium text-gray-900 dark:text-white" onclick="return confirm('Are you sure you want to book this slot?');">
                                        {{ $slot['formatted_time'] }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
