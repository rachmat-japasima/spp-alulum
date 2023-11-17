<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Pengguna') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Tambah Data Pengguna') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Tambah data pengguna untuk aplikasi SPP Al Ulum.") }}</h5>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="col-12">
                <div class="statistics-card">
                   
                        <form method="post" action="{{ route('user.store') }}">
                            @csrf
                    
                            <div class="mb-3 row">
                                <label for="Name" class="col-sm-2 col-form-label">{{__('Name')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" id="Name" value="{{old('name')}}" required autofocus autocomplete="name" placeholder="User Name">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('name')" />
                                </div>
                            </div>
                    
                            <div class="mb-3 row">
                                <label for="email" class="col-sm-2 col-form-label">{{__('Email')}}</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="email" id="email" value="{{old('email')}}" required autocomplete="email" placeholder="newEmail@example.com">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('email')" />
                                </div>
                            </div>
                    
                            <div class="mb-3 row">
                                <label for="password" class="col-sm-2 col-form-label">{{__('Password')}}</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="password" id="password" value="{{old('password')}}" required >
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('password')" />
                                </div>
                            </div>
                    
                            <div class="mb-3 row">
                                <label for="confirmPassword" class="col-sm-2 col-form-label">{{__('Confirm Password')}}</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="password_confirmation" id="confirmPassword" value="{{old('password_confirmation')}}" required >
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('password_confirmation')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="roles" class="col-sm-2 col-form-label">{{__('Hak Akses')}}</label>
                                <div class="col-sm-10">
                                    <select name="roles" id="roles" class="form-select" required>
                                        <option value="User" {{ old('roles') == "User" ? 'selected' : ''}}>User</option>
                                        <option value="Admin" {{ old('roles') == "Admin" ? 'selected' : ''}}>Admin</option>
                                        <option value="Super Admin" {{ old('roles') == "Super Admin" ? 'selected' : ''}}>Super Admin</option>
                                    </select>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('roles')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="remark" class="col-sm-2 col-form-label">{{__('Keterangan')}}</label>
                                <div class="col-sm-10">
                                    <textarea name="remark" id="remark" rows="3" class="form-control">{{old('remark')}}</textarea>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('remark')" />
                                </div>
                            </div>                    

                            <div class="mb-3 row">
                                <label for="role" class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <div class="form-check">
                                        <input class="form-check-input" name="email_verified" type="checkbox" value="verified" id="verified" checked>
                                        <label class="form-check-label" for="verified">
                                          Email Verified
                                        </label>
                                      </div>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('role')" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4 mt-3">
                                <button type="submit" class="btn btn-form">
                                    <img src="{{url('/assets/img/global/save.svg')}}" alt="">
                                    Simpan
                                </button>
                    
                            </div>
                        </form>
                </div>
            </div>
    
        </div>
    </div>

</x-app-layout>

