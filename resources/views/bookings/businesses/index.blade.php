<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h2 class="font-semibold text-2xl text-gray-900 dark:text-white">Find a Business</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Discover and book services near you</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('bookings.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label for="query" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                                <input type="text" id="query" name="query" value="{{ $query ?? '' }}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="sort_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort By</label>
                                <select id="sort_by" name="sort_by" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                    <option value="name">Name</option>
                                    <option value="distance">Distance</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="mt-4 px-6 py-3 bg-primary-600 hover:bg-primary-700 rounded-lg text-white">Search</button>
                        <a href="{{ route('bookings.index') }}" class="mt-4 ml-4 inline-block px-6 py-3 bg-gray-500 hover:bg-gray-600 rounded-lg text-white" aria-label="Clear search and reset filters">Clear</a>
                    </form>
                </div>
            </div>
            @if($businesses->isEmpty())
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">No businesses available yet.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($businesses as $business)
                        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $business->name }}</h3>
                            @if($business->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($business->description, 100) }}</p>
                            @endif
                            <a href="{{ route('bookings.show', $business) }}" class="inline-flex px-5 py-2.5 bg-primary-600 hover:bg-primary-700 rounded-lg text-white">View Services</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
