<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Transaksi Pembayaran Tahunan') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Laporan Transaksi Pembayaran Tahunan') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Laporan transaksi pembayaran tahunan untuk aplikasi SPP Al Ulum.") }}</h5>
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
                        <div id="lineChart">
                            <div id="loading">
                                <img src="{{ asset('assets/img/global/loading.svg') }}" alt="loading..." width="50">
                            </div>
                        </div>
                        <p class="text-center">Data Tahun {{ $filters->date }}</p>
                    </figure>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="statistics-card card">
                    <figure>
                        <div id="pieChart">
                            <div id="loading">
                                <img src="{{ asset('assets/img/global/loading.svg') }}" alt="loading..." width="50">
                            </div>
                        </div>
                        <p class="text-center">Total Transaksi : <span id="totalTransaksi">Rp. 0</span></p>
                    </figure>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="statistics-card card">
                    <figure>
                        <div id="pieChart2">
                            <div id="loading">
                                <img src="{{ asset('assets/img/global/loading.svg') }}" alt="loading..." width="50">
                            </div>
                        </div>
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
                                       <h4 class="my-1 text-info" id="totalRA">Rp. 0</h4>
                                       <p class="mb-0 font-13">dari <span id="jumlahRA">0</span> transkasi</p>
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
                                      <h4 class="my-1 text-danger" id="totalSD">Rp. 0</h4>
                                      <p class="mb-0 font-13">dari <span id="jumlahSD">0</span> transaksi</p>
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
                                      <h4 class="my-1 text-primary" id="totalSMP">Rp. 0</h4>
                                      <p class="mb-0 font-13"> dari <span id="jumlahSMP">0</span> transaksi</p>
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
                                    <h4 class="my-1 text-dark" id="totalSMA">Rp. 0</h4>
                                    <p class="mb-0 font-13"> dari <span id="jumlahSMA">0</span> tranksasi</p>
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
                <div class="row mb-3">
                    <div class="col">
                        <div class="card radius-10 bg-success border-0">
                           <div class="card-body">
                               <div class="d-flex align-items-center">
                                   <div>
                                       <p class="mb-0 text-light">Total Uang Sekolah</p>
                                       <h4 class="my-1 text-light" id="total_uang_sekolah">Rp. 0</h4>
                                       <p class="mb-0 font-13"></p>
                                   </div>
                                   <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto"><i class="fa fa-shopping-cart"></i>
                                   </div>
                               </div>
                           </div>
                        </div>
                      </div>
                      <div class="col">
                       <div class="card radius-10 bg-warning border-0">
                          <div class="card-body">
                              <div class="d-flex align-items-center">
                                  <div>
                                      <p class="mb-0 text-dark">Potongan Uang Sekolah</p>
                                      <h4 class="my-1 text-dark" id="total_potongan_us">Rp. 0</h4>
                                      <p class="mb-0 font-13"></p>
                                  </div>
                                  <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto"><i class="fa fa-dollar"></i>
                                  </div>
                              </div>
                          </div>
                       </div>
                     </div>
                     <div class="col">
                       <div class="card radius-10 bg-dark border-0">
                          <div class="card-body">
                              <div class="d-flex align-items-center">
                                  <div>
                                      <p class="mb-0 text-light">Total Uang Pembangunan</p>
                                      <h4 class="my-1 text-light" id="total_uang_pembangunan">Rp. 0</h4>
                                      <p class="mb-0 font-13"></p>
                                  </div>
                                  <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class="fa fa-bar-chart"></i>
                                  </div>
                              </div>
                          </div>
                       </div>
                     </div>
                     <div class="col">
                       <div class="card radius-10 bg-warning border-0 border-3">
                          <div class="card-body">
                              <div class="d-flex align-items-center">
                                  <div>
                                    <p class="mb-0 text-dark">Potongan Uang Pembangunan</p>
                                    <h4 class="my-1 text-dark" id="total_potongan_up">Rp. 0</h4>
                                    <p class="mb-0 font-13"></p>
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
                    <form action="{{ route('reports.yearly') }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                              <label for="tingkat" class="col-form-label">Filter</label>
                            </div>
                            <div class="col-3">
                              <select class="form-select" name="tingkat" id="tingkat" required>
                                <option value="full" {{ old('tingkat', $filters->tingkat) == 'full' ? 'selected' : '' }}>Semua</option>
                                <option value="RA" {{ old('tingkat', $filters->tingkat) == 'RA' ? 'selected' : '' }}>RA</option>
                                <option value="0" {{ old('tingkat', $filters->tingkat) == '0' ? 'selected' : '' }}>SD</option>
                                <option value="1" {{ old('tingkat', $filters->tingkat) == '1' ? 'selected' : '' }}>SMP</option>
                                <option value="2" {{ old('tingkat', $filters->tingkat) == '2' ? 'selected' : '' }}>SMA</option>
                              </select>
                              <x-input-error class="mt-2 text-danger" :messages="$errors->get('tingkat')" />
                            </div>
                            <div class="col-3">
                                <select class="form-select" name="date" id="date" required>
                                    @for ($i=2008; $i <= date('Y'); $i++)
                                        <option value="{{$i}}" {{ old('date', $filters->date) == $i ? 'selected' : '' }}>{{$i}}</option>
                                    @endfor
                                  </select>
                                  <x-input-error class="mt-2 text-danger" :messages="$errors->get('date')" />
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-form me-auto">
                                    <img src="{{url('/assets/img/global/filter.svg')}}" width="20" alt="">
                                    Filter
                                </button>
                            </div>
                        </form>
                            <div class="col-auto d-flex justify-content-end">
                                <form action="{{ route('reports.printResume') }}" method="POST" target="_blank">
                                    @csrf
                                    <input type="hidden" name="type" value="yearly">
                                    <input type="hidden" name="date" value="{{ $filters->date }}">
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
                                    <th>NIS/Nama</th>
                                    <th>Tingkat</th>
                                    <th>Kelas</th>
                                    <th>Tanggal</th>
                                    <th>TA.</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Metode</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Nama</th>
                                    <th>Tingkat</th>
                                    <th>Kelas</th>
                                    <th>Tanggal</th>
                                    <th>TA.</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Metode</th>
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
                function rupiah(number){
                    return new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR"
                    }).format(number);
                };

                $('#transactionTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url : "{{ route('reports.getData') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "type" : "yearly",
                            "tingkat" : "{!! $filters->tingkat !!}",
                            "date" : "{!! $filters->date !!}"
                        }
                    },
                    columns: [
                        { data: "no" },
                        { data: "no_bukti" },
                        { data: "name" },
                        { data: "tingkat" },
                        { data: "kelas" },
                        { data: "tanggal" },
                        { data: "tahun_ajaran" },
                        { data: "total" },
                        { data: "status" },
                        { data: "metode" },
                        { 
                            data: 'print', 
                            name: 'print',
                            orderable: false,
                            searchable: false,
                            "render": function ( data, type, row, meta ) { 
                                return data
                            }
                        },
                    ]	
                });

                $.ajax({
                    method: "POST",
                    url: "{{ route('reports.getDiagramDataYearly') }}",
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    data: {
                            "tingkat" : "{!! $filters->tingkat !!}",
                            "date" : "{!! $filters->date !!}"
                        },
                    context: document.body
                    }).done(function(data) {
                        data = JSON.parse(data);

                        $('#totalTransaksi').text(rupiah(data.totalTransaksi));
                        $('#totalRA').text(rupiah(data.total.RA));
                        $('#jumlahRA').text(data.jumlah.RA);
                        $('#totalSD').text(rupiah(data.total.SD));
                        $('#jumlahSD').text(data.jumlah.SD);
                        $('#totalSMP').text(rupiah(data.total.SMP));
                        $('#jumlahSMP').text(data.jumlah.SMP);
                        $('#totalSMA').text(rupiah(data.total.SMA));
                        $('#jumlahSMA').text(data.jumlah.SMA);

                        $('#total_potongan_up').text(rupiah(data.total_potongan_up));
                        $('#total_potongan_us').text(rupiah(data.total_potongan_us));
                        $('#total_uang_pembangunan').text(rupiah(data.total_uang_pembangunan));
                        $('#total_uang_sekolah').text(rupiah(data.total_uang_sekolah));

                        Highcharts.chart('lineChart', {
                            title: {
                                text: 'Laporan Transaksi Pembayaran Tahunan',
                                align: 'left'
                            },

                            subtitle: {
                                text: 'Yayasan Amanah Karomah Medan',
                                align: 'left'
                            },

                            yAxis: {
                                title: {
                                    text: 'Total Transaksi'
                                }
                            },

                            xAxis: {
                                accessibility: {
                                    rangeDescription: 'Range: 1 to 31'
                                },
                                tickInterval: 1,
                                title: {
                                    text: 'Tahun'
                                },
                            },

                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle'
                            },
                            tooltip: {
                                headerFormat: '',
                                pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
                                        'Total (Rp.): <b>{point.y}</b><br/>' +
                                        'Bulan: <b>{point.x}</b><br/>'
                            },
                            plotOptions: {
                                series: {
                                    label: {
                                        connectorAllowed: false
                                    },
                                    pointStart: 1,
                                    pointEnd: 30
                                }
                            },

                            series: [{
                                name: 'RA',
                                data: data.transaksiRA
                            }, {
                                name: 'SD',
                                data: data.transaksiSD
                            }, {
                                name: 'SMP',
                                data: data.transaksiSMP
                            }, {
                                name: 'SMA',
                                data: data.transaksiSMA
                            }],
                            colors: [
                                        '#00DFA2',
                                        '#D80032',
                                        '#3085C3',
                                        '#C4DFDF',
                                    ],

                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            layout: 'horizontal',
                                            align: 'center',
                                            verticalAlign: 'bottom'
                                        }
                                    }
                                }]
                            }

                            });

                            Highcharts.chart('pieChart', {
                                chart: {
                                    type: 'variablepie'
                                },
                                title: {
                                    text: 'Laporan Transaksi Pembayaran Tahunan',
                                    align: 'center'
                                },
                                subtitle: {
                                    text: 'Yayasan Amanah Karomah Medan',
                                    align: 'center'
                                },
                                tooltip: {
                                    headerFormat: '',
                                    pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
                                        'Total (Rp.): <b>{point.y}</b><br/>'+
                                        'Jumlah Transaksi: <b>{point.z}</b><br/>'
                                },
                                series: [{
                                    minPointSize: 10,
                                    innerSize: '20%',
                                    zMin: 0,
                                    name: 'countries',
                                    borderRadius: 5,
                                    data: [{
                                        name: 'RA',
                                        y: data.total.RA,
                                        z: data.jumlah.RA
                                    }, {
                                        name: 'SD',
                                        y: data.total.SD,
                                        z: data.jumlah.SD
                                    }, {
                                        name: 'SMP',
                                        y: data.total.SMP,
                                        z: data.jumlah.SMP
                                    }, {
                                        name: 'SMA',
                                        y: data.total.SMA,
                                        z: data.jumlah.SMA
                                    }],
                                    colors: [
                                        '#00DFA2',
                                        '#D80032',
                                        '#3085C3',
                                        '#C4DFDF',
                                    ]
                                }]
                            });

                            Highcharts.chart('pieChart2', {
                                chart: {
                                    type: 'pie'
                                },
                                title: {
                                    text: 'Perbandingan Uang Sekolah dan Uang Pebangunan',
                                    align: 'center'
                                },
                                subtitle: {
                                    text: 'Laporan Tahunan',
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
                                    name: 'Money',
                                    borderRadius: 5,
                                    data: [{
                                        name: 'Uang Sekolah',
                                        y: data.total_uang_sekolah
                                    }, {
                                        name: 'Potongan Uang Sekolah',
                                        y: data.total_potongan_us
                                    }, {
                                        name: 'Uang Pembangunan',
                                        y: data.total_uang_pembangunan
                                    }, {
                                        name: 'Potongan Uang Pembangunan',
                                        y: data.total_potongan_up
                                    }, {
                                        name: 'Lainnnya',
                                        y: data.total.lainnya
                                    }],
                                    colors: [
                                        '#0079FF',
                                        '#00DFA2',
                                        '#F6FA70',
                                        '#FF0060',
                                        '#FFA1F5'
                                    ]
                                }]
                            });

                    })
            }); 

            

        </script>
    @endpush

</x-app-layout>

