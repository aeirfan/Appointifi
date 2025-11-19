<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <x-page-header 
                title="Profile" 
                description="Manage your personal information, password, and account settings"
        
            />

            <div class="space-y-8">
                @include('profile.partials.update-profile-information-form')
                @include('profile.partials.update-password-form')
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-app-layout>
