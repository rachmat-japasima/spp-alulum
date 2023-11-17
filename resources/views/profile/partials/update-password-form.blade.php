<section>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        @if (session('status') === 'password-updated')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Data <strong>Saved</strong>.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="mb-3 row">
            <label for="Name" class="col-sm-2 col-form-label">{{__('Current Password')}}</label>
            <div class="col-sm-10">
                <input class="form-control" id="current_password" name="current_password" type="password" autocomplete="current-password"  required/>
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-danger" />
            </div>
        </div>

        <div class="mb-3 row">
            <label for="Name" class="col-sm-2 col-form-label">{{__('New Password')}}</label>
            <div class="col-sm-10">
                <input class="form-control" id="password" name="password" type="password" autocomplete="new-password"  required/>
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-danger" />
            </div>
        </div>

        <div class="mb-3 row">
            <label for="Name" class="col-sm-2 col-form-label">{{__('Confirm Password')}}</label>
            <div class="col-sm-10">
                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"  required/>
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-danger" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-form">
                Save
            </button>
        </div>
    </form>
</section>
