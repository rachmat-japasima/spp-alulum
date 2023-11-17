<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <div class="row mb-3">
                    <div class="col">
                        <div class="card radius-10 border-start border-0 border-3 border-info">
                           <div class="card-body">
                               <div class="d-flex align-items-center">
                                   <div>
                                       <p class="mb-0 text-secondary">Siswal RA</p>
                                       <h4 class="my-1 text-info">{{ $siswa->where('tingkat', 'RA')->count() }} Orang</h4>
                                       {{-- <p class="mb-0 font-13">Orang</p> --}}
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
                                      <p class="mb-0 text-secondary">Siswal SD</p>
                                      <h4 class="my-1 text-danger">{{ $siswa->where('tingkat', '0')->count() }} Orang</h4>
                                      {{-- <p class="mb-0 font-13">Orang</p> --}}
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
                                      <p class="mb-0 text-secondary">Siswal SMP</p>
                                      <h4 class="my-1 text-primary">{{ $siswa->where('tingkat', '1')->count() }} Orang</h4>
                                      {{-- <p class="mb-0 font-13">Orang</p> --}}
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
                                    <p class="mb-0 text-secondary">Siswa SMA</p>
                                    <h4 class="my-1 text-dark">{{ $siswa->where('tingkat', '2')->count() }} Orang</h4>
                                    {{-- <p class="mb-0 font-13">Orang</p> --}}
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
                    <figure>
                        <div id="lineChart">
                            <div id="loading">
                                <img src="{{ asset('assets/img/global/loading.svg') }}" alt="loading..." width="50">
                            </div>
                        </div>
                    </figure>
                </div>
            </div>
        </div>
    </div>

    @push('addon-styles')

    @endpush

    @push('addon-scripts')
        <script src="{{ asset('js/dataTables/dataTables.bootstrap5.min.js') }}"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="{{ asset('js/highchart/variable-pie.js') }}"></script>
        <script>
            $(document).ready(function() {
                $.ajax({
                    url: "{{ route('dashboard.getData') }}",
                    context: document.body
                    }).done(function(data) {
                        data = JSON.parse(data);
                        Highcharts.chart('lineChart', {
                            title: {
                                text: 'Laporan Transaksi Pembayaran',
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
                                categories : data.year
                            },

                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle'
                            },
                            tooltip: {
                                headerFormat: '',
                                pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
                                        'Total (Rp.): <b>{point.y}</b><br/>'
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
                                    data: data.totalRA
                                },
                                {
                                    name: 'SD',
                                    data: data.totalSD
                                },
                                {
                                    name: 'SMP',
                                    data: data.totalSMP
                                },
                                {
                                    name: 'SMA',
                                    data: data.totalSMA
                                }
                            ],
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
                    
                });
            })
        </script>
    @endpush

</x-app-layout>