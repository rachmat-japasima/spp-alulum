<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Potongan') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Edit Data Potongan') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Edit data potongan untuk aplikasi SPP Al Ulum.") }}</h5>
            </div>

            <div class="col-12">
                <form method="post" action="{{ route('fees.update', $data->id) }}">
                    @csrf
                    @method('patch')

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="statistics-card">
                                <h5>Tahun Angkatan</h5>
                                <hr/>
                                <div class="mb-3 row">
                                    <label for="tahun_angkatan" class="col-sm-2 col-form-label">{{__('Tahun Angkatan')}}</label>
                                    <div class="col-sm-10">
                                        <select id="tahun_angkatan" class="form-select" disabled required>
                                            <option value="{{date('Y', strtotime('-3 year'))}}" {{ old('tahun_angkatan', $data->tahun_angkatan) == date('Y', strtotime('-3 year')) ? 'selected' : ''}}>{{date('Y', strtotime('-3 year'))}}</option>
                                            <option value="{{date('Y', strtotime('-2 year'))}}" {{ old('tahun_angkatan', $data->tahun_angkatan) == date('Y', strtotime('-2 year')) ? 'selected' : ''}}>{{date('Y', strtotime('-2 year'))}}</option>
                                            <option value="{{date('Y', strtotime('-1 year'))}}" {{ old('tahun_angkatan', $data->tahun_angkatan) == date('Y', strtotime('-1 year')) ? 'selected' : ''}}>{{date('Y', strtotime('-1 year'))}}</option>
                                            <option value="{{date('Y')}}" {{ old('tahun_angkatan', $data->tahun_angkatan) == date('Y') ? 'selected' : ''}}>{{date('Y')}}</option>
                                            <option value="{{date('Y', strtotime('1 year'))}}" {{ old('tahun_angkatan', $data->tahun_angkatan) == date('Y', strtotime('1 year')) ? 'selected' : ''}}>{{date('Y', strtotime('1 year'))}}</option>
                                        </select>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('tahun_angkatan')" />
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label for="pembangunan_ra" class="col-sm-2 col-form-label">{{__('Pembangunan RA')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="pembangunan_ra" id="pembangunan_ra" value="{{old('pembangunan_ra', $data->pembangunan_ra)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('pembangunan_ra')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="pembangunan_sd" class="col-sm-2 col-form-label">{{__('Pembangunan SD')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="pembangunan_sd" id="pembangunan_sd" value="{{old('pembangunan_sd', $data->pembangunan_sd)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('pembangunan_sd')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="pembangunan_smp" class="col-sm-2 col-form-label">{{__('Pembangunan SMP')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="pembangunan_smp" id="pembangunan_smp" value="{{old('pembangunan_smp', $data->pembangunan_smp)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('pembangunan_smp')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="pembangunan_sma" class="col-sm-2 col-form-label">{{__('Pembangunan SMA')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="pembangunan_sma" id="pembangunan_sma" value="{{old('pembangunan_sma', $data->pembangunan_sma)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('pembangunan_sma')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="seleksi_masuk" class="col-sm-2 col-form-label">{{__('Seleksi Masuk')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="seleksi_masuk" id="seleksi_masuk" value="{{old('seleksi_masuk', $data->seleksi_masuk)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('seleksi_masuk')" />
                                    </div>
                                </div>
    
                                <div class="flex items-center gap-4 mt-3">
                                    <button type="submit" class="btn btn-form">
                                        <img src="{{url('/assets/img/global/save.svg')}}" alt="">
                                        Simpan
                                    </button>
                        
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="statistics-card">
                                <h5>Tingkat RA dan SD</h5>
                                <hr/>
                                <div class="mb-3 row">
                                    <label for="ra" class="col-sm-2 col-form-label">{{__('RA')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="ra" id="ra" value="{{old('ra', $data->ra)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('ra')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="kelas_1" class="col-sm-2 col-form-label">{{__('Kelas 1')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="kelas_1" id="kelas_1" value="{{old('kelas_1', $data->kelas_1)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas_1')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="kelas_2" class="col-sm-2 col-form-label">{{__('Kelas 2')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="kelas_2" id="kelas_2" value="{{old('kelas_2', $data->kelas_2)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas_2')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="kelas_3" class="col-sm-2 col-form-label">{{__('Kelas 3')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="kelas_3" id="kelas_3" value="{{old('kelas_3', $data->kelas_3)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas_3')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="kelas_4" class="col-sm-2 col-form-label">{{__('Kelas 4')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="kelas_4" id="kelas_4" value="{{old('kelas_4', $data->kelas_4)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas_4')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="kelas_5" class="col-sm-2 col-form-label">{{__('Kelas 5')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="kelas_5" id="kelas_5" value="{{old('kelas_5', $data->kelas_5)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas_5')" />
                                    </div>
                                </div>
    
                                <div class="row">
                                    <label for="kelas_6" class="col-sm-2 col-form-label">{{__('Kelas 6')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="kelas_6" id="kelas_6" value="{{old('kelas_6', $data->kelas_6)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas_6')" />
                                    </div>
                                </div>
    
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="statistics-card">
                                <h5>Tingkat SMP</h5>
                                <hr/>
                                <div class="mb-3 row">
                                    <label for="kelas_7" class="col-sm-2 col-form-label">{{__('Kelas 7')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="kelas_7" id="kelas_7" value="{{old('kelas_7', $data->kelas_7)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas_7')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="kelas_8" class="col-sm-2 col-form-label">{{__('Kelas 8')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="kelas_8" id="kelas_8" value="{{old('kelas_8', $data->kelas_8)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas_8')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="kelas_9" class="col-sm-2 col-form-label">{{__('Kelas 9')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="kelas_9" id="kelas_9" value="{{old('kelas_9', $data->kelas_9)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas_9')" />
                                    </div>
                                </div>
    
                                <div class="flex items-center gap-4 mt-3">
                                    <button type="submit" class="btn btn-form">
                                        <img src="{{url('/assets/img/global/save.svg')}}" alt="">
                                        Simpan
                                    </button>
                        
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="statistics-card">
                                <h5>Tingkat SMA</h5>
                                <hr/>
                                <div class="mb-3 row">
                                    <label for="kelas_10" class="col-sm-2 col-form-label">{{__('Kelas 10')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="kelas_10" id="kelas_10" value="{{old('kelas_10', $data->kelas_10)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas_10')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="kelas_11" class="col-sm-2 col-form-label">{{__('Kelas 11')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="kelas_11" id="kelas_11" value="{{old('kelas_11', $data->kelas_11)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas_11')" />
                                    </div>
                                </div>
    
                                <div class="mb-3 row">
                                    <label for="kelas_12" class="col-sm-2 col-form-label">{{__('Kelas 12')}}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" min="0" name="kelas_12" id="kelas_12" value="{{old('kelas_12', $data->kelas_12)}}" required placeholder="0">
                                        </div>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas_12')" />
                                    </div>
                                </div>
    
                                <div class="flex items-center gap-4 mt-3">
                                    <button type="submit" class="btn btn-form">
                                        <img src="{{url('/assets/img/global/save.svg')}}" alt="">
                                        Simpan
                                    </button>
                        
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
    
        </div>
    </div>

    @push('addon-styles')

    @endpush


    @push('addon-scripts')
       
    @endpush

</x-app-layout>

