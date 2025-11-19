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
                    <form method="POST" action="{{ route('business.profile.store') }}" class="space-y-6">
                        @csrf

                        <!-- Business & Location Details Section -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Business Details</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update your business name and description</p>
                            </div>
                            <div class="p-6 space-y-6">
                                <div>
                                    <x-input-label for="business_name" :value="__('Business Name')" class="text-gray-700 dark:text-gray-300 font-medium" />
                                    <x-text-input id="business_name" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" type="text" name="business_name" :value="old('business_name')" required />
                                    <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="description" :value="__('Description (Optional)')" class="text-gray-700 dark:text-gray-300 font-medium" />
                                    <textarea id="description" name="description" rows="4" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" placeholder="Tell customers about your business...">{{ old('description') }}</textarea>
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
                                    <x-text-input id="address" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" type="text" name="address" :value="old('address')" required placeholder="123 Main Street" />
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="city" :value="__('City')" class="text-gray-700 dark:text-gray-300 font-medium" />
                                    <x-text-input id="city" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" type="text" name="city" :value="old('city')" required placeholder="New York" />
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
                                Create Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>