<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Your Business Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">Welcome! You must create a business profile before you can access the dashboard.</p>

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('business.profile.store') }}">
                        @csrf

                        <h3 class="text-lg font-medium">Business Details</h3>
                        <div class="mt-4">
                            <x-input-label for="business_name" :value="__('Business Name')" />
                            <x-text-input id="business_name" class="block mt-1 w-full" type="text" name="business_name" :value="old('business_name')" required autofocus />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        </div>

                        <h3 class="text-lg font-medium mt-6">Location</h3>
                        <div class="mt-4">
                            <x-input-label for="address" :value="__('Address')" />
                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="city" :value="__('City')" />
                            <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" required />
                        </div>
                        <div class="grid grid-cols-1 gap-4 mt-4">
                            <p class="text-sm text-gray-600">We will look up latitude/longitude automatically from the address you provide.</p>
                        </div>

                        <h3 class="text-lg font-medium mt-6">Opening Hours</h3>
                        <p class="text-sm text-gray-600">Leave both fields empty to mark a day as closed.</p>
                        @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <div class="mt-4 border border-gray-200 rounded-lg p-4">
                                <label class="font-medium capitalize text-gray-900 block mb-2">{{ $day }}</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label :value="__('Open')" />
                                        <x-text-input type="time" name="hours[{{ $day }}][open]" class="block w-full mt-1" placeholder="09:00" />
                                    </div>
                                    <div>
                                        <x-input-label :value="__('Close')" />
                                        <x-text-input type="time" name="hours[{{ $day }}][close]" class="block w-full mt-1" placeholder="17:00" />
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Create Profile') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>