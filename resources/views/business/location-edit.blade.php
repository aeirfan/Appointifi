<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Location & Opening Hours') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('business.location.update') }}">
                        @csrf
                        @method('PATCH')

                        <h3 class="text-lg font-medium">Location Details</h3>
                        
                        <div class="mt-4">
                            <x-input-label for="address" :value="__('Address')" />
                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $location->address)" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="city" :value="__('City')" />
                            <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city', $location->city)" required />
                            <x-input-error :messages="$errors->get('city')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Current Latitude: {{ $location->latitude ?? 'N/A' }}, Longitude: {{ $location->longitude ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">Coordinates will be automatically updated when you save the address.</p>
                        </div>

                        <h3 class="text-lg font-medium mt-6">Opening Hours</h3>
                        <p class="text-sm text-gray-600 mt-2">Leave both fields empty to mark a day as closed.</p>
                        
                        @php
                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            $openingHours = $location->opening_hours ?? [];
                        @endphp
                        
                        @foreach ($days as $day)
                            @php
                                $hours = $openingHours[$day] ?? null;
                                $openTime = old("hours.{$day}.open", $hours['open'] ?? '');
                                $closeTime = old("hours.{$day}.close", $hours['close'] ?? '');
                            @endphp
                            <div class="mt-4 border border-gray-200 rounded-lg p-4">
                                <label class="font-medium capitalize text-gray-900 block mb-2">{{ $day }}</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label :value="__('Open')" />
                                        <x-text-input type="time" name="hours[{{ $day }}][open]" class="block w-full mt-1" :value="$openTime" placeholder="09:00" />
                                    </div>
                                    <div>
                                        <x-input-label :value="__('Close')" />
                                        <x-text-input type="time" name="hours[{{ $day }}][close]" class="block w-full mt-1" :value="$closeTime" placeholder="17:00" />
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <a href="{{ route('business.dashboard') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                            <x-primary-button>
                                {{ __('Save Changes') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
