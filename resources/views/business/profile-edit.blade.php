<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-gray-900 dark:text-white">
                    {{ __('Business Profile & Settings') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage your business information, hours, and availability
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        @if (session('status'))
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-700 dark:text-green-400 font-medium">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-400">There were some errors with your submission</h3>
                        <ul class="list-disc list-inside mt-2 text-sm text-red-700 dark:text-red-400">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Business & Opening Hours Form ONLY -->
        <form method="POST" action="{{ route('business.profile.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Business & Location Details Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Business Details</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update your business name and description</p>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <x-input-label for="business_name" :value="__('Business Name')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <x-text-input id="business_name" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" type="text" name="business_name" :value="old('business_name', $business->name)" required />
                        <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description (Optional)')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <textarea id="description" name="description" rows="4" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" placeholder="Tell customers about your business...">{{ old('description', $business->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Location Details Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Location Details</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Your business address and coordinates</p>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <x-input-label for="address" :value="__('Address')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <x-text-input id="address" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" type="text" name="address" :value="old('address', $location->address)" required placeholder="123 Main Street" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="city" :value="__('City')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <x-text-input id="city" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" type="text" name="city" :value="old('city', $location->city)" required placeholder="New York" />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-400">Coordinates</p>
                                <p class="text-sm text-blue-700 dark:text-blue-500 mt-1">
                                    @if($location->latitude && $location->longitude)
                                        Lat: {{ number_format($location->latitude, 6) }}, Long: {{ number_format($location->longitude, 6) }}
                                    @else
                                        Not yet geocoded
                                    @endif
                                </p>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Coordinates will be automatically updated when you save</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opening Hours Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Opening Hours</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Set your business hours for each day of the week</p>
                </div>
                <div class="p-6">
                    @php
                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        $openingHours = $location->opening_hours ?? [];
                    @endphp
                    
                    <div class="space-y-4">
                        @foreach ($days as $day)
                            @php
                                $hours = $openingHours[$day] ?? null;
                                $openTime = old("hours.{$day}.open", $hours['open'] ?? '');
                                $closeTime = old("hours.{$day}.close", $hours['close'] ?? '');
                                $isClosed = is_null($hours);
                            @endphp
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="font-semibold capitalize text-gray-900 dark:text-white text-base">{{ ucfirst($day) }}</label>
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" name="closed[{{ $day }}]" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 dark:bg-gray-700" @checked(old("closed.$day", $isClosed))>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Closed</span>
                                    </label>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label :value="__('Opening Time')" class="text-gray-700 dark:text-gray-300 text-sm font-medium" />
                                        <x-text-input type="time" name="hours[{{ $day }}][open]" class="block w-full mt-1.5 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-white" :value="$openTime" placeholder="09:00" />
                                    </div>
                                    <div>
                                        <x-input-label :value="__('Closing Time')" class="text-gray-700 dark:text-gray-300 text-sm font-medium" />
                                        <x-text-input type="time" name="hours[{{ $day }}][close]" class="block w-full mt-1.5 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-white" :value="$closeTime" placeholder="17:00" />
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Check "Closed" to mark this day as a rest day</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700 mt-6">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Business & Hours
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Recurring Block Times Section -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recurring Block Times</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Set up recurring blocked times for breaks, lunch, or prayer time</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if($recurringBlockedTimes->isNotEmpty())
                    <div class="space-y-3 mb-6">
                        @foreach($recurringBlockedTimes as $recurring)
                            <div class="border border-purple-200 dark:border-purple-700 rounded-lg p-4 flex justify-between items-center bg-purple-50 dark:bg-purple-900/20">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $recurring->title ?: 'Recurring Block' }}</p>
                                        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-400 rounded-full text-xs font-medium">
                                            {{ \Carbon\Carbon::parse($recurring->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($recurring->end_time)->format('g:i A') }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                        <span class="font-medium">Days:</span> {{ collect($recurring->days_of_week)->map(fn($d) => ucfirst($d))->join(', ') }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('business.recurring-blocks.delete', $recurring->id) }}" onsubmit="return confirm('Remove this recurring block?');" class="flex-shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 border border-transparent rounded-lg font-semibold text-sm text-white shadow-sm transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Remove
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 mb-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">No recurring block times set</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Add your first recurring block below</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('business.recurring-blocks.store') }}" class="p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                    @csrf
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Add New Recurring Block</h4>
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="recurring_title" :value="__('Title (Optional)')" class="text-gray-700 dark:text-gray-300 font-medium" />
                            <x-text-input id="recurring_title" name="title" class="block mt-2 w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-white" type="text" :value="old('title')" placeholder="e.g., Lunch Break" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="recurring_start_time" :value="__('Start Time')" class="text-gray-700 dark:text-gray-300 font-medium" />
                                <x-text-input id="recurring_start_time" name="start_time" class="block mt-2 w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-white" type="time" :value="old('start_time')" required />
                                <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="recurring_end_time" :value="__('End Time')" class="text-gray-700 dark:text-gray-300 font-medium" />
                                <x-text-input id="recurring_end_time" name="end_time" class="block mt-2 w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-white" type="time" :value="old('end_time')" required />
                                <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            <x-input-label :value="__('Repeats On (Select Days)')" class="text-gray-700 dark:text-gray-300 font-medium mb-3" />
                            <x-input-error :messages="$errors->get('days_of_week')" class="mt-1" />
                            <div class="grid grid-cols-4 md:grid-cols-7 gap-2">
                                @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                                    <label class="relative cursor-pointer">
                                        <input type="checkbox" name="days_of_week[]" value="{{ $day }}" class="peer sr-only" @checked(is_array(old('days_of_week')) && in_array($day, old('days_of_week')))>
                                        <div class="flex items-center justify-center p-3 border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 rounded-lg transition peer-checked:bg-primary-600 peer-checked:border-primary-600 peer-checked:text-white hover:border-primary-400 dark:hover:border-primary-500">
                                            <span class="text-xs font-semibold">{{ substr(ucfirst($day),0,3) }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Recurring Block
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Holidays Section -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Holiday Dates</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Mark full days when your business will be closed</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if($holidays->isNotEmpty())
                    <div class="space-y-3 mb-6">
                        @foreach($holidays as $holiday)
                            <div class="border border-green-200 dark:border-green-700 rounded-lg p-4 flex justify-between items-center bg-green-50 dark:bg-green-900/20">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="bg-green-600 text-white rounded-lg p-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($holiday->date)->format('F j, Y') }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($holiday->date)->format('l') }}</p>
                                            @if($holiday->name)
                                                <span class="inline-block mt-1 px-2 py-1 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 rounded text-xs font-medium">{{ $holiday->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('business.holidays.delete', $holiday->id) }}" onsubmit="return confirm('Remove this holiday?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-400 p-2 transition-colors duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p>No holidays set yet</p>
                            </div>
                        @endif

                        <!-- Add Holiday Form -->
                        <form method="POST" action="{{ route('business.holidays.store') }}" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="holiday_date" :value="__('Holiday Date')" />
                                    <x-text-input id="holiday_date" name="date" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-colors duration-200" type="date" :value="old('date')" required />
                                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="holiday_name" :value="__('Holiday Name (Optional)')" />
                                    <x-text-input id="holiday_name" name="name" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-colors duration-200" type="text" :value="old('name')" placeholder="e.g., Christmas" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                            </div>
                            <div class="flex justify-end pt-4">
                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Holiday
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    </div>
</x-app-layout>
