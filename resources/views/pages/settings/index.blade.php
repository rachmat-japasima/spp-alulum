<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Pengaturan Sistem') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Kelola pengaturan dari aplikasi SPP Al Ulum.") }}</h5>
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
                <div class="statistics-card card">
                  <h5 class="content-desc">
                      Pesan Notifikasi
                  </h5>
                  <form method="post" action="{{ route('settings.store') }}">
                    @csrf
                    <input type="hidden" name="id" value="1" required>
                    <div class="mb-3 row">
                        <label for="Name" class="col-sm-2 col-form-label">{{__('Pesan Utama')}}</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="5" style="height: 200px;" disabled>
Halo {Nama Siswa},
Selamat! Pembayaran uang sekolah Anda telah berhasil diproses.

Nama Siswa: {Nama Siswa}
Nomor Induk Siswa: {NIS}
Kelas: {Kelas / Grup}
Uang Sekolah : {Jumlah Uang Sekolah}
Uang Pembangunan: {Jumlah Uang Pembangunan}
Uang Lainnya: {Jumlah Uang Lainnya}
Potongan: {Jumlah Lainnya}
*Jumlah Pembayaran: {Jumlah Potongan}*
Tanggal Pembayaran: {Tanggal Transaksi}'

                            </textarea>
                            <x-input-error class="mt-2 text-danger" :messages="$errors->get('pesan_tambahan')" />
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                      <label for="Name" class="col-sm-2 col-form-label">{{__('Pesan Tambahan')}}</label>
                      <div class="col-sm-10">
                          {{-- <textarea name="pesan_tambahan" id="editor"> --}}
                          <textarea class="form-control" name="pesan_tambahan" required style="height: 300px;">
                            {{ $data->pesan_tambahan }}
                          </textarea>
                          <x-input-error class="mt-2 text-danger" :messages="$errors->get('pesan_tambahan')" />
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
        <script>
            $(document).ready(function () {
                
            });
            
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        </script>
    @endpush

</x-app-layout>

