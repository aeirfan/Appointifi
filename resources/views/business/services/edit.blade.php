<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-900 dark:text-white">Edit Service: {{ $service->name }}</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update service details</p>
                </div>
                <a href="{{ route('business.services.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-secondary-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-sm text-gray-900 dark:text-white shadow-sm transition-colors duration-200">
                    Back to Manage Services
                </a>
            </div>

            <!-- Status Messages -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-400 rounded-lg flex items-center gap-3">
                    <span>{{ session('status') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-400 rounded-lg flex items-center gap-3">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Edit Form Card -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 space-y-6">
                    <form method="POST" action="{{ route('business.services.update', $service) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Service Name -->
                        <div>
                            <x-input-label for="name" :value="__('Service Name')" class="text-gray-900 dark:text-white" />
                            <x-text-input id="name" name="name" type="text" :value="old('name', $service->name)"
                                class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Duration -->
                        <div>
                            <x-input-label for="duration" :value="__('Duration (in minutes)')" class="text-gray-900 dark:text-white" />
                            <x-text-input id="duration" name="duration" type="number" :value="old('duration', $service->duration)"
                                class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required />
                            <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                        </div>

                        <!-- Price -->
                        <div>
                            <x-input-label for="price" :value="__('Price (RM)')" class="text-gray-900 dark:text-white" />
                            <x-text-input id="price" name="price" type="text" :value="old('price', $service->price)"
                                class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('business.services.index') }}" class="px-6 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 rounded-lg text-white text-sm font-medium transition-colors duration-200">
                                Update Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
