<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Tahun Ajaran') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Tabel Data Tahun Ajaran') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Kelola data tahun ajaran dari aplikasi SPP Al Ulum.") }}</h5>
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> 
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        

            <div class="col-12">
                <div class="statistics-card">
                    <button type="button" class="btn btn-primary btn-radius mb-3 me-auto" data-bs-toggle="modal" data-bs-target="#FormModal">
                        <img src="{{url('/assets/img/global/plus-circle.svg')}}" width="20" alt="">
                        Tambah
                    </button>
                    <div class="table-responsive">
                        <table id="FeeTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Tahun</th>
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
                                    <td>{{ $item->tahun_ajaran }}</td>
                                    <td>
                                        @if ($item->status == 1)
                                            <span class="badge bg-info">Active</span>
                                        @else
                                            <span class="badge bg-warning">In-active</span>
                                        @endif    
                                    </td>
                                    <td>
                                        @if ($item->status == 1)
                                        <a href="{{ route('schoolYears.inActive', $item->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Inactive"><img src="{{url('/assets/img/global/lock.svg')}}" width="20" alt="Inactive"></a>
                                        @else
                                        <a href="{{ route('schoolYears.active', $item->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Aktifkan"><img src="{{url('/assets/img/global/check.svg')}}" width="20" alt="Aktifkan"></a>
                                        @endif

                                        <a href="#" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#FormEditModal" data-bs-whatever="{{ $item->tahun_ajaran }}" data-bs-id="{{ $item->id }}"><img src="{{url('/assets/img/global/edit.svg')}}" width="20" alt="Edit"></a>
                                        {{-- <form action="{{ route('schoolYears.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" onclick="return confirm('Apakah kamu yakin ingin menghapus ini ?');" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><img src="{{url('/assets/img/global/trash.svg')}}" width="20" alt="Hapus"></button>
                                        </form> --}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>Tahun</th>
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

    <div class="modal fade" id="FormModal" tabindex="-1" aria-labelledby="FormModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Tambah Tahun Ajaran</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form method="POST" action="{{ route('schoolYears.store') }}">
                @csrf
                <div class="mb-3">
                  <label for="recipient-name" class="col-form-label">Tahun Ajaran:</label>
                  <select name="tahun_ajaran" id="tahun_ajaran" class="form-select" required>
                        <option value="{{date('Y', strtotime('-3 year'))}}/{{date('Y', strtotime('-2 year'))}}" {{ old('tahun_ajaran') == date('Y', strtotime('-3 year')).'/'.date('Y', strtotime('-2 year')) ? 'selected' : ''}}>{{date('Y', strtotime('-3 year'))}}/{{date('Y', strtotime('-2 year'))}}</option>
                        <option value="{{date('Y', strtotime('-2 year'))}}/{{date('Y', strtotime('-1 year'))}}" {{ old('tahun_ajaran') == date('Y', strtotime('-2 year')).'/'.date('Y', strtotime('-1 year')) ? 'selected' : ''}}>{{date('Y', strtotime('-2 year'))}}/{{date('Y', strtotime('-1 year'))}}</option>
                        <option value="{{date('Y', strtotime('-1 year'))}}/{{date('Y')}}" {{ old('tahun_ajaran') == date('Y', strtotime('-1 year')).'/'.date('Y') ? 'selected' : ''}}>{{date('Y', strtotime('-1 year'))}}/{{date('Y')}}</option>
                        <option value="{{date('Y')}}/{{date('Y', strtotime('1 year'))}}" {{ old('tahun_ajaran') == date('Y').'/'.date('Y', strtotime('1 year')) ? 'selected' : ''}}>{{date('Y')}}/{{date('Y', strtotime('1 year'))}}</option>
                        <option value="{{date('Y', strtotime('1 year'))}}/{{date('Y', strtotime('2 year'))}}" {{ old('tahun_ajaran') == date('Y', strtotime('1 year')).'/'.date('Y', strtotime('2 year')) ? 'selected' : ''}}>{{date('Y', strtotime('1 year'))}}/{{date('Y', strtotime('2 year'))}}</option>
                    </select>
                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('tahun_ajaran')" />
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              <button type="sumbit" class="btn btn-primary">Simpan</button>
            </form>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="FormEditModal" tabindex="-1" aria-labelledby="FormEditModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Tambah Tahun Ajaran</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form method="POST" action="{{ route('schoolYears.update', 0) }}">
                @csrf
                @method('patch')
                <div class="mb-3">
                  <label for="recipient-name" class="col-form-label">Tahun Ajaran:</label>
                  <input type="hidden" name="id" id="id" required>
                  <select name="tahun_ajaran" id="tahun_ajaran" class="form-select" required>
                        <option value="{{date('Y', strtotime('-3 year'))}}/{{date('Y', strtotime('-2 year'))}}" {{ old('tahun_ajaran') == date('Y', strtotime('-3 year')).'/'.date('Y', strtotime('-2 year')) ? 'selected' : ''}}>{{date('Y', strtotime('-3 year'))}}/{{date('Y', strtotime('-2 year'))}}</option>
                        <option value="{{date('Y', strtotime('-2 year'))}}/{{date('Y', strtotime('-1 year'))}}" {{ old('tahun_ajaran') == date('Y', strtotime('-2 year')).'/'.date('Y', strtotime('-1 year')) ? 'selected' : ''}}>{{date('Y', strtotime('-2 year'))}}/{{date('Y', strtotime('-1 year'))}}</option>
                        <option value="{{date('Y', strtotime('-1 year'))}}/{{date('Y')}}" {{ old('tahun_ajaran') == date('Y', strtotime('-1 year')).'/'.date('Y') ? 'selected' : ''}}>{{date('Y', strtotime('-1 year'))}}/{{date('Y')}}</option>
                        <option value="{{date('Y')}}/{{date('Y', strtotime('1 year'))}}" {{ old('tahun_ajaran') == date('Y').'/'.date('Y', strtotime('1 year')) ? 'selected' : ''}}>{{date('Y')}}/{{date('Y', strtotime('1 year'))}}</option>
                        <option value="{{date('Y', strtotime('1 year'))}}/{{date('Y', strtotime('2 year'))}}" {{ old('tahun_ajaran') == date('Y', strtotime('1 year')).'/'.date('Y', strtotime('2 year')) ? 'selected' : ''}}>{{date('Y', strtotime('1 year'))}}/{{date('Y', strtotime('2 year'))}}</option>
                    </select>
                    <x-input-error class="mt-2 text-danger" :messages="$errors->get('tahun_ajaran')" />
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              <button type="sumbit" class="btn btn-primary">Simpan</button>
            </form>
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

            var FormEditModal = document.getElementById('FormEditModal')
            FormEditModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget
                var id = button.getAttribute('data-bs-id')
                var data = button.getAttribute('data-bs-whatever')

                // Update the modal's content.
                var modalSelect = FormEditModal.querySelector('#tahun_ajaran')
                var modalInput = FormEditModal.querySelector('#id')

                const option = new Option(data, data)

                modalSelect.add(option,data);
                modalSelect.value = data

                modalInput.value = id
            })
        </script>
    @endpush

</x-app-layout>

