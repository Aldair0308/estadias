<x-app-layout>
    <x-slot name="header">
        <h2 class="section-title">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    <div class="container py-6">
        <div class="feature-card space-y-6">
            <div class="p-6">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="p-6">
                @include('profile.partials.update-password-form')
            </div>

            {{-- <div class="p-6">
                @include('profile.partials.delete-user-form')
            </div> --}}
        </div>
    </div>
</x-app-layout>
