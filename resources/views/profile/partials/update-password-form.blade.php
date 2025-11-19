<section class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700 p-6 mt-8">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ __('Update Password') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="current_password" :value="__('Current Password')" class="text-gray-700 dark:text-gray-300" />
            <x-text-input id="current_password" name="current_password" type="password"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg 
                       dark:bg-gray-700 dark:text-white"
                autocomplete="current-password" />
            <x-input-error class="mt-2 text-red-600 dark:text-red-400" :messages="$errors->get('current_password')" />
        </div>

        <div>
            <x-input-label for="password" :value="__('New Password')" class="text-gray-700 dark:text-gray-300" />
            <x-text-input id="password" name="password" type="password"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg 
                       dark:bg-gray-700 dark:text-white"
                autocomplete="new-password" />
            <x-input-error class="mt-2 text-red-600 dark:text-red-400" :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 dark:text-gray-300" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg 
                       dark:bg-gray-700 dark:text-white"
                autocomplete="new-password" />
            <x-input-error class="mt-2 text-red-600 dark:text-red-400" :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-primary-600 hover:bg-primary-700 text-white">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
