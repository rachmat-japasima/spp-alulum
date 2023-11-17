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
                <div class="statistics-card">
                   
                        <form method="post" action="{{ route('discount.update', $data->id) }}">
                            @csrf
                            @method('patch')
                    
                            <div class="mb-3 row">
                                <label for="nama" class="col-sm-2 col-form-label">{{__('Nama')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nama" id="nama" value="{{old('nama', $data->nama)}}" required placeholder="Nama Potongan">
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('nama')" />
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label for="besaran" class="col-sm-2 col-form-label">{{__('Besaran (%)')}}</label>
                                <div class="col-sm-10">
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" min="0" name="besaran" id="besaran" value="{{old('besaran', $data->besaran)}}" required placeholder="0">
                                        <span class="input-group-text">%</span>
                                      </div>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('besaran')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="jenis" class="col-sm-2 col-form-label">{{__('Jenis Potongan')}}</label>
                                <div class="col-sm-10">
                                    <select name="jenis" id="jenis" class="form-select" required>
                                        <option value="Uang Sekolah" {{ old('jenis', $data->jenis) == 'Uang Sekolah' ? 'selected' : ''}}>Uang Sekolah</option>
                                        <option value="Uang Pembangunan" {{ old('jenis', $data->jenis) == 'Uang Pembangunan' ? 'selected' : ''}}>Uang Pembangunan</option>
                                    </select>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('jenis')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="keterangan" class="col-sm-2 col-form-label">{{__('Keterangan')}}</label>
                                <div class="col-sm-10">
                                    <textarea name="keterangan" id="keterangan" rows="3" style="height: 70px" class="form-control" required>{{old('keterangan', $data->keterangan)}}</textarea>
                                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('keterangan')" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="status" class="col-sm-2 col-form-label">{{__('Status')}}</label>
                                <div class="col-sm-10">
                                    <select name="status" id="status" class="form-select" required>
                                        <option value=1 {{ old('status', $data->status) == 1 ? 'selected' : ''}}>Aktif</option>
                                        <option value=0 {{ old('status', $data->status) == 0 ? 'selected' : ''}}>Tidak Aktif</option>
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
            <div class="col-12">
                <div class="statistics-card">
                    <h5 class="content-desc">Rincian Siswa Penerima Potongan</h5>
                    <button type="button" class="col-sm-2 btn btn-form me-auto" data-bs-toggle="modal" data-bs-target="#formModal">
                        <img src="{{url('/assets/img/global/plus-circle.svg')}}" width="20" alt="">
                        Tambah
                    </button>
                        <table id="studentTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Siswa</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1; 
                                @endphp
                                @foreach ($discountStudent as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->student->nama  }}</td>
                                    <td>{{ $item->keterangan  }}</td>
                                    <td>{{ $item->status  }}</td>
                                    <td>
                                        <a href="{{ route('discount.removeStudent', $item->id) }}" onclick="return confirm('Apakah kamu yakin ingin menghapus ini ?');" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><img src="{{url('/assets/img/global/trash.svg')}}" width="20" alt="Hapus"></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Siswa</th>
                                    <th>Keterangan</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                </div>
            </div>
    
        </div>
    </div>

    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Tambah Siswa ke Potongan</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('discount.addStudent') }}" class="mb-3">
                    @csrf
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">Nama Siswa:</label>
                        <input type="hidden" name="id_potongan" required value="{{$data->id}}">
                        <input type="hidden" name="status" required value="{{  Auth::user()->roles == 'Super Admin' ? 'Active' : 'Active' }}">
                        <select name="id_siswa" id="id_siswa" class="form-select" required autofocus data-placeholder="Pilih Siswa...">
                            <option></option>
                            @foreach ($students as $item)
                                <option value="{{ $item->id }}">{{ $item->nis }} - {{ $item->nama }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('id_siswa')" />
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="col-form-label">Keterangan:</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" style="height: 100px;"></textarea>
                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('keterangan')" />
                    </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-form">
                <img src="{{url('/assets/img/global/plus-circle.svg')}}" width="20" alt="">
                Tambah
            </button>
         </form>
            </div>
          </div>
        </div>
      </div>

    @push('addon-styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
        <link rel="stylesheet" href="{{asset('css/dataTables/dataTables.bootstrap5.min.css')}}">
    @endpush


    @push('addon-scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{ asset('js/dataTables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/dataTables/dataTables.bootstrap5.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                $( '#id_siswa' ).select2( {
                    theme: 'bootstrap-5',
                    placeholid_siswaer: $( this ).data( 'placeholder' ),
                    closeOnSelect: true,
                    dropdownParent: $('#formModal')
                } );

                $('#studentTable').DataTable();

                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }); 
        </script>
    @endpush

</x-app-layout>

