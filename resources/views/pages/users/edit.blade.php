<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Pengguna') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Edit Data Pengguna') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Edit data pengguna untuk aplikasi SPP Al Ulum.") }}</h5>
            </div>
        

            <div class="col-12">
                <div class="statistics-card">
                   
                        <form method="post" action="{{ route('user.update', $data->id) }}">
                            @csrf
                            @method('patch')
                    
                            <div class="mb-3 row">
                                <label for="Name" class="col-sm-2 col-form-label">{{__('Name')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" id="Name" value="{{old('name', $data->name)}}" required autocomplete="name" placeholder="Your new name">
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>
                            </div>
                    
                            <div class="mb-3 row">
                                <label for="email" class="col-sm-2 col-form-label">{{__('Email')}}</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="email" id="email" value="{{old('email', $data->email)}}" required autocomplete="username" placeholder="newEmail@example.com">
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="role" class="col-sm-2 col-form-label">{{__('Hak Akses')}}</label>
                                <div class="col-sm-10">
                                    <select name="roles" id="roles" class="form-select" required>
                                        <option value="User" {{ old('roles', $data->roles) == "User" ? 'selected' : ''}}>User</option>
                                        <option value="Admin" {{ old('roles', $data->roles) == "Admin" ? 'selected' : ''}}>Admin</option>
                                        <option value="Super Admin" {{ old('roles', $data->roles) == "Super Admin" ? 'selected' : ''}}>Super Admin</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('roles')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="status" class="col-sm-2 col-form-label">{{__('Status')}}</label>
                                <div class="col-sm-10">
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="Active" {{ old('status', $data->status) == "Active" ? 'selected' : ''}}>Active</option>
                                        <option value="In-active" {{ old('status', $data->status) == "In-Active" ? 'selected' : ''}}>In-active</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
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

            <div class="col-12 mt-5">
                <h2 class="content-title">{{ __('Ubah Kata Sandi') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Ubah kata sandi pengguna aplikasi raport merdeka.") }}</h5>
            </div>

            <div class="col-12">
                <div class="statistics-card">
                   
                        <form method="post" action="{{ route('user.changePassword', $data->id) }}">
                            @csrf
                            @method('patch')
                    
                            <div class="mb-3 row">
                                <label for="password" class="col-sm-2 col-form-label">{{__('Password')}}</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="password" id="password" value="{{old('password')}}" required >
                                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                </div>
                            </div>
                    
                            <div class="mb-3 row">
                                <label for="confirmPassword" class="col-sm-2 col-form-label">{{__('Confirm Password')}}</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="password_confirmation" id="confirmPassword" value="{{old('password_confirmation')}}" required >
                                    <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
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

    @push('addon-styles')

    @endpush


    @push('addon-scripts')
       
    @endpush

</x-app-layout>

