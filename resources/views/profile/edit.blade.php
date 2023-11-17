<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Profile Information') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Update your account's profile information and email address.") }}</h5>
            </div>
    
            <div class="col-12">
                <div class="statistics-card">
    
                    @include('profile.partials.update-profile-information-form')
    
                </div>
            </div>
    
        </div>
        <div class="row mt-3 mb-3">
            <div class="col-12">
                <h2 class="content-title">{{ __('Update Password') }}</h2>
                <h5 class="content-desc mb-4"> {{ __('Ensure your account is using a long, random password to stay secure.') }}</h5>
            </div>
    
            <div class="col-12">
                <div class="statistics-card">
    
                    @include('profile.partials.update-password-form')
                    
                </div>
            </div>
    
        </div>
    </div>
    {{-- delete own user / not use --}}
    {{-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div> --}}
</x-app-layout>
