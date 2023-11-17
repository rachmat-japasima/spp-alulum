<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaksi Pembayaran') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Proses Transaksi Pembayaran') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Proses transaksi pembayaran uang sekolah untuk aplikasi SPP Al Ulum.") }}</h5>
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
                   
                        <form method="post" action="{{ route('transactions.store') }}" id="selectForm">
                            @csrf
                    
                            <div class="mb-3 row">
                                <label for="id" class="col-sm-12 col-form-label mx-auto">
                                    <h3>{{__('Pilih Nama Siswa')}}</h3>
                                </label>
                                <div class="col-sm-12">
                                    <select name="id" id="id" class="form-select" required autofocus data-placeholder="Pilih Siswa...">
                                        <option></option>
                                        @foreach ($data as $item)
                                            <option value="{{ $item->id }}">{{ $item->nis }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('id')" />
                                </div>
                            </div>

                            {{-- <div class="flex items-center gap-4 mt-3">
                                <button type="submit" class="btn btn-form">
                                    Proses Transaksi
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-square"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                </button> --}}
                    
                            </div>
                        </form>
                </div>
            </div>
    
        </div>
    </div>

    @push('addon-styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
        <style>
            .form-select {
                background-color: #f8f8fc !important;
                border-radius: 8px !important;
                border: none !important;
                padding: 16px 20px !important;
                height: 150px !important;
                font-weight: 600 !important;
                height: auto !important;
            }

            .statistics-card{
                height: 65vh;
            }
        </style>
    @endpush


    @push('addon-scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $( '#id' ).select2( {
                    theme: 'bootstrap-5',
                    placeholder: $( this ).data( 'placeholder' ),
                    closeOnSelect: true,
                } );

                $('#id').change(function(){
                    $('#selectForm').submit();
                });
            }); 
        </script>
    @endpush

</x-app-layout>

