<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Siswa') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Tambah Data Siswa') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Tambah data siswa untuk aplikasi SPP Al Ulum.") }}</h5>
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
                   
                   
                        <form method="post" action="{{ route('students.store') }}">
                            @csrf
                            
                            <div class="mb-3 row">
                                <label for="nis" class="col-sm-2 col-form-label">{{__('NIS')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nis" id="nis" value="{{old('nis')}}" required autofocus placeholder="Nomor Induk Siswa">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('nis')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="nama" class="col-sm-2 col-form-label">{{__('Nama')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nama" id="nama" value="{{old('nama')}}" required placeholder="Nama Lengkap">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('nama')" />
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label for="tempat_lahir" class="col-sm-2 col-form-label">{{__('Tempat Lahir')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" value="{{old('tempat_lahir')}}" required placeholder="Kota Lahir">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('tempat_lahir')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="tgl_lahir" class="col-sm-2 col-form-label">{{__('Tanggal Lahir')}}</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir" value="{{old('tgl_lahir')}}" required>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('tgl_lahir')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="jenis_kelamin" class="col-sm-2 col-form-label">{{__('Jenis Kelamin')}}</label>
                                <div class="col-sm-10">
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                                        <option value="0" {{ old('jenis_kelamin') == "0" ? 'selected' : ''}}>Laki-laki</option>
                                        <option value="1" {{ old('jenis_kelamin') == "1" ? 'selected' : ''}}>Perempuan</option>
                                    </select>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('jenis_kelamin')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="agama" class="col-sm-2 col-form-label">{{__('Agama')}}</label>
                                <div class="col-sm-10">
                                    <select name="agama" id="agama" class="form-select" required>
                                        <option value="0" {{ old('agama') == "0" ? 'selected' : ''}}>Islam</option>
                                    </select>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('agama')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="alamat" class="col-sm-2 col-form-label">{{__('Alamat')}}</label>
                                <div class="col-sm-10">
                                    <textarea name="alamat" id="alamat" rows="3" style="height: 70px" class="form-control" required>{{old('alamat')}}</textarea>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('alamat')" />
                                </div>
                            </div> 

                            <div class="mb-3 row">
                                <label for="nama_ortu" class="col-sm-2 col-form-label">{{__('Nama Orang Tua')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nama_ortu" id="nama_ortu" value="{{old('nama_ortu')}}" required placeholder="Nama Orang Tua Siswa">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('nama_ortu')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="telp_ortu" class="col-sm-2 col-form-label">{{__('No. Telp Orang Tua')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="telp_ortu" id="telp_ortu" value="{{old('telp_ortu')}}" required placeholder="Nomor Telepon Orang Tua Siswa">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('telp_ortu')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="pekerjaan_ortu" class="col-sm-2 col-form-label">{{__('Pekerjaan Orang Tua')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="pekerjaan_ortu" id="pekerjaan_ortu" value="{{old('pekerjaan_ortu')}}" required placeholder="Pekerjaan Orang Tua Siswa">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('pekerjaan_ortu')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="tingkat" class="col-sm-2 col-form-label">{{__('Tingkat')}}</label>
                                <div class="col-sm-10">
                                    <select name="tingkat" id="tingkat" class="form-select" required>
                                        <option value="RA" {{ old('tingkat') == "RA" ? 'selected' : ''}}>RA</option>
                                        <option value="0" {{ old('tingkat') == "0" ? 'selected' : ''}}>SD</option>
                                        <option value="1" {{ old('tingkat') == "1" ? 'selected' : ''}}>SMP</option>
                                        <option value="2" {{ old('tingkat') == "2" ? 'selected' : ''}}>SMA</option>
                                    </select>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('tingkat')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="tahun_angkatan" class="col-sm-2 col-form-label">{{__('Tahun Angkatan')}}</label>
                                <div class="col-sm-10">
                                    <select name="tahun_angkatan" id="tahun_angkatan" class="form-select" required>
                                        <option value="{{date('Y', strtotime('-3 year'))}}" {{ old('tahun_angkatan') == date('Y', strtotime('-3 year')) ? 'selected' : ''}}>{{date('Y', strtotime('-3 year'))}}</option>
                                        <option value="{{date('Y', strtotime('-2 year'))}}" {{ old('tahun_angkatan') == date('Y', strtotime('-2 year')) ? 'selected' : ''}}>{{date('Y', strtotime('-2 year'))}}</option>
                                        <option value="{{date('Y', strtotime('-1 year'))}}" {{ old('tahun_angkatan') == date('Y', strtotime('-1 year')) ? 'selected' : ''}}>{{date('Y', strtotime('-1 year'))}}</option>
                                        <option value="{{date('Y')}}" {{ old('tahun_angkatan') == date('Y') ? 'selected' : ''}}>{{date('Y')}}</option>
                                        <option value="{{date('Y', strtotime('1 year'))}}" {{ old('tahun_angkatan') == date('Y', strtotime('1 year')) ? 'selected' : ''}}>{{date('Y', strtotime('1 year'))}}</option>
                                    </select>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('tahun_angkatan')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="kelas" class="col-sm-2 col-form-label">{{__('kelas')}}</label>
                                <div class="col-sm-10">
                                    <select name="kelas" id="kelas" class="form-select" required>
                                        <option value="RA" {{ old('kelas') == "RA" ? 'selected' : ''}}>RA</option>
                                        <option value="1" {{ old('kelas') == "1" ? 'selected' : ''}}>1</option>
                                        <option value="2" {{ old('kelas') == "2" ? 'selected' : ''}}>2</option>
                                        <option value="3" {{ old('kelas') == "3" ? 'selected' : ''}}>3</option>
                                        <option value="4" {{ old('kelas') == "4" ? 'selected' : ''}}>4</option>
                                        <option value="5" {{ old('kelas') == "5" ? 'selected' : ''}}>5</option>
                                        <option value="6" {{ old('kelas') == "6" ? 'selected' : ''}}>6</option>
                                        <option value="7" {{ old('kelas') == "7" ? 'selected' : ''}}>7</option>
                                        <option value="8" {{ old('kelas') == "8" ? 'selected' : ''}}>8</option>
                                        <option value="9" {{ old('kelas') == "9" ? 'selected' : ''}}>9</option>
                                        <option value="10" {{ old('kelas') == "10" ? 'selected' : ''}}>10</option>
                                        <option value="11" {{ old('kelas') == "11" ? 'selected' : ''}}>11</option>
                                        <option value="12" {{ old('kelas') == "12" ? 'selected' : ''}}>12</option>
                                    </select>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('kelas')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="grup" class="col-sm-2 col-form-label">{{__('Grup Kelas')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="grup" id="grup" value="{{old('grup')}}" required placeholder="A">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('grup')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="smasuk" class="col-sm-2 col-form-label">{{__('Status Masuk')}}</label>
                                <div class="col-sm-10">
                                    <select name="smasuk" id="smasuk" class="form-select" required>
                                        <option value="0" {{ old('smasuk') == "0" ? 'selected' : ''}}>Reguler</option>
                                        <option value="1" {{ old('smasuk') == "1" ? 'selected' : ''}}>Pindahan</option>
                                    </select>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('smasuk')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="status" class="col-sm-2 col-form-label">{{__('Status')}}</label>
                                <div class="col-sm-10">
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="1" {{ old('status') == "1" ? 'selected' : ''}}>Aktif</option>
                                        <option value="0" {{ old('status') == "0" ? 'selected' : ''}}>Tidak Aktif</option>
                                    </select>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('status')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="asal_kelas" class="col-sm-2 col-form-label">{{__('Asal Kelas')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="asal_kelas" id="asal_kelas" value="{{old('asal_kelas')}}" placeholder="Asal Kelas Sebelumnya">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('asal_kelas')" />
                                </div>
                            </div>           

                            <div class="mb-3 row">
                                <label for="asal_sekolah" class="col-sm-2 col-form-label">{{__('Asal Sekolah')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="asal_sekolah" id="asal_sekolah" value="{{old('asal_sekolah')}}" placeholder="Asal Sekolah Sebelumnya">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('asal_sekolah')" />
                                </div>
                            </div>      
                            
                            <div class="mb-3 row">
                                <label for="tmt_masuk" class="col-sm-2 col-form-label">{{__('Tanggal Masuk')}}</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" name="tmt_masuk" id="tmt_masuk" value="{{old('tmt_masuk')}}" required>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('tmt_masuk')" />
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

