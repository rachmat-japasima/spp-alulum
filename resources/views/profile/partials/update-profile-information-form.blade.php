<section>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Data <strong>Saved</strong>.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="mb-3 row">
            <label for="Name" class="col-sm-2 col-form-label">{{__('Name')}}</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="name" id="Name" value="{{old('name', $user->name)}}" required autofocus autocomplete="name" placeholder="Your new name">
                <x-input-error class="mt-2 text-danger" :messages="$errors->get('name')" />
            </div>
        </div>

        <div class="mb-3 row">
            <label for="email" class="col-sm-2 col-form-label">{{__('Email')}}</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" name="email" id="email" value="{{old('email', $user->email)}}" required autofocus autocomplete="username" placeholder="newEmail@example.com">
                <x-input-error class="mt-2 text-danger" :messages="$errors->get('email')" />
            </div>
        </div>

        <div>
            
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="btn btn-secondary">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-form">
                Save
            </button>

        </div>
    </form>
</section>
