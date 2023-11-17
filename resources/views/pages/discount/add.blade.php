<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Potongan') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Tambah Data Potongan') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Tambah data potongan untuk aplikasi SPP Al Ulum.") }}</h5>
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
                   
                   
                        <form method="post" action="{{ route('discount.store') }}">
                            @csrf
                            
                            <div class="mb-3 row">
                                <label for="nama" class="col-sm-2 col-form-label">{{__('Nama')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nama" id="nama" value="{{old('nama')}}" required placeholder="Nama Potongan">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('nama')" />
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label for="besaran" class="col-sm-2 col-form-label">{{__('Besaran (%)')}}</label>
                                <div class="col-sm-10">
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" min="0" name="besaran" id="besaran" value="{{old('besaran')}}" required placeholder="0">
                                        <span class="input-group-text">%</span>
                                      </div>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('besaran')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="jenis" class="col-sm-2 col-form-label">{{__('Jenis Potongan')}}</label>
                                <div class="col-sm-10">
                                    <select name="jenis" id="jenis" class="form-select" required>
                                        <option value="Uang Sekolah" {{ old('jenis') == 'Uang Sekolah' ? 'selected' : ''}}>Uang Sekolah</option>
                                        <option value="Uang Pembangunan" {{ old('jenis') == 'Uang Pembangunan' ? 'selected' : ''}}>Uang Pembangunan</option>
                                    </select>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('jenis')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="keterangan" class="col-sm-2 col-form-label">{{__('Keterangan')}}</label>
                                <div class="col-sm-10">
                                    <textarea name="keterangan" id="keterangan" rows="3" style="height: 70px" class="form-control" required>{{old('keterangan')}}</textarea>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('keterangan')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="status" class="col-sm-2 col-form-label">{{__('Status')}}</label>
                                <div class="col-sm-10">
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="1" {{ old('status') == 1 ? 'selected' : ''}}>Aktif</option>
                                        <option value="0" {{ old('status') == 0 ? 'selected' : ''}}>Tidak Aktif</option>
                                    </select>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('status')" />
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

