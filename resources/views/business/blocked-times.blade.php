<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Block Time Slots') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            <!-- Add New Blocked Time -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Block a New Time Slot</h3>
                    <form method="POST" action="{{ route('business.blocked-times.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="start_time" :value="__('Start Date & Time')" />
                                <x-text-input id="start_time" class="block mt-1 w-full" type="datetime-local" name="start_time" :value="old('start_time')" required />
                            </div>
                            <div>
                                <x-input-label for="end_time" :value="__('End Date & Time')" />
                                <x-text-input id="end_time" class="block mt-1 w-full" type="datetime-local" name="end_time" :value="old('end_time')" required />
                            </div>
                            <div>
                                <x-input-label for="reason" :value="__('Reason (Optional)')" />
                                <x-text-input id="reason" class="block mt-1 w-full" type="text" name="reason" :value="old('reason')" placeholder="e.g., Lunch break" />
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Block Time Slot') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Blocked Times -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Current Blocked Time Slots</h3>
                    
                    @if($blockedTimes->isEmpty())
                        <p class="text-gray-500 text-center py-8">No blocked time slots. Add one above to prevent bookings during specific times.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($blockedTimes as $blocked)
                                <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <p class="font-semibold text-gray-900">
                                                {{ $blocked->start_time->format('D, M j, Y') }}
                                            </p>
                                            @if($blocked->reason)
                                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                                    {{ $blocked->reason }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            🕒 {{ $blocked->start_time->format('g:i A') }} - {{ $blocked->end_time->format('g:i A') }}
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('business.blocked-times.delete', $blocked) }}" onsubmit="return confirm('Remove this blocked time slot?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
