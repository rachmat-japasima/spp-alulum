<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Fee;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\DiscountTransaction;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function daily(Request $request): View
    {
        $day = date("Y-m-d", strtotime($request->date));
        $tingkat = $request->tingkat;
        if ($tingkat == 'full') {
            $data = Transaction::whereDate('tgl_transaksi', $day)->get();
            $successTransaksi = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->get();

            $RA = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', 'RA');
            })->get();

            $SD = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '0');
            })->get();

            $SMP = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '1');
            })->get();

            $SMA = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '2');
            })->get();

            $jumlah = (object)['RA' => $RA->count(), 'SD' => $SD->count(), 'SMP' => $SMP->count(), 'SMA' => $SMA->count(), 'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count()];
            $total = (object)['RA' => $RA->sum('total'), 'SD' => $SD->sum('total'), 'SMP' => $SMP->sum('total'), 'SMA' => $SMA->sum('total'), 'lainnya' => $successTransaksi->sum('jumlah_lainnya')];
        } else {
            $data = Transaction::whereDate('tgl_transaksi', $day)->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', $tingkat);
            })->get();
            $successTransaksi = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')
                ->whereHas('student', function ($query) use ($tingkat) {
                    $query->where('tingkat', $tingkat);
                })->get();

            switch ($tingkat) {
                case 'RA':
                    $RA = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', 'RA');
                    })->get();
                    break;
                case '0':
                    $SD = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '0');
                    })->get();
                    break;
                case '1':
                    $SMP = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '1');
                    })->get();
                    break;
                case '2':
                    $SMA = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '2');
                    })->get();
                    break;
            }

            $jumlah = (object)['RA' => $tingkat == 'RA' ? $RA->count() : 0, 'SD' => $tingkat == '0' ? $SD->count() : 0, 'SMP' => $tingkat == '1' ? $SMP->count() : 0, 'SMA' => $tingkat == '2' ? $SMA->count() : 0, 'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count()];
            $total = (object)['RA' => $tingkat == 'RA' ? $RA->sum('total') : 0, 'SD' => $tingkat == '0' ? $SD->sum('total') : 0, 'SMP' => $tingkat == '1' ? $SMP->sum('total') : 0, 'SMA' => $tingkat == '2' ? $SMA->sum('total') : 0, 'lainnya' => $successTransaksi->sum('jumlah_lainnya')];
        }

        $totalUS = 0;
        $totalUP = 0;
        $totalPotUS = 0;
        $totalPotUP = 0;
        foreach ($successTransaksi as $item) {
            $totalUS += $item->schoolFee->sum('total');
            $totalUP += $item->schoolDevFee->sum('total');

            if ($item->discounts->count() > 0) {
                foreach ($item->discounts as $disc) {
                    if ($disc->discount->jenis == 'Uang Sekolah') {
                        $totalPotUS += $disc->total;
                    } elseif ($disc->discount->jenis == 'Uang Pembangunan') {
                        $totalPotUP += $disc->total;
                    }
                }
            }
        }

        $totalTransaksi = $successTransaksi->sum('total');
        $filters = (object)['tingkat' => $request->tingkat, 'date' => $request->date];

        return view('pages.report.daily', ([
            'data' => $data,
            'totalTransaksi' => $totalTransaksi,
            'filters' => $filters,
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => $totalUS,
            'total_potongan_us' => $totalPotUS,
            'total_uang_pembangunan' => $totalUP,
            'total_potongan_up' => $totalPotUP,
        ]));
    }

    /**
     * Show the data transaction for monthly.
     */
    public function monthly(Request $request)
    {
        $filters = (object)['tingkat' => $request->tingkat, 'date' => $request->date];

        return view('pages.report.monthly', ([
            'filters' => $filters,
        ]));
    }

    public function semester(Request $request)
    {
        $filters = (object)['tingkat' => $request->tingkat, 'date' => $request->date, 'semester' => $request->semester];

        return view('pages.report.semester', ([
            'filters' => $filters,
        ]));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function yearly(Request $request)
    {

        $filters = (object)['tingkat' => $request->tingkat, 'date' => $request->date];

        return view('pages.report.yearly', ([
            'filters' => $filters,
        ]));
    }

    /**
     * Display the specified resource.
     */
    public function schoolYear(Request $request)
    {
        $filters = (object)['tingkat' => $request->tingkat, 'date' => $request->date];

        $listSchoolYear = SchoolYear::all();

        return view('pages.report.schoolYear', ([
            'filters' => $filters,
            'listSchoolYear' => $listSchoolYear,
        ]));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function getData(Request $request)
    {
        $type = $request->type;
        $class = $request->tingkat;
        if ($type == 'daily') {
            $day = date("Y-m-d", strtotime($request->date));
            if ($class == 'full') {
                $data = Transaction::whereDate('tgl_transaksi', $day)->get();
            } else {
                $data = Transaction::whereDate('tgl_transaksi', $day)->whereHas('student', function ($query) use ($class) {
                    $query->where('tingkat', $class);
                })->get();
            }
        } elseif ($type == 'monthly') {
            $month = date("m", strtotime($request->date));
            $year = date("Y", strtotime($request->date));
            if ($class == 'full') {
                $data = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->get();
            } else {
                $data = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->whereHas('student', function ($query) use ($class) {
                    $query->where('tingkat', $class);
                })->get();
            }
        } elseif ($type == 'yearly') {
            if ($class == 'full') {
                $data = Transaction::whereYear('tgl_transaksi', $request->date)->get();
            } else {
                $data = Transaction::whereYear('tgl_transaksi', $request->date)->whereHas('student', function ($query) use ($class) {
                    $query->where('tingkat', $class);
                })->get();
            }
        } elseif ($type == 'schoolYear') {
            if ($class == 'full') {
                $data = Transaction::where('tahun_ajaran', $request->date)->where('no_bukti', '!=', '')->with('student')->get();
            } else {
                $data = Transaction::where('tahun_ajaran', $request->date)->whereHas('student', function ($query) use ($class) {
                    $query->where('tingkat', $class);
                })->get();
            }
        } elseif ($type == 'semester') {
            $year = $request->date;
            if ($request->semester == 'Ganjil') {
                $start = Carbon::parse($year . '-01-01')->format('Y-m-d');
                $end = Carbon::parse($year . '-06-30')->format('Y-m-d');
            } else {
                $start = Carbon::parse($year . '-07-01')->format('Y-m-d');
                $end = Carbon::parse($year . '-12-31')->format('Y-m-d');
            }

            if ($class == 'full') {
                $data = Transaction::whereBetween('tgl_transaksi', [$start, $end])->get();
            } else {
                $data = Transaction::whereBetween('tgl_transaksi', [$start, $end])->whereHas('student', function ($query) use ($class) {
                    $query->where('tingkat', $class);
                })->get();
            }
        } else {
            $data = Transaction::where('no_bukti', '!=', '')->with('student')->get();
        }

        $dataTable = collect([]);
        $no = 1;
        foreach ($data as $item) {
            if ($item->student != null) {
                $kelas = $item->student->kelas;
                $name = $item->student->nis . '/' . $item->student->nama;

                if ($item->student->tingkat == '0') {
                    $tingkat = '<span class="badge bg-danger">SD</span>';
                } elseif ($item->student->tingkat == '1') {
                    $tingkat = '<span class="badge bg-primary">SMP</span>';
                } elseif ($item->student->tingkat == '2') {
                    $tingkat = '<span class="badge bg-secondary">SMA</span>';
                } elseif ($item->student->tingkat == 'RA') {
                    $tingkat = '<span class="badge bg-info">RA</span>';
                }
            } else {
                $tingkat = '';
                $kelas = '';
                $name = '';
            }

            if ($item->status == 'Success') {
                $print = '<a href="' . route('transactions.print', $item->id) . '" class="btn btn-warning btn-sm" title="Cetak" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            </a>';
            } else {
                $print = '';
            }

            if ($item->status == 'Success') {
                $status = '<span class="badge bg-success">Success</span>';
            } elseif ($item->status == 'Cancel') {
                $status = '<span class="badge bg-warning">Cancelled</span>';
            } else {
                $status = '<span class="badge bg-danger">Failed</span>';
            }

            $tanggal = \Carbon\Carbon::parse($item->tgl_transaksi)->format('d M Y');
            $total = $this->currency($item->total);
            $no_bukti = '<a href="' . route('transactions.details', $item->id) . '" class="btn btn-success btn-sm d-flex w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" class="d-inline me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>    
                                ' . $item->no_bukti . '
                            </a>';

            $dataTable->push([
                'no' => $no,
                'no_bukti' => $no_bukti,
                'name' => $name,
                'tingkat' => $tingkat,
                'kelas' => $kelas,
                'tanggal' => $tanggal,
                'tahun_ajaran' => $item->tahun_ajaran,
                'total' => $total,
                'status' => $status,
                'metode' => $item->jenis,
                'print' => $print
            ]);
            $no++;
        }

        // dd($dataTable);

        return DataTables::of($dataTable)->escapeColumns([])->toJson();
    }

    function currency($expression)
    {
        return "Rp. " . number_format($expression, 0, ',', '.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function arrears(Request $request)
    {
        $tingkat = $request->tingkat;
        $month = $request->date;

        // dd($month);
        if ($tingkat == 'full') {
            $data = Student::where('status', 1)->whereNotNull('bulan_spp_terakhir')->where('bulan_spp_terakhir', '<', Carbon::now()->format('Y-m-d'))->get();
        } else {
            $data = Student::where('status', 1)->whereNotNull('bulan_spp_terakhir')->where('tingkat', $tingkat)->where('bulan_spp_terakhir', '<', Carbon::now()->format('Y-m-d'))->get();
        }

        $jumlah = (object)['RA' => 0, 'SD' => 0, 'SMP' => 0, 'SMA' => 0,];
        $total = (object)['RA' => 0, 'SD' => 0, 'SMP' => 0, 'SMA' => 0];

        foreach ($data as $item) {
            $schoolFeeAmount = Fee::where('tahun_angkatan', $item->tahun_angkatan)->first();

            if ($item->kelas == 'RA') {
                $kelasFee = 'ra';
            } else {
                $kelasFee = 'kelas_' . $item->kelas;
            }

            if ($item->bulan_spp_terakhir != null && $item->bulan_spp_terakhir != 0 && Carbon::parse($item->bulan_spp_terakhir)->format('m') != Carbon::now()->format('m')) {
                $arrears = $this->getArrearsMonths($item->bulan_spp_terakhir);
                $totalArrears = count($arrears) * intval($schoolFeeAmount[$kelasFee]);
            } else {
                $totalArrears = 0;
            }

            if ($item->tingkat == '0') {
                $jumlah->SD += 1;
                $total->SD += $totalArrears;
            } elseif ($item->tingkat == '1') {
                $jumlah->SMP += 1;
                $total->SMP += $totalArrears;
            } elseif ($item->tingkat == '2') {
                $jumlah->SMA += 1;
                $total->SMA += $totalArrears;
            } elseif ($item->tingkat == 'RA') {
                $jumlah->RA += 1;
                $total->RA += $totalArrears;
            }
        }

        $filters = (object)['tingkat' => $tingkat, 'date' => $month];

        return view('pages.report.arrears', compact('data', 'filters', 'jumlah', 'total'));
    }

    public function getDataArrears(Request $request)
    {
        $tingkat = $request->tingkat;
        $month = $request->date;


        if ($tingkat == 'full') {
            $data = Student::where('status', 1)->whereNotNull('bulan_spp_terakhir')->where('bulan_spp_terakhir', '<', Carbon::now()->format('Y-m-d'))->get();
        } else {
            $data = Student::where('status', 1)->whereNotNull('bulan_spp_terakhir')->where('tingkat', $tingkat)->where('bulan_spp_terakhir', '<', Carbon::now()->format('Y-m-d'))->get();
        }

        $dataTable = collect([]);
        $no = 1;
        foreach ($data as $item) {
            $schoolFeeAmount = Fee::where('tahun_angkatan', $item->tahun_angkatan)->first();

            if ($item->kelas == 'RA') {
                $kelasFee = 'ra';
            } else {
                $kelasFee = 'kelas_' . $item->kelas;
            }

            if ($item->tingkat == '0') {
                $tingkat = '<span class="badge bg-danger">SD</span>';
            } elseif ($item->tingkat == '1') {
                $tingkat = '<span class="badge bg-primary">SMP</span>';
            } elseif ($item->tingkat == '2') {
                $tingkat = '<span class="badge bg-secondary">SMA</span>';
            } elseif ($item->tingkat == 'RA') {
                $tingkat = '<span class="badge bg-info">RA</span>';
            } else {
                $tingkat = '';
            }

            $nama = $item->nama . ' ' . $tingkat;
            $kelas = $item->kelas . '/' . $item->grup;

            if ($item->bulan_spp_terakhir != null && $item->bulan_spp_terakhir != 0 && Carbon::parse($item->bulan_spp_terakhir)->format('m') != Carbon::now()->format('m')) {
                $arrears = $this->getArrearsMonths($item->bulan_spp_terakhir);
                $bulan = count($arrears) > 1 ? Carbon::parse($arrears[0])->format('M Y') . '-' . Carbon::parse($arrears[count($arrears) - 1])->format('M Y')  : Carbon::parse($arrears[0])->format('M Y');
                if (count($arrears) == 1) {
                    $color = 'primary';
                } elseif (count($arrears) == 2 || count($arrears) < 4) {
                    $color = 'warning';
                } elseif (count($arrears) == 4 || count($arrears) < 6) {
                    $color = 'danger';
                } elseif (count($arrears) >= 6) {
                    $color = 'dark';
                }
                $bulantext = '<br/><span class="badge bg-' . $color . '">' . $bulan . '</span>';
                $tunggakan = count($arrears) . ' Bulan ' . $bulantext;
                $total = count($arrears) * intval($schoolFeeAmount[$kelasFee]);
            } else {
                $tunggakan = 'Belum ada transaksi';
                $total = 0;
            }


            $total = $this->currency($total);
            $btnPay = '<a href="' . route('transactions.show', $item->id) . '" class="btn btn-form"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg> Bayar</a>';

            $dataTable->push([
                'no' => $no,
                'nis' => $item->nis,
                'nama' => $nama,
                'kelas' => $kelas,
                'tahun_angkatan' => $item->tahun_angkatan,
                'tunggakan' => $tunggakan,
                'total' => $total,
                'action' => $btnPay
            ]);
            $no++;
        }

        // dd($dataTable);

        return DataTables::of($dataTable)->escapeColumns([])->toJson();
    }

    public function arrearsPrint(Request $request)
    {
        $tingkat = $request->tingkat;
        $month = $request->date;

        // $TA = SchoolYear::latest()->first();
        $students = Student::where('status', 1)
            ->whereNotNull('bulan_spp_terakhir')
            ->where('bulan_spp_terakhir', '<', Carbon::now()->format('Y-m-d'))->OrderBy('tingkat', 'asc')
            ->OrderBy('kelas', 'asc')
            ->with('transactions')
            ->get();

        // dd($month);
        if ($tingkat == 'full') {
            $data = Student::where('status', 1)->whereNotNull('bulan_spp_terakhir')->where('bulan_spp_terakhir', '<', Carbon::now()->format('Y-m-d'))->OrderBy('tingkat', 'asc')->OrderBy('kelas', 'asc')->get();
        } else {
            $data = Student::where('status', 1)->whereNotNull('bulan_spp_terakhir')->where('tingkat', $tingkat)->where('bulan_spp_terakhir', '<', Carbon::now()->format('Y-m-d'))->OrderBy('tingkat', 'asc')->OrderBy('kelas', 'asc')->get();
        }

        $transactionsRA = Transaction::whereBelongsTo($students->where('tingkat', 'RA'))->where('status', 'Success')->sum('jumlah_us');
        $transactionsSD = Transaction::whereBelongsTo($students->where('tingkat', '0'))->where('status', 'Success')->sum('jumlah_us');
        $transactionsSMP = Transaction::whereBelongsTo($students->where('tingkat', '1'))->where('status', 'Success')->sum('jumlah_us');
        $transactionsSMA = Transaction::whereBelongsTo($students->where('tingkat', '2'))->where('status', 'Success')->sum('jumlah_us');

        $jumlah = (object)['RA' => 0, 'SD' => 0, 'SMP' => 0, 'SMA' => 0,];
        $total = (object)['RA' => 0, 'SD' => 0, 'SMP' => 0, 'SMA' => 0];
        $siswa = (object)['RA' => $students->where('tingkat', 'RA')->count(), 'SD' => $students->where('tingkat', '0')->count(), 'SMP' => $students->where('tingkat', '1')->count(), 'SMA' => $students->where('tingkat', '2')->count()];
        $totalUS = (object)['RA' => $transactionsRA, 'SD' => $transactionsSD, 'SMP' => $transactionsSMP, 'SMA' => $transactionsSMA];

        foreach ($students as $key => $item) {
            $schoolFeeAmount = Fee::where('tahun_angkatan', $item->tahun_angkatan)->first();

            if ($item->kelas == 'RA') {
                $kelasFee = 'ra';
            } else {
                $kelasFee = 'kelas_' . $item->kelas;
            }

            if ($item->bulan_spp_terakhir != null && $item->bulan_spp_terakhir != 0 && Carbon::parse($item->bulan_spp_terakhir)->format('m') != Carbon::now()->format('m')) {
                if (is_null($item->bulan_spp_terakhir) === false) {
                    $arrears = $this->getArrearsMonths($item->bulan_spp_terakhir);
                    $totalArrears = count($arrears) * intval($schoolFeeAmount[$kelasFee]);
                    $bulan = count($arrears) > 1 ? Carbon::parse($arrears[0])->format('M Y') . '-' . Carbon::parse($arrears[count($arrears) - 1])->format('M Y')  : Carbon::parse($arrears[0])->format('M Y');
                    $jumlahBulan = count($arrears);
                }
            } else {
                $totalArrears = 0;
                $bulan = '-';
                $jumlahBulan = 0;
            }

            $jumlah_up = $item->transactions->where('status', 'Success')->sum('jumlah_up');

            $data[$key]['uang_sekolah'] = intval($schoolFeeAmount[$kelasFee]);
            $data[$key]['bulan'] = $bulan;
            $data[$key]['total_tunggakan'] = $totalArrears;
            $data[$key]['jumlah_bulan'] = $jumlahBulan;

            if ($item->tingkat == '0') {
                $jumlah->SD += 1;
                $total->SD += $totalArrears;
                $data[$key]['jumlah_up'] = intval($schoolFeeAmount['pembangunan_sd']) - $jumlah_up;
            } elseif ($item->tingkat == '1') {
                $jumlah->SMP += 1;
                $total->SMP += $totalArrears;
                $data[$key]['jumlah_up'] =  intval($schoolFeeAmount['pembangunan_smp']) -  $jumlah_up;
            } elseif ($item->tingkat == '2') {
                $jumlah->SMA += 1;
                $total->SMA += $totalArrears;
                $data[$key]['jumlah_up'] =  intval($schoolFeeAmount['pembangunan_sma']) - $jumlah_up;
            } elseif ($item->tingkat == 'RA') {
                $jumlah->RA += 1;
                $total->RA += $totalArrears;
                $data[$key]['jumlah_up'] =  intval($schoolFeeAmount['pembangunan_ra']) - $jumlah_up;
            }
        }

        $filters = (object)['tingkat' => $tingkat, 'date' => $month];

        return view('pages.report.print.arrears', compact('data', 'filters', 'jumlah', 'total', 'siswa', 'totalUS'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    function monthToNumber($month)
    {
        switch ($month) {
            case 'Januari':
                return 1;
            case 'Februari':
                return 2;
            case 'Maret':
                return 3;
            case 'April':
                return 4;
            case 'Mei':
                return 5;
            case 'Juni':
                return 6;
            case 'Juli':
                return 7;
            case 'Agustus':
                return 8;
            case 'September':
                return 9;
            case 'Oktober':
                return 10;
            case 'November':
                return 11;
            case 'Desember':
                return 12;
            default:
                return 0;
        }
    }

    function getArrearsMonths($lastMonth)
    {
        // $months = [null, 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        // $listMonths = array();

        // if($start > $end){
        //     $from_year = date('Y', strtotime('-1 year'));
        //     $to_year = date('Y');

        //     $startDate = date($from_year.'-'.($start+1).'-01');
        //     $diff = (($to_year - $from_year) * 12) + ($end - ($start+1));

        //     for($i=0;$i<=$diff;$i++) {
        //         array_push($listMonths, $months[date('n', strtotime('+'.$i.' month', strtotime($startDate)))]);
        //     }

        // }else{
        //     $range = range($start+1, $end);

        //     foreach ($range as $month){
        //         array_push($listMonths, $months[$month]);
        //     }
        // }

        $listMonths = array();
        $lastMonth = strtotime($lastMonth);

        for ($i = 1; $i <= 18; $i++) {
            $month = date("Y-m-d", strtotime("+" . $i . " month", $lastMonth));
            if (strtotime($month) < strtotime(date("Y-m-d"))) {
                array_push($listMonths, $month);
            }
        }



        return $listMonths;
    }

    public function resumePrint(Request $request)
    {
        $type = $request->type;

        if ($type == 'daily') {
            $day = date("Y-m-d", strtotime($request->date));

            $RA = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', 'RA');
            })->get();

            $SD = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '0');
            })->get();

            $SMP = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '1');
            })->get();

            $SMA = Transaction::whereDate('tgl_transaksi', $day)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '2');
            })->get();
        } elseif ($type == 'monthly') {
            $month = date("m", strtotime($request->date));
            $year = date("Y", strtotime($request->date));

            $RA = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', 'RA');
            })->get();

            $SD = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '0');
            })
                ->get();

            $SMP = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '1');
            })
                ->get();

            $SMA = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '2');
            })->get();
        } elseif ($type == 'yearly') {
            $RA = Transaction::whereYear('tgl_transaksi', $request->date)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', 'RA');
            })->get();

            $SD = Transaction::whereYear('tgl_transaksi', $request->date)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '0');
            })->get();

            $SMP = Transaction::whereYear('tgl_transaksi', $request->date)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '1');
            })->get();

            $SMA = Transaction::whereYear('tgl_transaksi', $request->date)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '2');
            })->get();
        } elseif ($type == 'schoolYear') {
            $RA = Transaction::where('tahun_ajaran', $request->date)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', 'RA');
            })->get();

            $SD = Transaction::where('tahun_ajaran', $request->date)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '0');
            })->get();

            $SMP = Transaction::where('tahun_ajaran', $request->date)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '1');
            })->get();

            $SMA = Transaction::where('tahun_ajaran', $request->date)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '2');
            })->get();
        } elseif ($type == 'semester') {
            $year = $request->date;
            if ($request->semester == 'Ganjil') {
                $start = Carbon::parse($year . '-01-01')->format('Y-m-d');
                $end = Carbon::parse($year . '-06-30')->format('Y-m-d');
            } else {
                $start = Carbon::parse($year . '-07-01')->format('Y-m-d');
                $end = Carbon::parse($year . '-12-31')->format('Y-m-d');
            }

            $RA = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', 'RA');
            })->get();

            $SD = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '0');
            })->get();

            $SMP = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '1');
            })->get();

            $SMA = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '2');
            })->get();
        }

        if ($RA->isNotEmpty()) {
            $potRA = DiscountTransaction::whereBelongsTo($RA)
                ->select(DB::raw('*, SUM(total) as total_pot'))
                ->groupBy('id_potongan')
                ->with(['discount'])
                ->get();
        } else {
            $potRA = [];
        }

        if ($SD->isNotEmpty()) {
            $potSD = DiscountTransaction::whereBelongsTo($SD)
                ->select(DB::raw('*, SUM(total) as total_pot'))
                ->groupBy('id_potongan')
                ->with(['discount'])
                ->get();
        } else {
            $potSD = [];
        }

        if ($SMP->isNotEmpty()) {
            $potSMP = DiscountTransaction::whereBelongsTo($SMP)
                ->select(DB::raw('*, SUM(total) as total_pot'))
                ->groupBy('id_potongan')
                ->with(['discount'])
                ->get();
        } else {
            $potSMP = [];
        }

        if ($SMA->isNotEmpty()) {
            $potSMA = DiscountTransaction::whereBelongsTo($SMA)
                ->select(DB::raw('*, SUM(total) as total_pot'))
                ->groupBy('id_potongan')
                ->with(['discount'])
                ->get();
        } else {
            $potSMA = [];
        }

        $jumlah = (object)['RA' => $RA->count(), 'SD' => $SD->count(), 'SMP' => $SMP->count(), 'SMA' => $SMA->count()];
        $total = (object)['RA' => $RA->sum('total'), 'SD' => $SD->sum('total'), 'SMP' => $SMP->sum('total'), 'SMA' => $SMA->sum('total')];

        $RA = (object)['manualUS' => $RA->where('jenis', 'Manual')->sum('jumlah_us'), 'mbankingUS' => $RA->where('jenis', 'M-Banking')->sum('jumlah_us'), 'UP' => $RA->sum('jumlah_up'), 'Pot' => $RA->sum('jumlah_potongan'), 'Lain' => $RA->sum('jumlah_lainnya')];
        $SD = (object)['manualUS' => $SD->where('jenis', 'Manual')->sum('jumlah_us'), 'mbankingUS' => $SD->where('jenis', 'M-Banking')->sum('jumlah_us'), 'UP' => $SD->sum('jumlah_up'), 'Pot' => $SD->sum('jumlah_potongan'), 'Lain' => $SD->sum('jumlah_lainnya')];
        $SMP = (object)['manualUS' => $SMP->where('jenis', 'Manual')->sum('jumlah_us'), 'mbankingUS' => $SMP->where('jenis', 'M-Banking')->sum('jumlah_us'), 'UP' => $SMP->sum('jumlah_up'), 'Pot' => $SMP->sum('jumlah_potongan'), 'Lain' => $SMP->sum('jumlah_lainnya')];
        $SMA = (object)['manualUS' => $SMA->where('jenis', 'Manual')->sum('jumlah_us'), 'mbankingUS' => $SMA->where('jenis', 'M-Banking')->sum('jumlah_us'), 'UP' => $SMA->sum('jumlah_up'), 'Pot' => $SMA->sum('jumlah_potongan'), 'Lain' => $SMA->sum('jumlah_lainnya')];

        // print($RA);
        $filters = (object)['type' => $request->type, 'date' => $request->date];
        return view('pages.report.print.daily', ([
            'filters' => $filters,
            'jumlah' => $jumlah,
            'total' => $total,
            'RA' => $RA,
            'SD' => $SD,
            'SMP' => $SMP,
            'SMA' => $SMA,
            'potRA' => $potRA,
            'potSD' => $potSD,
            'potSMP' => $potSMP,
            'potSMA' => $potSMA,
        ]));
    }

    public function discount(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $tingkat = $request->tingkat;
        $potongan = $request->potongan;

        if ($tingkat == 'full') {
            $data = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->get();

            $RA = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', 'RA');
            })->get();

            $SD = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '0');
            })->get();

            $SMP = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '1');
            })->get();

            $SMA = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '2');
            })->get();

            $jumlah = (object)['RA' => $RA->count(), 'SD' => $SD->count(), 'SMP' => $SMP->count(), 'SMA' => $SMA->count()];
            $total = (object)['RA' => $RA->sum('jumlah_potongan'), 'SD' => $SD->sum('jumlah_potongan'), 'SMP' => $SMP->sum('jumlah_potongan'), 'SMA' => $SMA->sum('jumlah_potongan')];
        } else {
            $data = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', $tingkat);
            })->get();

            switch ($tingkat) {
                case 'RA':
                    $RA = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', 'RA');
                    })->get();
                    break;
                case '0':
                    $SD = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '0');
                    })->get();
                    break;
                case '1':
                    $SMP = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '1');
                    })->get();
                    break;
                case '2':
                    $SMA = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '2');
                    })->get();
                    break;
            }

            $jumlah = (object)['RA' => $tingkat == 'RA' ? $RA->count() : 0, 'SD' => $tingkat == '0' ? $SD->count() : 0, 'SMP' => $tingkat == '1' ? $SMP->count() : 0, 'SMA' => $tingkat == '2' ? $SMA->count() : 0];
            $total = (object)['RA' => $tingkat == 'RA' ? $RA->sum('jumlah_potongan') : 0, 'SD' => $tingkat == '0' ? $SD->sum('jumlah_potongan') : 0, 'SMP' => $tingkat == '1' ? $SMP->sum('jumlah_potongan') : 0, 'SMA' => $tingkat == '2' ? $SMA->sum('jumlah_potongan') : 0];
        }

        $dataPot = [];
        if ($data->isNotEmpty()) {
            if ($potongan != 'full') {
                $dataPot = DiscountTransaction::whereBelongsTo($data)
                    ->where('id_potongan', $potongan)
                    ->select(DB::raw('*, SUM(total) as total_pot'))
                    ->groupBy('id_potongan')
                    ->with(['discount'])
                    ->get();
            } else {
                $dataPot = DiscountTransaction::whereBelongsTo($data)
                    ->select(DB::raw('*, SUM(total) as total_pot'))
                    ->groupBy('id_potongan')
                    ->with(['discount'])
                    ->get();
            }
        }

        $filters = (object)['tingkat' => $request->tingkat, 'potongan' => $potongan, 'startDate' => $request->startDate, 'endDate' => $request->endDate];

        $listDiscount = Discount::all();

        return view('pages.report.discount', ([
            'data' => $data,
            'filters' => $filters,
            'jumlah' => $jumlah,
            'total' => $total,
            'listDiscount' => $listDiscount,
            'dataPotongan' => $dataPot,
        ]));
    }

    public function getDataDiscount(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $tingkat = $request->tingkat;
        $potongan = $request->potongan;


        if ($tingkat == 'full') {
            $dataTransaksi = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->get();
        } else {
            $dataTransaksi = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', $tingkat);
            })->get();
        }

        if ($dataTransaksi->isNotEmpty() && $potongan == 'full') {
            $data = DiscountTransaction::whereBelongsTo($dataTransaksi)
                ->with(['discount', 'transaction'])
                ->get();
        } elseif ($dataTransaksi->isNotEmpty() && $potongan != 'full') {
            $data = DiscountTransaction::whereBelongsTo($dataTransaksi)
                ->where('id_potongan', $potongan)
                ->with(['discount', 'transaction'])
                ->get();
        } else {
            $data = [];
        }

        $dataTable = collect([]);
        $no = 1;
        foreach ($data as $item) {
            if ($item->transaction != null) {
                $student = Student::find($item->transaction->id_siswa);

                $kelas = $student->kelas;
                $name = $student->nis . '/' . $student->nama;

                if ($student->tingkat == '0') {
                    $tingkat = '<span class="badge bg-danger">SD</span>';
                } elseif ($student->tingkat == '1') {
                    $tingkat = '<span class="badge bg-primary">SMP</span>';
                } elseif ($student->tingkat == '2') {
                    $tingkat = '<span class="badge bg-secondary">SMA</span>';
                } elseif ($student->tingkat == 'RA') {
                    $tingkat = '<span class="badge bg-info">RA</span>';
                }
            } else {
                $tingkat = '';
                $kelas = '';
                $name = '';
            }

            $tanggal = \Carbon\Carbon::parse($item->transaction->tgl_transaksi)->format('d M Y');
            $total = $this->currency($item->total);
            $no_bukti = '<a href="' . route('transactions.details', $item->transaction->id) . '" class="btn btn-success btn-sm d-flex w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" class="d-inline me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>    
                                ' . $item->transaction->no_bukti . '
                            </a>';

            $dataTable->push([
                'no' => $no,
                'no_bukti' => $no_bukti,
                'potongan' => $item->discount->nama,
                'name' => $name,
                'tingkat' => $tingkat,
                'kelas' => $kelas,
                'tanggal' => $tanggal,
                'tahun_ajaran' => $item->transaction->tahun_ajaran,
                'total' => $total,
            ]);
            $no++;
        }

        // dd($dataTable);

        return DataTables::of($dataTable)->escapeColumns([])->toJson();
    }

    public function resumeDiscount(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $tingkat = $request->tingkat;
        $potongan = $request->potongan;

        if ($tingkat == 'full') {
            $data = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->get();
        } else {

            switch ($tingkat) {
                case 'RA':
                    $data = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', 'RA');
                    })->get();
                    break;
                case '0':
                    $data = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '0');
                    })->get();
                    break;
                case '1':
                    $data = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '1');
                    })->get();
                    break;
                case '2':
                    $data = Transaction::whereBetween('tgl_transaksi', [$startDate, $endDate])->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '2');
                    })->get();
                    break;
            }
        }

        $dataPot = [];
        $dataTotalPot = [];
        if ($data->isNotEmpty()) {
            if ($potongan != 'full') {
                $dataTotalPot = DiscountTransaction::whereBelongsTo($data)
                    ->where('id_potongan', $potongan)
                    ->select(DB::raw('*, SUM(total) as total_pot'))
                    ->groupBy('id_potongan')
                    ->with(['discount', 'transaction'])
                    ->get();

                $dataPot = DiscountTransaction::whereBelongsTo($data)
                    ->where('id_potongan', $potongan)
                    ->with(['discount', 'transaction'])
                    ->get();
            } else {
                $dataTotalPot = DiscountTransaction::whereBelongsTo($data)
                    ->select(DB::raw('*, SUM(total) as total_pot'))
                    ->groupBy('id_potongan')
                    ->with(['discount', 'transaction'])
                    ->get();

                $dataPot = DiscountTransaction::whereBelongsTo($data)
                    ->with(['discount', 'transaction'])
                    ->get();
            }
        }

        $dataTable = [];
        $no = 1;
        foreach ($dataPot as $item) {
            if ($item->transaction != null) {
                $student = Student::find($item->transaction->id_siswa);

                $kelas = $student->kelas;
                $name = $student->nis . '/' . $student->nama;

                if ($student->tingkat == '0') {
                    $tingkat = 'SD';
                } elseif ($student->tingkat == '1') {
                    $tingkat = 'SMP';
                } elseif ($student->tingkat == '2') {
                    $tingkat = 'SMA';
                } elseif ($student->tingkat == 'RA') {
                    $tingkat = 'RA';
                }
            } else {
                $tingkat = '';
                $kelas = '';
                $name = '';
            }

            $tanggal = \Carbon\Carbon::parse($item->transaction->tgl_transaksi)->format('d M Y');
            $total = $this->currency($item->total);
            $no_bukti = $item->transaction->no_bukti;

            $dataTables = [
                'no' => $no,
                'no_bukti' => $no_bukti,
                'potongan' => $item->discount->nama,
                'name' => $name,
                'tingkat' => $tingkat,
                'kelas' => $kelas,
                'tanggal' => $tanggal,
                'tahun_ajaran' => $item->transaction->tahun_ajaran,
                'total' => $total,
            ];
            array_push($dataTable, $dataTables);
            $no++;
        }

        $filters = (object)['tingkat' => $request->tingkat, 'potongan' => $potongan, 'startDate' => $request->startDate, 'endDate' => $request->endDate];

        return view('pages.report.print.discount', ([
            'filters' => $filters,
            'data' => $data,
            'dataPotongan' => $dataPot,
            'dataTotalPot' => $dataTotalPot,
            'dataTable' => $dataTable,
        ]));
    }

    public function getDiagramDataMonthly(Request $request)
    {
        $month = date("m", strtotime($request->date));
        $year = date("Y", strtotime($request->date));

        $tingkat = $request->tingkat;
        if ($tingkat == 'full') {
            $data = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->get();
            $successTransaksi = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->get();

            $RA = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', 'RA');
            })->get();

            $SD = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', '0');
            })->get();

            $SMP = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', '1');
            })->get();

            $SMA = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', '2');
            })->get();

            $jumlah = (object)['RA' => $RA->count(), 'SD' => $SD->count(), 'SMP' => $SMP->count(), 'SMA' => $SMA->count(), 'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count()];
            $total = (object)['RA' => $RA->sum('total'), 'SD' => $SD->sum('total'), 'SMP' => $SMP->sum('total'), 'SMA' => $SMA->sum('total'), 'lainnya' => $successTransaksi->sum('jumlah_lainnya')];
        } else {
            $data = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', $tingkat);
            })->get();
            $successTransaksi = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', $tingkat);
            })->get();

            switch ($tingkat) {
                case 'RA':
                    $RA = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', 'RA');
                    })->get();
                    break;
                case '0':
                    $SD = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '0');
                    })->get();
                    break;
                case '1':
                    $SMP = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '1');
                    })->get();
                    break;
                case '2':
                    $SMA = Transaction::whereMonth('tgl_transaksi', $month)->whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '2');
                    })->get();
                    break;
            }

            $jumlah = (object)['RA' => $tingkat == 'RA' ? $RA->count() : 0, 'SD' => $tingkat == '0' ? $SD->count() : 0, 'SMP' => $tingkat == '1' ? $SMP->count() : 0, 'SMA' => $tingkat == '2' ? $SMA->count() : 0, 'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count()];
            $total = (object)['RA' => $tingkat == 'RA' ? $RA->sum('total') : 0, 'SD' => $tingkat == '0' ? $SD->sum('total') : 0, 'SMP' => $tingkat == '1' ? $SMP->sum('total') : 0, 'SMA' => $tingkat == '2' ? $SMA->sum('total') : 0, 'lainnya' => $successTransaksi->sum('jumlah_lainnya')];
        }

        $totalUS = 0;
        $totalUP = 0;
        $totalPotUS = 0;
        $totalPotUP = 0;
        foreach ($successTransaksi as $item) {
            $totalUS += $item->schoolFee->sum('total');
            $totalUP += $item->schoolDevFee->sum('total');

            if ($item->discounts->count() > 0) {
                foreach ($item->discounts as $disc) {
                    if ($disc->discount->jenis == 'Uang Sekolah') {
                        $totalPotUS += $disc->total;
                    } elseif ($disc->discount->jenis == 'Uang Pembangunan') {
                        $totalPotUP += $disc->total;
                    }
                }
            }
        }

        $transaksiRA = [];
        $transaksiSD = [];
        $transaksiSMP = [];
        $transaksiSMA = [];
        $d = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for ($i = 1; $i <= $d; $i++) {
            $transactions_RA = Transaction::whereDate('tgl_transaksi', $request->date . '-' . $i)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) use ($tingkat) {
                    $query->where('tingkat', 'RA');
                })->sum('total');

            if ($transactions_RA) {
                array_push($transaksiRA, intval($transactions_RA));
            } else {
                array_push($transaksiRA, 0);
            }

            $transactions_SD = Transaction::whereDate('tgl_transaksi', $request->date . '-' . $i)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) use ($tingkat) {
                    $query->where('tingkat', '0');
                })->sum('total');

            if ($transactions_SD) {
                array_push($transaksiSD, intval($transactions_SD));
            } else {
                array_push($transaksiSD, 0);
            }

            $transactions_SMP = Transaction::whereDate('tgl_transaksi', $request->date . '-' . $i)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) use ($tingkat) {
                    $query->where('tingkat', '1');
                })->sum('total');

            if ($transactions_SMP) {
                array_push($transaksiSMP, intval($transactions_SMP));
            } else {
                array_push($transaksiSMP, 0);
            }

            $transactions_SMA = Transaction::whereDate('tgl_transaksi', $request->date . '-' . $i)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) use ($tingkat) {
                    $query->where('tingkat', '2');
                })->sum('total');

            if ($transactions_SMA) {
                array_push($transaksiSMA, intval($transactions_SMA));
            } else {
                array_push($transaksiSMA, 0);
            }
        }
        // dd($transaksi);

        $totalTransaksi = $successTransaksi->sum('total');

        $data = [
            'totalTransaksi' => $totalTransaksi,
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => $totalUS,
            'total_potongan_us' => $totalPotUS,
            'total_uang_pembangunan' => $totalUP,
            'total_potongan_up' => $totalPotUP,
            'transaksiRA' => $transaksiRA,
            'transaksiSD' => $transaksiSD,
            'transaksiSMP' => $transaksiSMP,
            'transaksiSMA' => $transaksiSMA,
        ];

        return json_encode($data);
    }

    public function getDiagramDataSemester(Request $request)
    {

        $year = $request->date;
        if ($request->semester == 'Ganjil') {
            $start = Carbon::parse($year . '-01-01')->format('Y-m-d');
            $end = Carbon::parse($year . '-06-30')->format('Y-m-d');
            $i = 1;
            $max = 6;
        } else {
            $start = Carbon::parse($year . '-07-01')->format('Y-m-d');
            $end = Carbon::parse($year . '-12-31')->format('Y-m-d');
            $i = 7;
            $max = 12;
        }

        $tingkat = $request->tingkat;
        if ($tingkat == 'full') {
            $data = Transaction::whereBetween('tgl_transaksi', [$start, $end])->get();
            $successTransaksi = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->get();

            $RA = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', 'RA');
            })->get();

            $SD = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '0');
            })->get();

            $SMP = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '1');
            })->get();

            $SMA = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '2');
            })->get();

            $jumlah = (object)['RA' => $RA->count(), 'SD' => $SD->count(), 'SMP' => $SMP->count(), 'SMA' => $SMA->count(), 'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count()];
            $total = (object)['RA' => $RA->sum('total'), 'SD' => $SD->sum('total'), 'SMP' => $SMP->sum('total'), 'SMA' => $SMA->sum('total'), 'lainnya' => $successTransaksi->sum('jumlah_lainnya')];
        } else {
            $data = Transaction::whereBetween('tgl_transaksi', [$start, $end])->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', $tingkat);
            })->get();
            $successTransaksi = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', $tingkat);
            })->get();

            switch ($tingkat) {
                case 'RA':
                    $RA = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', 'RA');
                    })->get();
                    break;
                case '0':
                    $SD = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '0');
                    })->get();
                    break;
                case '1':
                    $SMP = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '1');
                    })->get();
                    break;
                case '2':
                    $SMA = Transaction::whereBetween('tgl_transaksi', [$start, $end])->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '2');
                    })->get();
                    break;
            }

            $jumlah = (object)['RA' => $tingkat == 'RA' ? $RA->count() : 0, 'SD' => $tingkat == '0' ? $SD->count() : 0, 'SMP' => $tingkat == '1' ? $SMP->count() : 0, 'SMA' => $tingkat == '2' ? $SMA->count() : 0, 'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count()];
            $total = (object)['RA' => $tingkat == 'RA' ? $RA->sum('total') : 0, 'SD' => $tingkat == '0' ? $SD->sum('total') : 0, 'SMP' => $tingkat == '1' ? $SMP->sum('total') : 0, 'SMA' => $tingkat == '2' ? $SMA->sum('total') : 0, 'lainnya' => $successTransaksi->sum('jumlah_lainnya')];
        }

        $totalUS = 0;
        $totalUP = 0;
        $totalPotUS = 0;
        $totalPotUP = 0;
        foreach ($successTransaksi as $item) {
            $totalUS += $item->schoolFee->sum('total');
            $totalUP += $item->schoolDevFee->sum('total');

            if ($item->discounts->count() > 0) {
                foreach ($item->discounts as $disc) {
                    if ($disc->discount->jenis == 'Uang Sekolah') {
                        $totalPotUS += $disc->total;
                    } elseif ($disc->discount->jenis == 'Uang Pembangunan') {
                        $totalPotUP += $disc->total;
                    }
                }
            }
        }

        $transaksiRA = [];
        $transaksiSD = [];
        $transaksiSMP = [];
        $transaksiSMA = [];
        for ($i; $i <= $max; $i++) {
            $transactions_RA = Transaction::whereMonth('tgl_transaksi', $i)
                ->whereYear('tgl_transaksi', $year)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', 'RA');
                })->sum('total');

            if ($transactions_RA) {
                array_push($transaksiRA, intval($transactions_RA));
            } else {
                array_push($transaksiRA, 0);
            }

            $transactions_SD = Transaction::whereMonth('tgl_transaksi', $i)
                ->whereYear('tgl_transaksi', $year)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', '0');
                })->sum('total');

            if ($transactions_SD) {
                array_push($transaksiSD, intval($transactions_SD));
            } else {
                array_push($transaksiSD, 0);
            }

            $transactions_SMP = Transaction::whereMonth('tgl_transaksi', $i)
                ->whereYear('tgl_transaksi', $year)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', '1');
                })->sum('total');

            if ($transactions_SMP) {
                array_push($transaksiSMP, intval($transactions_SMP));
            } else {
                array_push($transaksiSMP, 0);
            }

            $transactions_SMA = Transaction::whereMonth('tgl_transaksi', $i)
                ->whereYear('tgl_transaksi', $year)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) use ($tingkat) {
                    $query->where('tingkat', '2');
                })->sum('total');

            if ($transactions_SMA) {
                array_push($transaksiSMA, intval($transactions_SMA));
            } else {
                array_push($transaksiSMA, 0);
            }
        }
        // dd($transaksi);

        $totalTransaksi = $successTransaksi->sum('total');

        $data = [
            'data' => $data,
            'totalTransaksi' => $totalTransaksi,
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => $totalUS,
            'total_potongan_us' => $totalPotUS,
            'total_uang_pembangunan' => $totalUP,
            'total_potongan_up' => $totalPotUP,
            'transaksiRA' => $transaksiRA,
            'transaksiSD' => $transaksiSD,
            'transaksiSMP' => $transaksiSMP,
            'transaksiSMA' => $transaksiSMA,
        ];

        return json_encode($data);
    }

    public function getDiagramDataYearly(Request $request)
    {
        $year = $request->date;

        $tingkat = $request->tingkat;
        if ($tingkat == 'full') {
            $data = Transaction::whereYear('tgl_transaksi', $year)->get();
            $successTransaksi = Transaction::whereYear('tgl_transaksi', $year)->where('status', 'Success')->get();

            $RA = Transaction::whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', 'RA');
            })->get();

            $SD = Transaction::whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '0');
            })->get();

            $SMP = Transaction::whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '1');
            })->get();

            $SMA = Transaction::whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '2');
            })->get();

            $jumlah = (object)['RA' => $RA->count(), 'SD' => $SD->count(), 'SMP' => $SMP->count(), 'SMA' => $SMA->count(), 'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count()];
            $total = (object)['RA' => $RA->sum('total'), 'SD' => $SD->sum('total'), 'SMP' => $SMP->sum('total'), 'SMA' => $SMA->sum('total'), 'lainnya' => $successTransaksi->sum('jumlah_lainnya')];
        } else {
            $data = Transaction::whereYear('tgl_transaksi', $year)->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', $tingkat);
            })->get();
            $successTransaksi = Transaction::whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', $tingkat);
            })->get();

            switch ($tingkat) {
                case 'RA':
                    $RA = Transaction::whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', 'RA');
                    })->get();
                    break;
                case '0':
                    $SD = Transaction::whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '0');
                    })->get();
                    break;
                case '1':
                    $SMP = Transaction::whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '1');
                    })->get();
                    break;
                case '2':
                    $SMA = Transaction::whereYear('tgl_transaksi', $year)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '2');
                    })->get();
                    break;
            }

            $jumlah = (object)['RA' => $tingkat == 'RA' ? $RA->count() : 0, 'SD' => $tingkat == '0' ? $SD->count() : 0, 'SMP' => $tingkat == '1' ? $SMP->count() : 0, 'SMA' => $tingkat == '2' ? $SMA->count() : 0, 'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count()];
            $total = (object)['RA' => $tingkat == 'RA' ? $RA->sum('total') : 0, 'SD' => $tingkat == '0' ? $SD->sum('total') : 0, 'SMP' => $tingkat == '1' ? $SMP->sum('total') : 0, 'SMA' => $tingkat == '2' ? $SMA->sum('total') : 0, 'lainnya' => $successTransaksi->sum('jumlah_lainnya')];
        }

        $totalUS = 0;
        $totalUP = 0;
        $totalPotUS = 0;
        $totalPotUP = 0;
        foreach ($successTransaksi as $item) {
            $totalUS += $item->schoolFee->sum('total');
            $totalUP += $item->schoolDevFee->sum('total');

            if ($item->discounts->count() > 0) {
                foreach ($item->discounts as $disc) {
                    if ($disc->discount->jenis == 'Uang Sekolah') {
                        $totalPotUS += $disc->total;
                    } elseif ($disc->discount->jenis == 'Uang Pembangunan') {
                        $totalPotUP += $disc->total;
                    }
                }
            }
        }

        $transaksiRA = [];
        $transaksiSD = [];
        $transaksiSMP = [];
        $transaksiSMA = [];
        for ($i = 1; $i <= 12; $i++) {
            $transactions_RA = Transaction::whereMonth('tgl_transaksi', $i)
                ->whereYear('tgl_transaksi', $year)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', 'RA');
                })->sum('total');

            if ($transactions_RA) {
                array_push($transaksiRA, intval($transactions_RA));
            } else {
                array_push($transaksiRA, 0);
            }

            $transactions_SD = Transaction::whereMonth('tgl_transaksi', $i)
                ->whereYear('tgl_transaksi', $year)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', '0');
                })->sum('total');

            if ($transactions_SD) {
                array_push($transaksiSD, intval($transactions_SD));
            } else {
                array_push($transaksiSD, 0);
            }

            $transactions_SMP = Transaction::whereMonth('tgl_transaksi', $i)
                ->whereYear('tgl_transaksi', $year)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', '1');
                })->sum('total');

            if ($transactions_SMP) {
                array_push($transaksiSMP, intval($transactions_SMP));
            } else {
                array_push($transaksiSMP, 0);
            }

            $transactions_SMA = Transaction::whereMonth('tgl_transaksi', $i)
                ->whereYear('tgl_transaksi', $year)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) use ($tingkat) {
                    $query->where('tingkat', '2');
                })->sum('total');

            if ($transactions_SMA) {
                array_push($transaksiSMA, intval($transactions_SMA));
            } else {
                array_push($transaksiSMA, 0);
            }
        }
        // dd($transaksi);

        $totalTransaksi = $successTransaksi->sum('total');

        $data = [
            'totalTransaksi' => $totalTransaksi,
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => $totalUS,
            'total_potongan_us' => $totalPotUS,
            'total_uang_pembangunan' => $totalUP,
            'total_potongan_up' => $totalPotUP,
            'transaksiRA' => $transaksiRA,
            'transaksiSD' => $transaksiSD,
            'transaksiSMP' => $transaksiSMP,
            'transaksiSMA' => $transaksiSMA,
        ];

        return json_encode($data);
    }

    public function getDiagramDataSchoolYear(Request $request)
    {
        $schoolYear = $request->date;

        $tingkat = $request->tingkat;
        if ($tingkat == 'full') {
            $data = Transaction::where('tahun_ajaran', $schoolYear)->get();
            $successTransaksi = Transaction::where('tahun_ajaran', $schoolYear)->where('status', 'Success')->get();

            $RA = Transaction::where('tahun_ajaran', $schoolYear)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', 'RA');
            })->get();

            $SD = Transaction::where('tahun_ajaran', $schoolYear)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '0');
            })->get();

            $SMP = Transaction::where('tahun_ajaran', $schoolYear)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '1');
            })->get();

            $SMA = Transaction::where('tahun_ajaran', $schoolYear)->where('status', 'Success')->whereHas('student', function ($query) {
                $query->where('tingkat', '2');
            })->get();

            $jumlah = (object)['RA' => $RA->count(), 'SD' => $SD->count(), 'SMP' => $SMP->count(), 'SMA' => $SMA->count(), 'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count()];
            $total = (object)['RA' => $RA->sum('total'), 'SD' => $SD->sum('total'), 'SMP' => $SMP->sum('total'), 'SMA' => $SMA->sum('total'), 'lainnya' => $successTransaksi->sum('jumlah_lainnya')];
        } else {
            $data = Transaction::where('tahun_ajaran', $schoolYear)->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', $tingkat);
            })->get();
            $successTransaksi = Transaction::where('tahun_ajaran', $schoolYear)->where('status', 'Success')->whereHas('student', function ($query) use ($tingkat) {
                $query->where('tingkat', $tingkat);
            })->get();

            switch ($tingkat) {
                case 'RA':
                    $RA = Transaction::where('tahun_ajaran', $schoolYear)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', 'RA');
                    })->get();
                    break;
                case '0':
                    $SD = Transaction::where('tahun_ajaran', $schoolYear)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '0');
                    })->get();
                    break;
                case '1':
                    $SMP = Transaction::where('tahun_ajaran', $schoolYear)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '1');
                    })->get();
                    break;
                case '2':
                    $SMA = Transaction::where('tahun_ajaran', $schoolYear)->where('status', 'Success')->whereHas('student', function ($query) {
                        $query->where('tingkat', '2');
                    })->get();
                    break;
            }

            $jumlah = (object)['RA' => $tingkat == 'RA' ? $RA->count() : 0, 'SD' => $tingkat == '0' ? $SD->count() : 0, 'SMP' => $tingkat == '1' ? $SMP->count() : 0, 'SMA' => $tingkat == '2' ? $SMA->count() : 0, 'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count()];
            $total = (object)['RA' => $tingkat == 'RA' ? $RA->sum('total') : 0, 'SD' => $tingkat == '0' ? $SD->sum('total') : 0, 'SMP' => $tingkat == '1' ? $SMP->sum('total') : 0, 'SMA' => $tingkat == '2' ? $SMA->sum('total') : 0, 'lainnya' => $successTransaksi->sum('jumlah_lainnya')];
        }

        $totalUS = 0;
        $totalUP = 0;
        $totalPotUS = 0;
        $totalPotUP = 0;
        foreach ($successTransaksi as $item) {
            $totalUS += $item->schoolFee->sum('total');
            $totalUP += $item->schoolDevFee->sum('total');

            if ($item->discounts->count() > 0) {
                foreach ($item->discounts as $disc) {
                    if ($disc->discount->jenis == 'Uang Sekolah') {
                        $totalPotUS += $disc->total;
                    } elseif ($disc->discount->jenis == 'Uang Pembangunan') {
                        $totalPotUP += $disc->total;
                    }
                }
            }
        }

        $transaksiRA = [];
        $transaksiSD = [];
        $transaksiSMP = [];
        $transaksiSMA = [];
        $months = [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6];

        foreach ($months as $i) {
            $transactions_RA = Transaction::whereMonth('tgl_transaksi', $i)
                ->where('tahun_ajaran', $schoolYear)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', 'RA');
                })->sum('total');

            if ($transactions_RA) {
                array_push($transaksiRA, intval($transactions_RA));
            } else {
                array_push($transaksiRA, 0);
            }

            $transactions_SD = Transaction::whereMonth('tgl_transaksi', $i)
                ->where('tahun_ajaran', $schoolYear)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', '0');
                })->sum('total');

            if ($transactions_SD) {
                array_push($transaksiSD, intval($transactions_SD));
            } else {
                array_push($transaksiSD, 0);
            }

            $transactions_SMP = Transaction::whereMonth('tgl_transaksi', $i)
                ->where('tahun_ajaran', $schoolYear)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', '1');
                })->sum('total');

            if ($transactions_SMP) {
                array_push($transaksiSMP, intval($transactions_SMP));
            } else {
                array_push($transaksiSMP, 0);
            }

            $transactions_SMA = Transaction::whereMonth('tgl_transaksi', $i)
                ->where('tahun_ajaran', $schoolYear)
                ->where('status', 'Success')
                ->whereHas('student', function ($query) use ($tingkat) {
                    $query->where('tingkat', '2');
                })->sum('total');

            if ($transactions_SMA) {
                array_push($transaksiSMA, intval($transactions_SMA));
            } else {
                array_push($transaksiSMA, 0);
            }
        }
        // dd($transaksi);

        $totalTransaksi = $successTransaksi->sum('total');

        $data = [
            'totalTransaksi' => $totalTransaksi,
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => $totalUS,
            'total_potongan_us' => $totalPotUS,
            'total_uang_pembangunan' => $totalUP,
            'total_potongan_up' => $totalPotUP,
            'transaksiRA' => $transaksiRA,
            'transaksiSD' => $transaksiSD,
            'transaksiSMP' => $transaksiSMP,
            'transaksiSMA' => $transaksiSMA,
        ];

        return json_encode($data);
    }
}
