<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Siswa Tunggakan') }}
        </h2>
    </x-slot>

    <div class="content">
        <div class="row">
            <div class="col-12">
                <h2 class="content-title">{{ __('Tabel Data Siswa Tunggakan Uang Sekolah') }}</h2>
                <h5 class="content-desc mb-4">{{ __("Kelola data siswa dari aplikasi SPP Al Ulum.") }}</h5>
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
                                       <p class="mb-0 font-13">dari {{ $jumlah->RA }} Siswa</p>
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
                                      <p class="mb-0 font-13">dari {{ $jumlah->SD }} Siswa</p>
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
                                      <p class="mb-0 font-13"> dari {{ $jumlah->SMP }} Siswa</p>
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
                                    <p class="mb-0 font-13"> dari {{ $jumlah->SMA }} Siswa</p>
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
                    <form action="{{ route('reports.arrears') }}" method="POST">
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
                            <div class="col-auto">
                                <input type="hidden" class="form-control" name="date" value="{{ $filters->date }}" required/>
                                <x-input-error class="mt-2 text-danger" :messages="$errors->get('date')" />
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-form me-auto">
                                    <img src="{{url('/assets/img/global/filter.svg')}}" width="20" alt="">
                                    Filter
                                </button>
                            </div>
                        </form>
                            <div class="col-auto">
                                <form action="{{ route('reports.printArrears') }}" method="POST" target="_blank">
                                    @csrf
                                    <input type="hidden" name="tingkat" value="{{$filters->tingkat}}">
                                    <input type="hidden" name="date" value="{{ \Carbon\Carbon::today()->format('n') }}">
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
                <div class="statistics-card">
                    <div id="button-wrapper" class="col-md-6"></div>
                    <div class="table-responsive">
                        <table id="StudentTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>NIS</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Tahun Angkatan</th>
                                    <th>Tunggakan</th>
                                    <th>Total</th>
                                    <th width="15%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>NIS</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Tahun Angkatan</th>
                                    <th>Tunggakan</th>
                                    <th>Total</th>
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
        <link rel="stylesheet" href="{{asset('css/dataTables/dataTables.bootstrap5.min.css')}}">
        <link rel="stylesheet" href="{{asset('css/dataTables/buttons.bootstrap5.min.css')}}">
        <style>
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
            $(document).ready(function () {
                $('#StudentTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url : "{{ route('reports.getDataArrears') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "tingkat" : "{!! $filters->tingkat !!}",
                            "date" : "{!! $filters->date !!}"
                        }
                    },
                    columns: [
                        { data: "no" },
                        { data: "nis" },
                        { data: "nama" },
                        { data: "kelas" },
                        { data: "tahun_angkatan" },
                        { data: "tunggakan" },
                        { data: "total" },
                        { data: "action" },
                    ]	 
                });

            });
            
            Highcharts.chart('ColoumnChart', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Laporan Tunggakan Per Hari Ini'
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
                            '<td style="padding:0"><b>{point.y:.1f} Siswa</b></td></tr>',
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
                        data: [{!! $jumlah->RA !!}]

                    }, {
                        name: 'SD',
                        data: [{!! $jumlah->SD !!}]

                    }, {
                        name: 'SMP',
                        data: [{!! $jumlah->SMP !!}]

                    }, {
                        name: 'SMA',
                        data: [{!! $jumlah->SMA !!}]

                    }]
                });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        </script>
    @endpush

</x-app-layout>

