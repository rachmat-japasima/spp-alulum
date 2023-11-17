<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaksi Pembayaran') }}
        </h2>
    </x-slot>
    @php
        $tunggakan = array();
    @endphp

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Detail Transaksi Pembayaran') }}</h2>
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
            @if (Auth::user()->roles != 'User')
                <div class="col-12">
                    <a href="{{ route('discount.create') }}" class="btn btn-pink btn-radius mb-3"  data-bs-toggle="modal" data-bs-target="#transactionModal" data-bs-whatever="@getbootstrap">
                        <img src="{{url('/assets/img/global/plus-circle.svg')}}" width="20" alt="">
                        Pembayaran Uang Sekolah
                    </a>
                </div>
            @endif
            <div class="col-12">
                <div class="statistics-card card">
                    <h5 class="content-desc">Rincian Siswa</h5>
                            <div class="row">
                                <div class="col-12 col-md-7">
                                    <form method="post" action="{{ route('transactions.store') }}" id="selectForm">
                                        @csrf
                                        <label for="id" class="form-label">{{__('NIS / Nama Siswa')}}</label>
                                        <select name="id" id="id" class="form-select" required autofocus data-placeholder="Pilih Siswa...">
                                            <option></option>
                                              @foreach ($list_siswa as $item)
                                                  <option value="{{ $item->id }}" {{ $data->id == $item->id ? 'selected' : '' }}>{{ $item->nis }} - {{ $item->nama }}</option>
                                              @endforeach
                                        </select>
                                        <x-input-error class="mt-2 text-danger" :messages="$errors->get('id')" />
                                    </form>
                                </div>
                                <div class="col-12 col-md-5">
                                        <label for="tahun_masuk" class="form-label">{{__('Tahun Masuk')}}</label>
                                        <div class="input-group mb-3">
                                            <input type="number" class="form-control" min="0" id="tahun_masuk" value="{{ $data->tahun_angkatan }}" disabled placeholder="0">
                                        </div>
                                </div>
                            </div>
                            @php
                                if ($data->tingkat == '0'){
                                    $tingkat = 'SD';
                                }elseif ($data->tingkat == '1') {
                                    $tingkat = 'SMP';
                                }elseif ($data->tingkat == '2') {
                                    $tingkat = 'SMA';
                                }elseif ($data->tingkat == 'RA') {
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
                                        @foreach ($discountStudent as $item)
                                            @if ($item->discount->status == 1 && $item->status == 'Active')
                                                <option value="{{ $item->discount->nama }}" selected>{{ $item->jenis == "Uang Sekolah" ? "US" : "UP" }}-{{ $item->discount->nama }} ({{ $item->discount->besaran }}%)</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                @php
                                    if ($data->kelas == 'RA'){
                                        $kelas = 'ra';
                                    }else {
                                        $kelas = 'kelas_'.$data->kelas;
                                    }
                                @endphp
                                <div class="col-12 col-md-5">
                                        <label for="uang_sekolah" class="form-label">{{__('Uang Sekolah')}}</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="tahun_masuk" value="@currency($schoolFeeAmount[$kelas])" disabled placeholder="0">
                                        </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label for="tunggakan" class="form-label">{{__('Tunggakan Uang Sekolah')}}</label>
                                        @if ($data->bulan_spp_terakhir != null)
                                            <select id="tunggakan" class="form-select" multiple="multiple" disabled>
                                                @foreach ($months as $item)
                                                    @if (\Carbon\Carbon::parse($item)->format('Y-m-d') < \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d'))
                                                        @php
                                                            array_push($tunggakan, \Carbon\Carbon::parse($item)->format('Y-m-d'));
                                                        @endphp
                                                        <option value="{{$item}}" selected>{{ \Carbon\Carbon::parse($item)->format('M Y') }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" class="form-control" value="Belum ada Transaksi" disabled>
                                        @endif
                                </div>
                            </div>

                </div>
            </div>
            <div class="col-12">
                <div class="statistics-card card">
                    <h5 class="content-desc">
                        Rincian Transaksi 
                        <br>
                        <span class="badge bg-info">
                           Total Transaksi @currency($totalTransaksi)
                        </span>
                    </h5>
                    <div class="table-responsive">
                        <table id="transactionTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Tanggal</th>
                                    <th>TA.</th>
                                    <th>Uang Pembangunan</th>
                                    <th>Uang Sekolah</th>
                                    <th>Jumlah Potongan</th>
                                    <th>Jumlah Lainnya</th>
                                    <th>Total</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1; 
                                @endphp
                                @foreach ($transactions as $item)
                                @php
                                    $potonganTextUS = false;
                                    $potonganTextUP = false;

                                    if($item->discounts->count() > 0){
                                        foreach ($item->discounts as $disc) {
                                            if ($disc->discount->jenis == 'Uang Sekolah') {
                                                $potonganTextUS = true;
                                            }elseif ($disc->discount->jenis == 'Uang Pembangunan') {
                                                $potonganTextUP = true;
                                            }
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>
                                        <a href="{{ route('transactions.details', $item->id) }}" class="btn btn-success btn-sm d-flex w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="d-inline me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>    
                                            {{ $item->no_bukti }}
                                        </a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->tgl_transaksi)->format('d M Y') }}</td>
                                    <td>{{ $item->tahun_ajaran }}</td>
                                    <td>
                                        @if ($potonganTextUP)
                                            <span class="text-danger text-decoration-line-through">@currency($item->jumlah_up)</span> <br/>
                                            @currency($item->schoolDevFee->sum('total'))
                                        @else
                                            @currency($item->jumlah_up) 
                                        @endif
                                    </td>
                                    <td>
                                        @if ($potonganTextUS)
                                            <span class="text-danger text-decoration-line-through">@currency($item->jumlah_us)</span> <br/>
                                            @currency($item->schoolFee->sum('total'))
                                        @else
                                            @currency($item->jumlah_us)
                                        @endif
                                        @if ($item->bulan != '')
                                            <span class="badge bg-primary">
                                                {{$item->bulan}}
                                            </span>
                                        @endif
                                    </td>
                                    <td>@currency($item->jumlah_potongan)</td>
                                    <td>@currency($item->jumlah_lainnya)</td>
                                    <td>@currency($item->total)</td>
                                    <td>{{ $item->jenis }}</td>
                                    <td>
                                        @if ($item->status == 'Success')
                                            <span class="badge bg-success">Success</span>
                                        @elseif ($item->status == 'Cancel')
                                            <span class="badge bg-warning">Cancelled</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif    
                                    </td>
                                    <td>
                                        @if ($item->status == 'Success')
                                            <a href="{{ route('transactions.print', $item->id) }}" class="btn btn-secondary btn-sm" title="Cetak" target="_blank">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Tanggal</th>
                                    <th>TA.</th>
                                    <th>Uang Pembangunan</th>
                                    <th>Uang Sekolah</th>
                                    <th>Jumlah Potongan</th>
                                    <th>Jumlah Lainnya</th>
                                    <th>Total</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="statistics-card card">
                    <h5 class="content-desc">
                        Rincian Transaksi Uang Sekolah 
                        <br>
                        <span class="badge bg-info">
                           Total Transaksi
                           @if (count($schoolFee) > 0)
                            @currency($schoolFee->sum('total'))
                           @endif
                        </span>
                    </h5>
                    <div class="table-responsive">
                        <table id="schoolFeeTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Bulan</th>
                                    <th>TA.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1; 
                                @endphp
                                @foreach ($schoolFee as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>
                                        <a href="{{ route('transactions.details', $item->id_transaksi) }}" class="btn btn-success btn-sm d-flex w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="d-inline me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>    
                                            {{ $item->transaction->no_bukti }}
                                        </a>
                                    </td>
                                    <td>{{ $item->bulan  }}</td>
                                    <td>{{ $item->transaction->tahun_ajaran  }}</td>
                                    <td>@currency($item->total)</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>TA.</th>
                                    <th>Bulan</th>
                                    <th>Total</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="statistics-card card">
                    <h5 class="content-desc">
                        Rincian Transaksi Uang Pembangunan Sekolah <br> (@currency($schoolFeeAmount['pembangunan_'.strtolower($tingkat)]))
                        <br>
                        <span class="badge bg-info">
                           Total Transaksi 
                           @if (count($schoolDevFee) > 0)
                            @currency($schoolDevFee->sum('total'))
                           @endif
                        </span>
                    </h5>
                    <div class="table-responsive">
                        <table id="schoolDevFeeTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1; 
                                @endphp
                                @foreach ($schoolDevFee as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>
                                        <a href="{{ route('transactions.details', $item->id_transaksi) }}" class="btn btn-success btn-sm d-flex w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="d-inline me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>    
                                            {{ $item->transaction->no_bukti }}
                                        </a>
                                    </td>
                                    <td>@currency($item->total)</td>
                                    <td>{{ $item->keterangan  }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="statistics-card card">
                    <h5 class="content-desc">
                        Rincian Transaksi dengan Potongan
                        <br>
                        <span class="badge bg-info">
                           Total Transaksi 
                           @if (count($discounts) > 0)
                            @currency($discounts->sum('total'))
                           @endif
                        </span>
                    </h5>
                    <div class="table-responsive">
                        <table id="discountTransactionTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Jenis Potongan</th>
                                    <th>Besaran</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1; 
                                @endphp
                                @foreach ($discounts as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>
                                        <a href="{{ route('transactions.details', $item->id_transaksi) }}" class="btn btn-success btn-sm d-flex w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="d-inline me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>    
                                            {{ $item->transaction->no_bukti }}
                                        </a>
                                    </td>
                                    <td>{{ $item->discount->jenis == 'Uang Sekolah' ? 'US' : 'UP'  }}-{{ $item->discount->nama  }}</td>
                                    <td>{{ $item->discount->besaran  }}%</td>
                                    <td>@currency($item->total)</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Jenis Potongan</th>
                                    <th>Besaran</th>
                                    <th>Total</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="statistics-card card">
                    <h5 class="content-desc">
                        Rincian Transaksi Lainnya
                        <br>
                        <span class="badge bg-info">
                           Total Transaksi 
                           @if (count($otherTransactions) > 0)
                            @currency($otherTransactions->sum('total'))
                           @endif
                        </span>
                    </h5>
                    <div class="table-responsive">
                        <table id="otherTransactionTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1; 
                                @endphp
                                @foreach ($otherTransactions as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>
                                        <a href="{{ route('transactions.details', $item->id_transaksi) }}" class="btn btn-success btn-sm d-flex w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="d-inline me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>    
                                            {{ $item->transaction->no_bukti }}
                                        </a>
                                    </td>
                                    <td>@currency($item->total)</td>
                                    <td>{{ $item->keterangan  }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
    
        </div>
    </div>

    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="transactionModalLabel">
                Pembayaran Uang Sekolah
                <span class="badge bg-info">
                    {{ $tingkat }}
                </span>
            </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="modalForm" method="POST" action="{{ route('transactions.pay') }}">
                @csrf
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" name="transfer">
                    <label for="transfer" class="form-check-label">Metode Transfer </label>
                </div>
                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="mb-3">
                            <label for="no_bukti" class="col-form-label">No. Bukti :</label>
                            <input type="text" class="form-control" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('dmyHms') }}" disabled>
                            <input type="hidden" class="form-control" name="no_bukti" id="no_bukti" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('dmyHms') }}" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="mb-3">
                            <label for="tahun_ajaran" class="col-form-label">TA. :</label>
                            <select name="tahun_ajaran" id="tahun_ajaran" class="form-select" required>
                                  @foreach ($schoolYear as $item)
                                      <option value="{{ $item->tahun_ajaran }}" {{ old('tahun_ajaran', $data->tahun_ajaran) == $item->id ? 'selected' : '' }}>{{ $item->tahun_ajaran }}</option>
                                  @endforeach
                            </select>
                            <x-input-error class="mt-2 text-danger" :messages="$errors->get('tahun_ajaran')" />
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="mb-3">
                            <label for="nama" class="col-form-label">Nama Siswa :</label>
                            <input type="hidden" class="form-control" id="id" name="id" required value="{{ $data->id }}">
                            <input type="text" class="form-control" id="nama" value="{{ $data->nis }} / {{ $data->nama }}" disabled>
                          </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <div class="mb-3">
                            <label for="nama" class="col-form-label">Kelas / Grup :</label>
                            <input type="text" class="form-control" id="nama" value="{{ $data->kelas }} / {{ $data->grup }}" disabled>
                            <input type="hidden" class="form-control" name="tingkat" value="{{ $data->tingkat }}{{ $data->grup }}{{ $data->kelas }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="bulan" class="col-form-label">Pilih Bulan Pembayaran :</label>
                            <select name="bulan[]" id="bulan" class="form-select" multiple="multiple" data-placeholder="Pilih bulan...">
                                    @foreach ($months as $month)
                                        <option value="{{$month}}" {{ old('bulan') == $month ? 'selected' : '' }} {{ $loop->index != 0 ? "disabled='disabled'" : '' }}>{{ \Carbon\Carbon::parse($month)->format('M Y') }}</option>
                                    @endforeach
                            </select>
                            <x-input-error class="mt-2 text-danger" :messages="$errors->get('bulan')" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="uang_sekolah" class="col-form-label">Uang Sekolah :</label>
                            <input type="hidden" class="form-control" name="uang_sekolah" value="{{$schoolFeeAmount[$kelas]}}" required>
                            <input type="text" class="form-control" id="uang_sekolah" data-us="{{$schoolFeeAmount[$kelas]}}" value="@currency($schoolFeeAmount[$kelas])" disabled >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="potongan" class="col-form-label">Pilih Potongan :</label>
                            <select name="potongan[]" id="potongan" class="form-select" multiple="multiple">
                                @foreach ($discountList as $item)
                                    <option value="{{ $item->id }}" data-potongan="{{ $item->besaran }}" data-jenis="{{ $item->jenis}}"
                                        @if ($discountStudent->contains('id_potongan', $item->id))  
                                            {{ 'selected' }}
                                        @endif 
                                    >
                                    {{ $item->jenis == "Uang Sekolah" ? "US" : "UP" }}-{{ $item->nama }} ({{ $item->besaran }}%)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2 text-danger" :messages="$errors->get('potongan')" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="keterangan" class="col-form-label">Keterangan :</label>
                            <input type="text" class="form-control" name='keterangan' id="keterangan" placeholder="-" >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="pembangunan" id="pembangunanSwitch">
                                <label for="uang_pembangunan" class="form-check-label">Uang Pembangunan (@currency($schoolFeeAmount['pembangunan_'.strtolower($tingkat)])):</label>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Rp.</span>
                                <input type="number" class="form-control" min="0" name="uang_pembangunan" id="uang_pembangunan" value="{{old('uang_pembangunan')}}" disabled placeholder="0">
                            </div>
                            <x-input-error class="mt-2 text-danger" :messages="$errors->get('uang_pembangunan')" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="pembangunan_ket" class="form-check-label">Keterangan :</label>
                            <input type="text" class="form-control" name='pembangunan_ket' id="pembangunan_ket" disabled placeholder="-" >
                        </div>
                    </div>
                </div>
                <div class="row" id="containerLainnya">
                    <div class="col-12">
                        <div class="form-check form-switch form-check-inline">
                            <input class="form-check-input" type="checkbox" role="switch" name="lainnya" id="lainnyaSwitch">
                            <label for="total_lainnya" class="form-check-label">
                                Pembayaran Lainnya : 
                                <button type="button" class="btn btn-secondary btn-sm" disabled id="btnLainnya">
                                    <img src="{{url('/assets/img/global/plus-circle.svg')}}" width="20" alt="">
                                </button>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <div>
                <h5 class="fw-bold">Total : <span id="total"></span></h5>
              </div>
              <div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Proses Pembayaran</button>
            </form>
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
            input[type="checkbox"] {
                display: grid;
                place-content: center;
            }

            input[type="checkbox"]::before {
                content: "";
                width: 0.65em;
                height: 0.65em;
                transform: scale(0);
                transition: 120ms transform ease-in-out;
                box-shadow: inset 1em 1em var(--form-control-color);
            }

            input[type="checkbox"]:checked::before {
                transform: scale(1);
            }

            .modal-footer{
                justify-content: space-between !important
            }

            .btn-pink{
                background-color: #DF4598;
                color: white;
            }

            .btn-pink:hover{
                background-color: #e79cc4;
                color: white;
            }
        </style>
    @endpush


    @push('addon-scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{ asset('js/dataTables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/dataTables/dataTables.bootstrap5.min.js') }}"></script>
        @if (session()->has('newurl'))
            <script type="text/javascript">
                window.open({{ session('newurl') }}, '_blank');
            </script>
        @endif
        <script>
            $(document).ready(function() {
                $( '#id' ).select2( {
                    theme: 'bootstrap-5',
                    placeholder: $( this ).data( 'placeholder' ),
                    closeOnSelect: true,
                } );

                $('.js-example-basic-multiple').select2({
                    theme: 'bootstrap-5',
                    placeholder: $( this ).data( 'placeholder' ),
                });

                $('#bulan').select2({
                    theme: 'bootstrap-5',
                    placeholder: $( this ).data( 'placeholder' ),
                    closeOnSelect: true,
                });

                $('#tunggakan').select2({
                    theme: 'bootstrap-5'
                });

                $('#potongan').select2({
                    theme: 'bootstrap-5',
                    placeholder: $( this ).data( 'placeholder' ),
                    closeOnSelect: false,
                });

                $('#id').change(function(){
                    $('#selectForm').submit();
                });

                $('#pembangunanSwitch').change(function(){
                    if(this.checked) {
                        $('#uang_pembangunan').prop('disabled', false);
                        $('#pembangunan_ket').prop('disabled', false);
                    }else{
                        $('#uang_pembangunan').prop('disabled', true);
                        $('#pembangunan_ket').prop('disabled', true);
                    }
                });

                $('#lainnyaSwitch').change(function(){
                    if(this.checked) {
                        $('#btnLainnya').prop('disabled', false);
                        $('#total_lainnya').prop('disabled', false);
                        $('#lainnya_ket').prop('disabled', false);
                    }else{
                        $('#btnLainnya').prop('disabled', true);
                        $('#total_lainnya').prop('disabled', true);
                        $('#lainnya_ket').prop('disabled', true);
                    }
                });

                $('select#bulan').change(function(e){
                    e.preventDefault();
                    const selectedOptions = $(this).find("option:selected");
                    const lastIndex = selectedOptions.last().index();

                    // Disable all options
                    $(this).find("option").prop("disabled", "disabled");

                    // Enable options up to the last selected one
                    for (let i = 0; i <= lastIndex +1; i++) {
                        $("select#bulan option:eq(" + i + ")").prop("disabled", false);
                    }
                });

                $('select#bulan option').click(function(e){
                    e.preventDefault();
                    const selectedOptions = $(this).find("option:selected");
                    const lastIndex = selectedOptions.last().index();

                    // Disable all options
                    $(this).find("option").prop("disabled", "disabled");

                    // Enable options up to the last selected one
                    for (let i = 0; i <= lastIndex +1; i++) {
                        $("select#bulan option:eq(" + i + ")").prop("disabled", false);
                    }
                });


                let bulan = 0;
                let potonganUs = 0;
                let potonganUp = 0;
                let lainnya = 0;

                function updateTotal(){
                    updatePotongan()
                    lainnya = 0;
                    if( $('#lainnyaSwitch').is(':checked') ){
                        $('input#total_lainnya').each(function() {
                            totalLainnya = parseInt($(this).val());
                            if(isNaN(totalLainnya)){
                                totalLainnya = 0; 
                            }
                            console.log(totalLainnya);
                            lainnya += totalLainnya;
                        })
                    }else{
                        lainnya = 0;
                    }
                        
                    // console.log(bulan + potongan);
                    let us = parseInt($('#uang_sekolah').data('us'));
                    let up = 0;
                    if( $('#pembangunanSwitch').is(':checked') ){
                        up = parseInt($('#uang_pembangunan').val());
                        
                        if(potonganUp > 0){
                            up = up - ((up * potonganUp) / 100);
                        }
                        
                        if(isNaN(up)){
                            up = 0; 
                        }
                    }
                    
                    var total = 0;
                    if (potonganUs > 0){
                        total = ((us * bulan) - (((us * potonganUs) / 100) * bulan)) + up + lainnya;
                    }else{
                        total = (us * bulan) + up + lainnya;
                    }

                    total = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    $("#total").text(total);
                }

                function updatePotongan(){
                    var totalPotonganUS = 0;
                    var totalPotonganUP = 0;
                    $('select#potongan option').each(function() {
                        if($(this).is(':selected')){
                            jenis = $(this).data('jenis');
                            if(jenis === 'Uang Sekolah'){
                                totalPotonganUS += parseInt($(this).data('potongan'));
                            }else if(jenis === 'Uang Pembangunan'){
                                totalPotonganUP += parseInt($(this).data('potongan'));
                            }
                        }
                    });
                    potonganUs = totalPotonganUS;
                    potonganUp = totalPotonganUP;
                }

                $('#modalForm').change(function(e) {
                    e.preventDefault();
                    updateTotal()
                });

                $('select#bulan').change(function(){
                    bulan = 0;
                    $('select#bulan option').each(function() {
                        if($(this).is(':selected')){
                            bulan += 1;
                            // console.log(bulan);
                        }
                    });
                    updatePotongan();
                    updateTotal();
                });

                $('select#potongan').change(function(e){
                    e.preventDefault();
                    updatePotongan();                    
                });

                var x = 0;
                $('#btnLainnya').click(function(e) {
                    e.preventDefault();
                    if (x < 3) {
                        $('#containerLainnya').append('<div class="row" id="element'+x+'"><div class="col-12 col-md-4"><div class="mb-3"><label for="total_lainnya" class="col-form-label">Nominal :</label><div class="input-group mb-3"><button type="button" class="btn btn-danger btn-sm d-inline delete" data-id="element'+x+'"><img src="{{url("/assets/img/global/trash.svg")}}" width="20" alt="hapus"></button><span class="input-group-text">Rp.</span><input type="number" class="form-control" min="0" name="total_lainnya[]" id="total_lainnya" placeholder="0"></div></div></div><div class="col-12 col-md-8"><div class="mb-3"><label for="lainnya_ket" class="col-form-label">Keterangan :</label><input type="text" class="form-control" name="lainnya_ket[]" id="lainnya_ket" placeholder="-" ></div></div></div>');
                        x++;
                    } else {
                        alert('Kamu sudah mencapai batas maksimum')
                    }
                });

                $('#containerLainnya').on("click", ".delete", function(e) {
                    e.preventDefault();
                    id = $( this ).data( 'id' );
                    parent = $( this ).parent('div');
                    inputValue = parent.find('input#total_lainnya').val()
                    $('#'+id).remove();
                    x--;

                    updateTotal();
                });


                $('#transactionTable').DataTable();
                $('#schoolFeeTable').DataTable();
                $('#schoolDevFeeTable').DataTable();
                $('#discountTransactionTable').DataTable();
                $('#otherTransactionTable').DataTable();
            }); 
        </script>
    @endpush

</x-app-layout>

