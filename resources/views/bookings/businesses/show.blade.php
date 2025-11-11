<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-900 dark:text-white">{{ $business->name }}</h2>
                    @if($business->locations->first())
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $business->locations->first()->city }}</p>
                    @endif
                </div>
                <a href="{{ route('bookings.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    ← Back to search
                </a>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    @if($business->description)
                        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
                            <div class="p-6">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-2">About</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ $business->description }}</p>
                            </div>
                        </div>
                    @endif
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="p-6">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-4">Services</h3>
                            @if($business->services->isEmpty())
                                <p class="text-gray-500 dark:text-gray-400">No services available.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach($business->services as $service)
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-primary-300 dark:hover:border-primary-700 transition bg-white dark:bg-gray-800">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $service->name }}</h4>
                                                    @if($service->description)
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $service->description }}</p>
                                                    @endif
                                                    <div class="flex gap-4 text-sm">
                                                        <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            {{ $service->duration }} mins
                                                        </span>
                                                        @if($service->price)
                                                            <span class="text-gray-900 dark:text-white font-semibold flex items-center gap-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                RM {{ number_format($service->price, 2) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <a href="{{ route('bookings.availability', [$business, $service]) }}" class="ml-4 flex-shrink-0 inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 rounded-lg text-white text-sm font-medium">
                                                    Book Now
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-1 space-y-6">
                    @if($business->locations->first())
                        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="p-6">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Location
                                </h3>
                                <p class="text-gray-700 dark:text-gray-300 text-sm">
                                    {{ $business->locations->first()->address }}<br>
                                    {{ $business->locations->first()->city }}
                                </p>
                            </div>
                        </div>
                        @if($business->locations->first()->opening_hours)
                            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="p-6">
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Opening Hours
                                        </h3>
                                        @auth
                                            @if(auth()->id() === $business->owner_id)
                                                <a href="{{ route('business.location.edit') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">Edit</a>
                                            @endif
                                        @endauth
                                    </div>
                                    <div class="space-y-2 text-sm">
                                        @php
                                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                            $today = strtolower(now()->format('l'));
                                        @endphp
                                        @foreach($days as $day)
                                            @php
                                                $hours = $business->locations->first()->opening_hours[$day] ?? null;
                                                $isToday = $day === $today;
                                            @endphp
                                            <div class="flex justify-between {{ $isToday ? 'font-semibold text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300' }}">
                                                <span class="capitalize">{{ $day }}{{ $isToday ? ' (Today)' : '' }}</span>
                                                <span>
                                                    @if($hours && !empty($hours['open']) && !empty($hours['close']))
                                                        {{ $hours['open'] }} - {{ $hours['close'] }}
                                                    @else
                                                        Closed
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
