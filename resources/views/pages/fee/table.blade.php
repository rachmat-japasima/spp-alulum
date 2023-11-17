<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Biaya') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Tabel Data Biaya') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Kelola data biaya dari aplikasi SPP Al Ulum.") }}</h5>
            </div>
        

            <div class="col-12">
                <div class="statistics-card">
                    <a href="{{ route('fees.create') }}" class="btn btn-primary btn-radius mb-3 me-auto">
                        <img src="{{url('/assets/img/global/plus-circle.svg')}}" width="20" alt="">
                        Tambah
                    </a>
                    <div class="table-responsive">
                        <table id="FeeTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Tahun</th>
                                    <th>RA</th>
                                    <th>Kelas 1</th>
                                    <th>Kelas 7</th>
                                    <th>Kelas 10</th>
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
                                    <td>{{ $item->tahun_angkatan }}</td>
                                    <td>@currency($item->ra)</td>
                                    <td>@currency($item->kelas_1)</td>
                                    <td>@currency($item->kelas_7)</td>
                                    <td>@currency($item->kelas_10)</td>
                                    <td>
                                        <a href="{{ route('fees.edit', $item->id) }}" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><img src="{{url('/assets/img/global/edit.svg')}}" width="20" alt="Edit"> </a>
                                        <form action="{{ route('fees.destroy', $item->id) }}" method="POST" class="d-inline">
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
                                    <th>Tahun</th>
                                    <th>RA</th>
                                    <th>Kelas 1</th>
                                    <th>Kelas 7</th>
                                    <th>Kelas 10</th>
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
                $('#FeeTable').DataTable();
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        </script>
    @endpush

</x-app-layout>

