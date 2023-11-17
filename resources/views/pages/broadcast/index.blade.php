<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pesan Broadcast') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Pesan Broadcast') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Kirim pesan ke banyak orang melalui aplikasi SPP Al Ulum.") }}</h5>
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
                      Pesan Broadcast 
                  </h5>
                  <form method="post" action="{{ route('broadcast.all') }}">
                    @csrf
                    <input type="hidden" name="id" value="1" required>
                    <div class="mb-3 row">
                        <label for="Name" class="col-sm-2 col-form-label">{{__('Tujuan Pesan')}}</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="tingkat" id="tingkat" required>
                                <option value="full" {{ old('tingkat') == 'full' ? 'selected' : '' }}>Seluruh Siswa</option>
                                <option value="RA" {{ old('tingkat') == 'RA' ? 'selected' : '' }}>Tingkat RA</option>
                                <option value="0" {{ old('tingkat') == '0' ? 'selected' : '' }}>Tingkat SD</option>
                                <option value="1" {{ old('tingkat') == '1' ? 'selected' : '' }}>Tingkat SMP</option>
                                <option value="2" {{ old('tingkat') == '2' ? 'selected' : '' }}>Tingkat SMA</option>
                              </select>
                            <x-input-error class="mt-2 text-danger" :messages="$errors->get('pesan_tambahan')" />
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                      <label for="Name" class="col-sm-2 col-form-label">{{__('Pesan Tambahan')}}</label>
                      <div class="col-sm-10">
                          {{-- <textarea name="pesan_tambahan" id="editor"> --}}
                          <textarea class="form-control" name="pesan_tambahan" required style="height: 300px;" required></textarea>
                          <x-input-error class="mt-2 text-danger" :messages="$errors->get('pesan_tambahan')" />
                      </div>
                  </div>

                    <div class="flex items-center gap-4 mt-3">
                        <button type="submit" class="btn btn-form">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                            Kirim Pesan
                        </button>
            
                    </div>
                </form>
                </div>
            </div>

            <div class="col-12">
                <div class="statistics-card card">
                  <h5 class="content-desc">
                      Pesan Broadcast Spesifik Nomor
                  </h5>
                  <form method="post" action="{{ route('broadcast.store') }}">
                    @csrf
                    <input type="hidden" name="id" value="1" required>
                    <div class="mb-3 row">
                        <label for="Name" class="col-sm-2 col-form-label">{{__('Tujuan Pesan')}}</label>
                        <div class="col-sm-10">
                            <select name="no_telp[]" id="id" class="form-select" required autofocus data-placeholder="Pilih Siswa..." multiple="multiple">
                                <option></option>
                                @foreach ($data as $item)
                                    @if ($item->telp_ortu != null && $item->telp_ortu != '' && strlen($item->telp_ortu) > 0 && strlen($item->telp_ortu) < 14)
                                        <option value="{{ $item->telp_ortu }}">{{ $item->telp_ortu }} - {{ $item->nama }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <x-input-error class="mt-2 text-danger" :messages="$errors->get('pesan_tambahan')" />
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                      <label for="Name" class="col-sm-2 col-form-label">{{__('Pesan Tambahan')}}</label>
                      <div class="col-sm-10">
                          <textarea class="form-control" name="pesan" required style="height: 300px;" required></textarea>
                          <x-input-error class="mt-2 text-danger" :messages="$errors->get('pesan_tambahan')" />
                      </div>
                  </div>

                    <div class="flex items-center gap-4 mt-3">
                        <button type="submit" class="btn btn-form">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                            Kirim Pesan
                        </button>
            
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    @push('addon-styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @endpush


    @push('addon-scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
           $(document).ready(function() {
                $( '#id' ).select2( {
                    theme: 'bootstrap-5',
                    placeholder: $( this ).data( 'placeholder' ),
                    closeOnSelect: false,
                } );

                $('#id').change(function(){
                    $('#selectForm').submit();
                });
            }); 
            
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        </script>
    @endpush

</x-app-layout>

