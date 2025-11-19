<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-900 dark:text-white">Add New Service</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Create a new service for your business</p>
                </div>
                <a href="{{ route('business.services.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-secondary-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-sm text-gray-900 dark:text-white shadow-sm transition-colors duration-200">
                    Back to Manage Services
                </a>
            </div>

            <!-- Create Form Card -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <form method="POST" action="{{ route('business.services.store') }}" class="space-y-6">
                        @csrf

                        <!-- Service Name -->
                        <div>
                            <x-input-label for="name" :value="__('Service Name')" class="text-gray-900 dark:text-white" />
                            <x-text-input id="name" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" type="text" name="name" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Duration -->
                        <div>
                            <x-input-label for="duration" :value="__('Duration (in minutes)')" class="text-gray-900 dark:text-white" />
                            <x-text-input id="duration" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" type="number" name="duration" :value="old('duration')" required />
                            <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                        </div>

                        <!-- Price -->  
                        <div>
                            <x-input-label for="price" :value="__('Price (RM)')" class="text-gray-900 dark:text-white" />
                            <x-text-input id="price" class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" type="text" name="price" :value="old('price')" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>
    
                        <!-- Form Actions -->
                        <div>
                        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('business.services.index') }}" class="px-6 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">Cancel</a>
                            <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 rounded-lg text-white text-sm font-medium transition-colors duration-200">Save Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>