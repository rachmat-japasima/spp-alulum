<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Siswa Aktif') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Tabel Data Pengguna') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Kelola data siswa dari aplikasi SPP Al Ulum.") }}</h5>
            </div>
        

            <div class="col-12">
                <div class="statistics-card">
                    <a href="{{ route('students.create') }}" class="btn btn-primary btn-radius mb-3 me-auto">
                        <img src="{{url('/assets/img/global/plus-circle.svg')}}" width="20" alt="">
                        Tambah
                    </a>
                    <a href="{{ route('students.oldTable') }}" class="btn btn-warning btn-radius mb-3 ms-2 me-auto">
                        <img src="{{url('/assets/img/global/table.svg')}}" width="20" alt="">
                        Siswa Lama
                    </a>
                    <div class="table-responsive">
                        <table id="StudentTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>NIS</th>
                                    <th>Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Kelas</th>
                                    <th>Tahun Angkatan</th>
                                    <th>SPP Terakhir</th>
                                    <th>Status</th>
                                    <th width="15%"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>NIS</th>
                                    <th>Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Kelas</th>
                                    <th>Tahun Angkatan</th>
                                    <th>SPP Terakhir</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
    
                </div>
            </div>
    
        </div>
    </div>

    @push('addon-styles')
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="stylesheet" href="{{asset('css/dataTables/dataTables.bootstrap5.min.css')}}">
    @endpush


    @push('addon-scripts')
        <script src="{{ asset('js/dataTables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/dataTables/dataTables.bootstrap5.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                $('#StudentTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url : "{{ route('students.getData') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "status" : 1,
                        }
                    },
                    columns: [
                        { data: "no" },
                        { data: "nis" },
                        { data: "nama" },
                        { data: "jenis_kelamin" },
                        { data: "kelas" },
                        { data: "tahun_angkatan" },
                        { data: "spp_terakhir" },
                        { data: "status" },
                        { data: "action" },
                    ]	 
                });
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        </script>
    @endpush

</x-app-layout>

