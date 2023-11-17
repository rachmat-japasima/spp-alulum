<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Potongan') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Tabel Data Potongan') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Kelola data potongan dari aplikasi SPP Al Ulum.") }}</h5>
            </div>
        

            <div class="col-12">
                <div class="statistics-card">
                    <a href="{{ route('discount.create') }}" class="btn btn-primary btn-radius mb-3 me-auto">
                        <img src="{{url('/assets/img/global/plus-circle.svg')}}" width="20" alt="">
                        Tambah
                    </a>
                    <div class="table-responsive">
                        <table id="DiscountTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Besaran (%)</th>
                                    <th>Jenis Potongan</th>
                                    <th>Keterangan</th>
                                    <th>Penerima</th>
                                    <th>Status</th>
                                    <th width="15%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1; 
                                @endphp
                                @foreach ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->besaran  }}</td>
                                    <td>{{ $item->jenis  }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->discountStudent->count() }} orang</td>
                                    <td>
                                        @if ($item->status == 1)
                                            <span class="badge bg-info">Active</span>
                                        @else
                                            <span class="badge bg-warning">In-active</span>
                                        @endif    
                                    </td>
                                    <td>
                                        @if ($item->status == 1)
                                        <a href="{{ route('discount.inActive', $item->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Inactive"><img src="{{url('/assets/img/global/lock.svg')}}" width="20" alt="Inactive"></a>
                                        @else
                                        <a href="{{ route('discount.active', $item->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Aktifkan"><img src="{{url('/assets/img/global/check.svg')}}" width="20" alt="Aktifkan"></a>
                                        @endif
                                        
                                        <a href="{{ route('discount.edit', $item->id) }}" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><img src="{{url('/assets/img/global/edit.svg')}}" width="20" alt="Edit"> </a>
                                        <form action="{{ route('discount.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" onclick="return confirm('Apakah kamu yakin ingin menghapus ini ?');" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><img src="{{url('/assets/img/global/trash.svg')}}" width="20" alt="Hapus"></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Besaran (%)</th>
                                    <th>Jenis Potongan</th>
                                    <th>Keterangan</th>
                                    <th>Penerima</th>
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
                $('#DiscountTable').DataTable();
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        </script>
    @endpush

</x-app-layout>

