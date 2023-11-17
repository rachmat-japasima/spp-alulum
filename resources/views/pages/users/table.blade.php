<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Pengguna') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Tabel Data Pengguna') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Kelola data pengguna dari aplikasi raport merdeka.") }}</h5>
            </div>
        

            <div class="col-12">
                <div class="statistics-card">
                    <a href="{{ route('user.add') }}" class="btn btn-primary btn-radius mb-3 me-auto">
                        <img src="{{url('/assets/img/global/plus-circle.svg')}}" width="20" alt="">
                        Tambah</a>
                    <div class="table-responsive">
                        <table id="userTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Hak Akses</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1; 
                                @endphp
                                @foreach ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->roles }}</td>
                                    <td>{{ $item->remark }}</td>
                                    <td>@if ($item->status == "Active")
                                        <span class="badge bg-info">{{$item->status}}</span>
                                    @else
                                        <span class="badge bg-warning">{{$item->status}}</span>
                                    @endif</td>
                                    <td>
                                        @if ($item->status == "Active")
                                        <a href="{{ route('user.inActive', $item->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Blokir"><img src="{{url('/assets/img/global/lock.svg')}}" width="20" alt="Blokir"></a>
                                        @else
                                        <a href="{{ route('user.active', $item->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Aktifkan"><img src="{{url('/assets/img/global/check.svg')}}" width="20" alt="Aktifkan"></a>
                                        @endif
                                        
                                        <a href="{{ route('user.edit', $item->id) }}" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><img src="{{url('/assets/img/global/edit.svg')}}" width="20" alt="Edit"> </a>
                                        <a href="{{ route('user.delete', $item->id) }}" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><img src="{{url('/assets/img/global/trash.svg')}}" width="20" alt="Hapus"> </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Hak Akses</th>
                                    <th>Keterangan</th>
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
        <link rel="stylesheet" href="{{asset('css/dataTables/dataTables.bootstrap5.min.css')}}">
    @endpush


    @push('addon-scripts')
        <script src="{{ asset('js/dataTables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/dataTables/dataTables.bootstrap5.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                $('#userTable').DataTable();
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        </script>
    @endpush

</x-app-layout>

