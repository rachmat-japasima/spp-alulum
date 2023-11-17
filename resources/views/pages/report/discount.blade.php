<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Transaksi Dengan Potongan') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Laporan Transaksi Potongan') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Laporan transaksi dengan potongan untuk aplikasi SPP Al Ulum.") }}</h5>
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
                    <figure>
                        <div id="ColoumnChart"></div>
                        <p class="text-center">Data Transaksi Potongan {{ \Carbon\Carbon::parse($filters->startDate)->format("d/m/Y") }} - {{ \Carbon\Carbon::parse($filters->endDate)->format("d/m/Y") }}</p>
                    </figure>
                </div>
            </div>
            <div class="col-12">
                <div class="statistics-card card">
                    <figure>
                        <div id="pieChart"></div>
                        <p class="text-center">Total Transaksi : @currency($data->sum('jumlah_potongan'))</p>
                    </figure>
                </div>
            </div>
            <div class="col-12">
                <div class="row mb-3">
                    <div class="col">
                        <div class="card radius-10 border-start border-0 border-3 border-info">
                           <div class="card-body">
                               <div class="d-flex align-items-center">
                                   <div>
                                       <p class="mb-0 text-secondary">Total RA</p>
                                       <h4 class="my-1 text-info">@currency($total->RA)</h4>
                                       <p class="mb-0 font-13">dari {{ $jumlah->RA }} transkasi</p>
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
                                      <p class="mb-0 text-secondary">Total SD</p>
                                      <h4 class="my-1 text-danger">@currency($total->SD)</h4>
                                      <p class="mb-0 font-13">dari {{ $jumlah->SD }} transaksi</p>
                                  </div>
                                  <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto"><i class="fa fa-dollar"></i>
                                  </div>
                              </div>
                          </div>
                       </div>
                     </div>
                     <div class="col">
                       <div class="card radius-10 border-start border-0 border-3 border-primary">
                          <div class="card-body">
                              <div class="d-flex align-items-center">
                                  <div>
                                      <p class="mb-0 text-secondary">Total SMP</p>
                                      <h4 class="my-1 text-primary">@currency($total->SMP)</h4>
                                      <p class="mb-0 font-13"> dari {{ $jumlah->SMP }} transaksi</p>
                                  </div>
                                  <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class="fa fa-bar-chart"></i>
                                  </div>
                              </div>
                          </div>
                       </div>
                     </div>
                     <div class="col">
                       <div class="card radius-10 border-start border-0 border-3 border-secondary">
                          <div class="card-body">
                              <div class="d-flex align-items-center">
                                  <div>
                                    <p class="mb-0 text-secondary">Total SMA</p>
                                    <h4 class="my-1 text-dark">@currency($total->SMA)</h4>
                                    <p class="mb-0 font-13"> dari {{ $jumlah->SMA }} tranksasi</p>
                                  </div>
                                  <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto"><i class="fa fa-users"></i>
                                  </div>
                              </div>
                          </div>
                       </div>
                     </div> 
                </div>
            </div>
            <div class="col-12">
                <div class="statistics-card card">
                    <h5 class="content-desc">Filter Data</h5>
                    <form action="{{ route('reports.discount') }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                              <label for="tingkat" class="col-form-label">Filter</label>
                            </div>
                            <div class="col-2">
                              <select class="form-select" name="tingkat" id="tingkat" required>
                                <option value="full" {{ old('tingkat', $filters->tingkat) == 'full' ? 'selected' : '' }}>Semua</option>
                                <option value="RA" {{ old('tingkat', $filters->tingkat) == 'RA' ? 'selected' : '' }}>RA</option>
                                <option value="0" {{ old('tingkat', $filters->tingkat) == '0' ? 'selected' : '' }}>SD</option>
                                <option value="1" {{ old('tingkat', $filters->tingkat) == '1' ? 'selected' : '' }}>SMP</option>
                                <option value="2" {{ old('tingkat', $filters->tingkat) == '2' ? 'selected' : '' }}>SMA</option>
                              </select>
                              <x-input-error class="mt-2 text-danger" :messages="$errors->get('tingkat')" />
                            </div>
                            <div class="col-auto">
                                <select class="form-select" name="potongan" id="potongan" required>
                                    <option value="full" {{ old('potongan', $filters->potongan) == 'full' ? 'selected' : '' }}>Semua</option>
                                    @foreach ($listDiscount as $item)
                                        <option value="{{$item->id}}" {{ old('potongan', $filters->potongan) == $item->id ? 'selected' : '' }}>{{ $item->jenis == 'Uang Sekolah' ? '(US)' : '(UP)' }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2 text-danger" :messages="$errors->get('potongan')" />
                              </div>
                            <div class="col-auto">
                                <input type="date" name="startDate" class="form-control" value="{{ $filters->startDate }}" required>
                                <x-input-error class="mt-2 text-danger" :messages="$errors->get('startDate')" />
                            </div>
                            <div class="col-auto">
                                <input type="date" name="endDate" class="form-control" value="{{$filters->endDate}}" required>
                                <x-input-error class="mt-2 text-danger" :messages="$errors->get('endDate')" />
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-form me-auto">
                                    <img src="{{url('/assets/img/global/filter.svg')}}" width="20" alt="">
                                    Filter
                                </button>
                            </div>
                        </form>
                            <div class="col-auto d-flex justify-content-end">
                                <form action="{{ route('reports.printDiscount') }}" method="POST" target="_blank">
                                    @csrf
                                    <input type="hidden" class="form-control" name="tingkat" value="{{ $filters->tingkat }}">
                                    <input type="hidden" class="form-control" name="potongan" value="{{ $filters->potongan }}">
                                    <input type="hidden" class="form-control" name="startDate" value="{{ $filters->startDate }}">
                                    <input type="hidden" class="form-control" name="endDate" value="{{ $filters->endDate }}">
                                    <button type="submit" class="btn btn-print ms-auto">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                        Print Resume
                                    </button>
                            
                                </form>
                            </div>
                        </div>

                </div>
            </div>
            <div class="col-12">
                <div class="statistics-card card">
                    <h5 class="content-desc">
                        Rincian Transaksi Tahunan
                    </h5>
                    <div class="table-responsive">
                        <table id="transactionTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Potongan</th>
                                    <th>NIS/Nama</th>
                                    <th>Tingkat</th>
                                    <th>Kelas</th>
                                    <th>Tanggal</th>
                                    <th>TA.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Potongan</th>
                                    <th>NIS/Nama</th>
                                    <th>Tingkat</th>
                                    <th>Kelas</th>
                                    <th>Tanggal</th>
                                    <th>TA.</th>
                                    <th>Total</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
    
        </div>
    </div>

    @php
        $pieChart = [];
        foreach ($dataPotongan as $key => $value) {
            $obj = (object)['name' => $value->discount->nama, 'y' => intval($value->total_pot)];
            array_push($pieChart, $obj);
        }
    @endphp


    @push('addon-styles')
        <meta name="csrf-token" content="{{ csrf_token() }}" />
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
            .btn-print{
                background: #64CCC5;
                border-radius: 16px;
                padding: 10px 18px;
                color: #ffffff;
                font-size: 16px;
                font-weight: 600 !important;
            }

            .btn-print:hover{
                background: #65a39f;
                color: #ffffff;
            }
        </style>
    @endpush

    @push('addon-scripts')
        <script src="{{ asset('js/dataTables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/dataTables/dataTables.bootstrap5.min.js') }}"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="{{ asset('js/highchart/variable-pie.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#transactionTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url : "{{ route('reports.getDataDiscount') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "tingkat" : "{!! $filters->tingkat !!}",
                            "startDate" : "{!! $filters->startDate !!}",
                            "endDate" : "{!! $filters->endDate !!}",
                            "potongan" : "{!! $filters->potongan !!}"
                        }
                    },
                    columns: [
                        { data: "no" },
                        { data: "no_bukti" },
                        { data: "potongan" },
                        { data: "name" },
                        { data: "tingkat" },
                        { data: "kelas" },
                        { data: "tanggal" },
                        { data: "tahun_ajaran" },
                        { data: "total" },
                    ]	
                });
            }); 

            Highcharts.chart('ColoumnChart', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Laporan Transaksi Potongan'
                    },
                    subtitle: {
                        text: 'Yayasan Amanah Karomah Medan'
                    },
                    xAxis: {
                        categories: [
                            'Hari ini',
                        ],
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Rainfall (mm)'
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} Transaksi</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: 'RA',
                        data: [{!! $total->RA !!}]

                    }, {
                        name: 'SD',
                        data: [{!! $total->SD !!}]

                    }, {
                        name: 'SMP',
                        data: [{!! $total->SMP !!}]

                    }, {
                        name: 'SMA',
                        data: [{!! $total->SMA !!}]

                    }],
                    colors: [
                            '#00DFA2',
                            '#D80032',
                            '#3085C3',
                            '#C4DFDF',
                        ]
                });

                // Data retrieved from https://worldpopulationreview.com/country-rankings/countries-by-density
                Highcharts.chart('pieChart', {
                    chart: {
                        type: 'variablepie'
                    },
                    title: {
                        text: 'Laporan Transaksi Dengan Potongan',
                        align: 'center'
                    },
                    subtitle: {
                        text: 'Yayasan Amanah Karomah Medan',
                        align: 'center'
                    },
                    tooltip: {
                        headerFormat: '',
                        pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
                            'Total (Rp.): <b>{point.y}</b><br/>'
                    },
                    series: [{
                        minPointSize: 10,
                        innerSize: '20%',
                        zMin: 0,
                        name: 'countries',
                        borderRadius: 5,
                        data: {!! json_encode($pieChart) !!},
                        colors: [
                            '#3085C3',
                            '#F4E869',
                            '#713ABE',
                            '#D80032',
                            '#79AC78',
                            '#6C3428',
                            '#CEE6F3',
                            '#F24C3D',
                            '#22A699',
                            '#E893CF',
                        ]
                    }]
                });

    
        </script>
    @endpush

</x-app-layout>

