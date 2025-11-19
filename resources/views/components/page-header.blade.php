<div class="mb-8 flex items-center gap-3">
    @if (!empty($icon))
        <div class="p-2 rounded-lg bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
            {!! $icon !!}
        </div>
    @endif

    <div>
        <h2 class="font-semibold text-2xl text-gray-900 dark:text-white">
            {{ $title }}
        </h2>

        @if (!empty($description))
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $description }}
            </p>
        @endif
    </div>
</div>
