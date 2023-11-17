<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaksi Pembayaran') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Detail Transaksi Pembayaran') }} <span class="badge bg-pink w-auto">Status : {{ $data->status }}</span></h2>
                <h5 class="content-desc mb-4">{{ __("Detail transaksi pembayaran uang sekolah untuk aplikasi SPP Al Ulum.") }}</h5>
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
            <div class="col-12 mb-3">
                @if ($data->status == 'Success')
                    <a href="{{ route('transactions.print', $data->id) }}" class="btn btn-secondary btn-radius" target="_blank">
                        <img src="{{url('/assets/img/global/print.svg')}}" width="20" alt="">
                        Print Invoice
                    </a>
                    <form action="{{ route('transactions.update', $data->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('patch')
                        <button type="submit" onclick="return confirm('Apakah kamu yakin ingin menghapus ini ?');" class="btn btn-warning btn-radius ms-3" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                            <img src="{{url('/assets/img/global/x-circle.svg')}}" width="20" alt="">
                            Cancel Transaksi Pembayaran
                        </button>
                    </form>
                @endif
            </div>
            <div class="col-12">
                <div class="statistics-card card">
                    <h5 class="content-desc">Rincian Siswa</h5>
                            <div class="row">
                                <div class="col-12 col-md-7">
                                        <label for="nama" class="form-label">{{__('NIS / Nama Siswa')}}</label>
                                        <input type="text" class="form-control" name="nama" id="nama" value="{{ $student->nis }} / {{ $student->nama }}" disabled>
                                </div>
                                <div class="col-12 col-md-5">
                                        <label for="tahun_masuk" class="form-label">{{__('Tahun Masuk')}}</label>
                                        <div class="input-group mb-3">
                                            <input type="number" class="form-control" min="0" id="tahun_masuk" value="{{ $student->tahun_angkatan }}" disabled placeholder="0">
                                        </div>
                                </div>
                            </div>
                            @php
                                if ($student->tingkat == '0'){
                                    $tingkat = 'SD';
                                }elseif ($student->tingkat == '1') {
                                    $tingkat = 'SMP';
                                }elseif ($student->tingkat == '2') {
                                    $tingkat = 'SMA';
                                }elseif ($student->tingkat == 'RA') {
                                    $tingkat = 'RA';
                                }else {
                                    $tingkat = '';
                                }
                            @endphp
                            <div class="row">
                                <div class="col-12 col-md-7">
                                    <label for="tingkat" class="form-label">{{__('Tingkat')}}</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="tingkat" value="{{ $tingkat }}" disabled >
                                    </div>
                                </div>
                                <div class="col-12 col-md-5">
                                    <label for="kelas" class="form-label">{{__('Kelas / Grup')}}</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="kelas" value="{{ $data->kelas }} / {{ $data->grup }}" disabled >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-7">
                                    <label for="potongan_siswa" class="form-label">{{__('Potongan Siswa')}}</label>
                                    <select id="potongan_siswa" class="js-example-basic-multiple form-select" multiple="multiple" disabled>
                                        @foreach ($data->discounts as $item)
                                            <option value="{{ $item->discount->nama }}" selected>{{ $item->discount->nama }} ({{ $item->discount->besaran }}%)</option>
                                        @endforeach
                                    </select>
                                </div>
                                @php
                                    if ($student->kelas == 'RA'){
                                        $kelas = 'ra';
                                    }else {
                                        $kelas = 'kelas_'.$student->kelas;
                                    }
                                @endphp
                                <div class="col-12 col-md-5">
                                        <label for="uang_sekolah" class="form-label">{{__('Uang Sekolah')}}</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="tahun_masuk" value="@currency($schoolFeeAmount[$kelas])" disabled placeholder="0">
                                        </div>
                                </div>
                            </div>

                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="statistics-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-column justify-content-between align-items-start">
                                <h5 class="content-desc">No. Bukti ({{ $data->jenis }})</h5>

                                <h3 class="statistics-value">#{{ $data->no_bukti }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="statistics-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-column justify-content-between align-items-start">
                                <h5 class="content-desc">Total Transaksi</h5>

                                <h3 class="statistics-value">@currency($data->total)</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="statistics-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-column justify-content-between align-items-start">
                                <h5 class="content-desc">Tanggal Transaksi</h5>

                                <h3 class="statistics-value">
                                    {{ \Carbon\Carbon::parse($data->tgl_transaksi)->format('d M Y') }}
                                    {{-- <span class="fs-6 fw-light text-secondary">
                                        {{ \Carbon\Carbon::parse($item->tgl_transaksi)->format('H:i:s') }}
                                    </span> --}}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-3 border-info">
                       <div class="card-body">
                           <div class="d-flex align-items-center">
                               <div>
                                   <p class="mb-0 text-secondary">Uang Sekolah ({{ $data->jumlah_bulan }} bulan)</p>
                                   <h4 class="my-1 text-info">@currency($data->jumlah_us)</h4>
                                   <p class="mb-0 font-13">{{ $data->bulan }}</p>
                               </div>
                               <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto"><i class="fa fa-shopping-cart"></i>
                               </div>
                           </div>
                       </div>
                    </div>
                  </div>
                  <div class="col">
                   <div class="card radius-10 border-start border-0 border-3 border-danger">
                      <div class="card-body">
                          <div class="d-flex align-items-center">
                              <div>
                                  <p class="mb-0 text-secondary">Uang Pembangunan</p>
                                  <h4 class="my-1 text-danger">@currency($data->jumlah_up)</h4>
                                  <p class="mb-0 font-13">dari @currency($schoolFeeAmount['pembangunan_'.strtolower($tingkat)])</p>
                              </div>
                              <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto"><i class="fa fa-dollar"></i>
                              </div>
                          </div>
                      </div>
                   </div>
                 </div>
                 <div class="col">
                   <div class="card radius-10 border-start border-0 border-3 border-success">
                      <div class="card-body">
                          <div class="d-flex align-items-center">
                              <div>
                                  <p class="mb-0 text-secondary">Uang Lainnya</p>
                                  <h4 class="my-1 text-success">@currency($data->jumlah_lainnya)</h4>
                                  <p class="mb-0 font-13"> dari {{ $data->otherTransactions->count() }} pembayaran</p>
                              </div>
                              <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class="fa fa-bar-chart"></i>
                              </div>
                          </div>
                      </div>
                   </div>
                 </div>
                 <div class="col">
                   <div class="card radius-10 border-start border-0 border-3 border-warning">
                      <div class="card-body">
                          <div class="d-flex align-items-center">
                              <div>
                                <p class="mb-0 text-secondary">Jumlah Potongan</p>
                                <h4 class="my-1 text-warning">@currency($data->jumlah_potongan)</h4>
                                <p class="mb-0 font-13"> dari {{ $data->discounts->count() }} jenis potongan</p>
                              </div>
                              <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto"><i class="fa fa-users"></i>
                              </div>
                          </div>
                      </div>
                   </div>
                 </div> 
            </div>
            <div class="col-12 col-md-6">
                <div class="statistics-card card">
                    <h5 class="content-desc">
                        Rincian Transaksi Uang Sekolah
                    </h5>
                    <div class="table-responsive">
                        <table id="schoolFeeTable" class="table table-sm" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Bulan</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1; 
                                @endphp
                                @foreach ($data->schoolFee as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->bulan  }}</td>
                                    <td>
                                        @currency($item->total)
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="statistics-card card">
                    <h5 class="content-desc">
                        Rincian Transaksi Uang Pembangunan Sekolah
                    </h5>
                    <div class="table-responsive">
                        <table id="schoolDevFeeTable" class="table table-sm" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1; 
                                @endphp
                                @foreach ($data->schoolDevFee as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>@currency($item->total)</td>
                                    <td>{{ $item->keterangan  }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="statistics-card card">
                    <h5 class="content-desc">
                        Rincian Transaksi dengan Potongan
                    </h5>
                    <div class="table-responsive">
                        <table id="discountTransactionTable" class="table table-sm" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Jenis Potongan</th>
                                    <th>Besaran</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1; 
                                @endphp
                                @foreach ($data->discounts as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->discount->jenis == 'Uang Sekolah' ? 'US' : 'UP'  }}-{{ $item->discount->nama  }}</td>
                                    <td>{{ $item->discount->besaran  }}%</td>
                                    <td>@currency($item->total)</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="statistics-card card">
                    <h5 class="content-desc">
                        Rincian Transaksi Lainnya
                    </h5>
                    <div class="table-responsive">
                        <table id="otherTransactionTable" class="table table-sm" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1; 
                                @endphp
                                @foreach ($data->otherTransactions as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>@currency($item->total)</td>
                                    <td>{{ $item->keterangan  }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    
        </div>
    </div>

    @push('addon-styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
        <link rel="stylesheet" href="{{asset('css/dataTables/dataTables.bootstrap5.min.css')}}">
        <style>
            .bg-pink{
                background-color: #DF4598;
                color: white;
            }
        </style>
    @endpush


    @push('addon-scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                theme: 'bootstrap-5',
                placeholder: $( this ).data( 'placeholder' ),
            });

        }); 
    </script>
    @endpush

</x-app-layout>

