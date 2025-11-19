<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-900 dark:text-white">Manage Services</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Create and manage the services you offer</p>
                </div>
                <a href="{{ route('business.services.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Service
                </a>
            </div>
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
            @if($services->isEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400 font-medium text-lg mb-2">No services yet</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mb-6">Create your first service to start accepting bookings</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($services as $service)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ $service->name }}</h3>
                            <div class="space-y-2 mb-4">
                                <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-semibold">{{ $service->duration }}</span> minutes</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300">RM {{ $service->price ? number_format($service->price, 2) : 'N/A' }}</p>
                            </div>
                            <div class="flex justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <a 
                                    href="{{ route('business.services.edit', $service) }}" 
                                    class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 
                                        rounded-lg text-sm text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-600 
                                        transition-colors duration-200 text-center font-medium">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('business.services.destroy', $service) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        onclick="return confirm('Are you sure you want to delete this service?');"
                                        class="px-4 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 
                                            rounded-lg text-sm text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 
                                            transition-colors duration-200 font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
