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
        $day = Carbon::parse($request->date)->toDateString();
        $tingkatFilter = $request->tingkat;

        // Base query transaksi di hari tsb
        $base = Transaction::query()
            ->whereDate('tgl_transaksi', $day)
            ->with([
                'student',
                'schoolFee',       // relasi
                'schoolDevFee',    // relasi
                'schoolMaintenanceFee',    // relasi
                'schoolEquipmentFee',    // relasi
                'discounts.discount',
            ]);

        if ($tingkatFilter !== 'full') {
            $base->whereHas('student', fn($q) => $q->where('tingkat', $tingkatFilter));
        }

        // Semua transaksi (success + failed) untuk table
        $data = (clone $base)->get();

        // hanya transaksi sukses
        $successTransaksi = (clone $base)->where('status', 'Success')->get();

        // Grouping per tingkat dari transaksi sukses (tanpa query RA/SD/SMP/SMA berulang)
        $grouped = $successTransaksi->groupBy(fn($t) => optional($t->student)->tingkat);

        $jumlah = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->count(),
            'SD'  => ($grouped['0']  ?? collect())->count(),
            'SMP' => ($grouped['1']  ?? collect())->count(),
            'SMA' => ($grouped['2']  ?? collect())->count(),
            'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count(),
        ];

        $total = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->sum('total'),
            'SD'  => ($grouped['0']  ?? collect())->sum('total'),
            'SMP' => ($grouped['1']  ?? collect())->sum('total'),
            'SMA' => ($grouped['2']  ?? collect())->sum('total'),
            'lainnya' => $successTransaksi->sum('jumlah_lainnya'),
        ];

        // Hitung total US/UP & potongan (tanpa N+1 karena sudah eager load)
        $totalUS = 0;
        $totalUP = 0;
        $totalUPP = 0;
        $totalUPK = 0;
        $totalPotUS = 0;
        $totalPotUP = 0;
        $totalPotUPP = 0;

        foreach ($successTransaksi as $trx) {
            $totalUS += $trx->schoolFee->sum('total');
            $totalUP += $trx->schoolDevFee->sum('total');
            $totalUPP += $trx->schoolMaintenanceFee->sum('total');
            $totalUPK += $trx->schoolEquipmentFee->sum('total');

            foreach ($trx->discounts as $disc) {
                $jenis = $disc->discount?->jenis;
                if ($jenis === 'Uang Sekolah') {
                    $totalPotUS += (int) $disc->total;
                } elseif ($jenis === 'Uang Pembangunan') {
                    $totalPotUP += (int) $disc->total;
                } elseif ($jenis === 'Uang Pemeliharaan dan Pengembangan') {
                    $totalPotUPP += (int) $disc->total;
                }
            }
        }

        $totalTransaksi = $successTransaksi->sum('total');

        $filters = (object)[
            'tingkat' => $tingkatFilter,
            'date' => $request->date,
        ];

        return view('pages.report.daily', [
            'data' => $data,
            'totalTransaksi' => $totalTransaksi,
            'filters' => $filters,
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => $totalUS,
            'total_potongan_us' => $totalPotUS,
            'total_uang_pembangunan' => $totalUP,
            'total_potongan_up' => $totalPotUP,
            'total_upp' => $totalUPP,
            'total_potongan_upp' => $totalPotUPP,
            'total_upk' => $totalUPK,
        ]);
    }


    /**
     * Show the data transaction for monthly.
     */
    public function monthly(Request $request)
    {
        // request->date dari <input type="month"> = "YYYY-MM"
        $dt = Carbon::createFromFormat('Y-m', $request->date);
        $start = $dt->copy()->startOfMonth()->startOfDay();
        $end   = $dt->copy()->endOfMonth()->endOfDay();

        $tingkatFilter = $request->tingkat;

        $base = Transaction::query()
            ->whereBetween('tgl_transaksi', [$start, $end])
            ->with([
                'student',
                'schoolFee',
                'schoolDevFee',
                'schoolMaintenanceFee',
                'schoolEquipmentFee',
                'discounts.discount',
            ]);

        if ($tingkatFilter !== 'full') {
            $base->whereHas('student', fn($q) => $q->where('tingkat', $tingkatFilter));
        }

        $data = (clone $base)->get();
        $successTransaksi = (clone $base)->where('status', 'Success')->get();

        $grouped = $successTransaksi->groupBy(fn($t) => optional($t->student)->tingkat);

        $jumlah = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->count(),
            'SD'  => ($grouped['0']  ?? collect())->count(),
            'SMP' => ($grouped['1']  ?? collect())->count(),
            'SMA' => ($grouped['2']  ?? collect())->count(),
            'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count(),
        ];

        $total = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->sum('total'),
            'SD'  => ($grouped['0']  ?? collect())->sum('total'),
            'SMP' => ($grouped['1']  ?? collect())->sum('total'),
            'SMA' => ($grouped['2']  ?? collect())->sum('total'),
            'lainnya' => $successTransaksi->sum('jumlah_lainnya'),
        ];

        $totalUS = 0;
        $totalUP = 0;
        $totalUPP = 0;
        $totalUPK = 0;
        $totalPotUS = 0;
        $totalPotUP = 0;
        $totalPotUPP = 0;

        foreach ($successTransaksi as $trx) {
            $totalUS  += $trx->schoolFee->sum('total');
            $totalUP  += $trx->schoolDevFee->sum('total');
            $totalUPP += $trx->schoolMaintenanceFee->sum('total');
            $totalUPK += $trx->schoolEquipmentFee->sum('total');

            foreach ($trx->discounts as $disc) {
                $jenis = $disc->discount?->jenis;
                if ($jenis === 'Uang Sekolah') $totalPotUS += (int) $disc->total;
                elseif ($jenis === 'Uang Pembangunan') $totalPotUP += (int) $disc->total;
                elseif ($jenis === 'Uang Pemeliharaan dan Pengembangan') $totalPotUPP += (int) $disc->total;
            }
        }

        $filters = (object)[
            'tingkat' => $tingkatFilter,
            'date' => $request->date, // tetap "YYYY-MM"
        ];

        return view('pages.report.monthly', [ // sebaiknya beda view, tapi kalau masih pakai daily view silakan
            'data' => $data,
            'totalTransaksi' => $successTransaksi->sum('total'),
            'filters' => $filters,
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => $totalUS,
            'total_potongan_us' => $totalPotUS,
            'total_uang_pembangunan' => $totalUP,
            'total_potongan_up' => $totalPotUP,
            'total_upp' => $totalUPP,
            'total_potongan_upp' => $totalPotUPP,
            'total_upk' => $totalUPK,
        ]);
    }


    public function semester(Request $request)
    {
        $year = (int) $request->date;
        $semester = $request->semester;
        $tingkatFilter = $request->tingkat;

        if ($semester === 'Ganjil') {
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end   = Carbon::create($year, 6, 30)->endOfDay();
        } else { // Genap
            $start = Carbon::create($year, 7, 1)->startOfDay();
            $end   = Carbon::create($year, 12, 31)->endOfDay();
        }

        $base = Transaction::query()
            ->whereBetween('tgl_transaksi', [$start, $end])
            ->with([
                'student',
                'schoolFee',
                'schoolDevFee',
                'schoolMaintenanceFee',
                'schoolEquipmentFee',
                'discounts.discount',
            ]);

        if ($tingkatFilter !== 'full') {
            $base->whereHas('student', fn($q) => $q->where('tingkat', $tingkatFilter));
        }

        $data = (clone $base)->get();
        $successTransaksi = (clone $base)->where('status', 'Success')->get();

        $grouped = $successTransaksi->groupBy(fn($t) => optional($t->student)->tingkat);

        $jumlah = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->count(),
            'SD'  => ($grouped['0']  ?? collect())->count(),
            'SMP' => ($grouped['1']  ?? collect())->count(),
            'SMA' => ($grouped['2']  ?? collect())->count(),
            'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count(),
        ];

        $total = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->sum('total'),
            'SD'  => ($grouped['0']  ?? collect())->sum('total'),
            'SMP' => ($grouped['1']  ?? collect())->sum('total'),
            'SMA' => ($grouped['2']  ?? collect())->sum('total'),
            'lainnya' => $successTransaksi->sum('jumlah_lainnya'),
        ];

        $totalUS = 0;
        $totalUP = 0;
        $totalUPP = 0;
        $totalUPK = 0;
        $totalPotUS = 0;
        $totalPotUP = 0;
        $totalPotUPP = 0;

        foreach ($successTransaksi as $trx) {
            $totalUS  += $trx->schoolFee->sum('total');
            $totalUP  += $trx->schoolDevFee->sum('total');
            $totalUPP += $trx->schoolMaintenanceFee->sum('total');
            $totalUPK += $trx->schoolEquipmentFee->sum('total');

            foreach ($trx->discounts as $disc) {
                $jenis = $disc->discount?->jenis;
                if ($jenis === 'Uang Sekolah') $totalPotUS += (int) $disc->total;
                elseif ($jenis === 'Uang Pembangunan') $totalPotUP += (int) $disc->total;
                elseif ($jenis === 'Uang Pemeliharaan dan Pengembangan') $totalPotUPP += (int) $disc->total;
            }
        }

        $filters = (object)[
            'tingkat' => $tingkatFilter,
            'date' => (string)$year,
            'semester' => $semester,
        ];

        // view boleh tetap monthly kalau layout sama
        return view('pages.report.semester', [
            'data' => $data,
            'totalTransaksi' => $successTransaksi->sum('total'),
            'filters' => $filters,
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => $totalUS,
            'total_potongan_us' => $totalPotUS,
            'total_uang_pembangunan' => $totalUP,
            'total_potongan_up' => $totalPotUP,
            'total_upp' => $totalUPP,
            'total_potongan_upp' => $totalPotUPP,
            'total_upk' => $totalUPK,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function yearly(Request $request)
    {
        $year = (int) $request->date;
        $tingkatFilter = $request->tingkat;

        $base = Transaction::query()
            ->whereYear('tgl_transaksi', $year)
            ->with([
                'student',
                'schoolFee',
                'schoolDevFee',
                'schoolMaintenanceFee',
                'schoolEquipmentFee',
                'discounts.discount',
            ]);

        if ($tingkatFilter !== 'full') {
            $base->whereHas('student', fn($q) => $q->where('tingkat', $tingkatFilter));
        }

        $data = (clone $base)->get();
        $successTransaksi = (clone $base)->where('status', 'Success')->get();

        $grouped = $successTransaksi->groupBy(fn($t) => optional($t->student)->tingkat);

        $jumlah = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->count(),
            'SD'  => ($grouped['0']  ?? collect())->count(),
            'SMP' => ($grouped['1']  ?? collect())->count(),
            'SMA' => ($grouped['2']  ?? collect())->count(),
            'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count(),
        ];

        $total = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->sum('total'),
            'SD'  => ($grouped['0']  ?? collect())->sum('total'),
            'SMP' => ($grouped['1']  ?? collect())->sum('total'),
            'SMA' => ($grouped['2']  ?? collect())->sum('total'),
            'lainnya' => $successTransaksi->sum('jumlah_lainnya'),
        ];

        $totalUS = $totalUP = $totalUPP = $totalUPK = 0;
        $totalPotUS = $totalPotUP = $totalPotUPP = 0;

        foreach ($successTransaksi as $trx) {
            $totalUS  += $trx->schoolFee->sum('total');
            $totalUP  += $trx->schoolDevFee->sum('total');
            $totalUPP += $trx->schoolMaintenanceFee->sum('total');
            $totalUPK += $trx->schoolEquipmentFee->sum('total');

            foreach ($trx->discounts as $disc) {
                $jenis = $disc->discount?->jenis;
                if ($jenis === 'Uang Sekolah') $totalPotUS += (int) $disc->total;
                elseif ($jenis === 'Uang Pembangunan') $totalPotUP += (int) $disc->total;
                elseif ($jenis === 'Uang Pemeliharaan dan Pengembangan') $totalPotUPP += (int) $disc->total;
            }
        }

        $filters = (object)[
            'tingkat' => $tingkatFilter,
            'date' => (string) $year,
        ];

        return view('pages.report.yearly', [
            'data' => $data,
            'totalTransaksi' => $successTransaksi->sum('total'),
            'filters' => $filters,
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => $totalUS,
            'total_potongan_us' => $totalPotUS,
            'total_uang_pembangunan' => $totalUP,
            'total_potongan_up' => $totalPotUP,
            'total_upp' => $totalUPP,
            'total_potongan_upp' => $totalPotUPP,
            'total_upk' => $totalUPK,
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function schoolYear(Request $request)
    {
        $tahunAjaran = $request->date;      // contoh: "2023/2024"
        $tingkatFilter = $request->tingkat;

        $base = Transaction::query()
            ->where('tahun_ajaran', $tahunAjaran)
            ->with([
                'student',
                'schoolFee',
                'schoolDevFee',
                'schoolMaintenanceFee',
                'schoolEquipmentFee',
                'discounts.discount',
            ]);

        if ($tingkatFilter !== 'full') {
            $base->whereHas('student', fn($q) => $q->where('tingkat', $tingkatFilter));
        }

        $data = (clone $base)->get();
        $successTransaksi = (clone $base)->where('status', 'Success')->get();

        $grouped = $successTransaksi->groupBy(fn($t) => optional($t->student)->tingkat);

        $jumlah = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->count(),
            'SD'  => ($grouped['0']  ?? collect())->count(),
            'SMP' => ($grouped['1']  ?? collect())->count(),
            'SMA' => ($grouped['2']  ?? collect())->count(),
            'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count(),
        ];

        $total = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->sum('total'),
            'SD'  => ($grouped['0']  ?? collect())->sum('total'),
            'SMP' => ($grouped['1']  ?? collect())->sum('total'),
            'SMA' => ($grouped['2']  ?? collect())->sum('total'),
            'lainnya' => $successTransaksi->sum('jumlah_lainnya'),
        ];

        $totalUS = $totalUP = $totalUPP = $totalUPK = 0;
        $totalPotUS = $totalPotUP = $totalPotUPP = 0;

        foreach ($successTransaksi as $trx) {
            $totalUS  += $trx->schoolFee->sum('total');
            $totalUP  += $trx->schoolDevFee->sum('total');
            $totalUPP += $trx->schoolMaintenanceFee->sum('total');
            $totalUPK += $trx->schoolEquipmentFee->sum('total');

            foreach ($trx->discounts as $disc) {
                $jenis = $disc->discount?->jenis;
                if ($jenis === 'Uang Sekolah') $totalPotUS += (int) $disc->total;
                elseif ($jenis === 'Uang Pembangunan') $totalPotUP += (int) $disc->total;
                elseif ($jenis === 'Uang Pemeliharaan dan Pengembangan') $totalPotUPP += (int) $disc->total;
            }
        }

        $filters = (object)[
            'tingkat' => $tingkatFilter,
            'date' => $tahunAjaran,
        ];

        $listSchoolYear = SchoolYear::all();

        return view('pages.report.schoolYear', [
            'data' => $data,
            'totalTransaksi' => $successTransaksi->sum('total'),
            'filters' => $filters,
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => $totalUS,
            'total_potongan_us' => $totalPotUS,
            'total_uang_pembangunan' => $totalUP,
            'total_potongan_up' => $totalPotUP,
            'total_upp' => $totalUPP,
            'total_potongan_upp' => $totalPotUPP,
            'total_upk' => $totalUPK,
            'listSchoolYear' => $listSchoolYear
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function getData(Request $request)
    {
        $type = $request->type;
        $class = $request->tingkat;

        $q = Transaction::query()->with('student');

        // filter tingkat (optional)
        if ($class !== 'full') {
            $q->whereHas('student', fn($sq) => $sq->where('tingkat', $class));
        }

        // filter berdasarkan type
        if ($type === 'daily') {
            $day = Carbon::parse($request->date)->toDateString();
            $q->whereDate('tgl_transaksi', $day);
        } elseif ($type === 'monthly') {
            $dt = Carbon::parse($request->date);
            $q->whereMonth('tgl_transaksi', $dt->month)
                ->whereYear('tgl_transaksi', $dt->year);
        } elseif ($type === 'yearly') {
            $q->whereYear('tgl_transaksi', $request->date);
        } elseif ($type === 'schoolYear') {
            $q->where('tahun_ajaran', $request->date)
                ->where('no_bukti', '!=', ''); // konsisten

        } elseif ($type === 'semester') {
            $year = $request->date;
            if ($request->semester === 'Ganjil') {
                $start = Carbon::parse($year . '-01-01')->toDateString();
                $end   = Carbon::parse($year . '-06-30')->toDateString();
            } else {
                $start = Carbon::parse($year . '-07-01')->toDateString();
                $end   = Carbon::parse($year . '-12-31')->toDateString();
            }
            $q->whereBetween('tgl_transaksi', [$start, $end]);
        } else {
            $q->where('no_bukti', '!=', ''); // seperti kode kamu
        }

        $data = $q->get();

        $dataTable = collect([]);
        $no = 1;

        foreach ($data as $item) {
            $student = $item->student;

            if ($student) {
                $kelas = $student->kelas;
                $name = $student->nis . '/' . $student->nama;

                $t = $student->tingkat;
                $tingkatBadge =
                    $t === '0' ? '<span class="badge bg-danger">SD</span>' : ($t === '1' ? '<span class="badge bg-primary">SMP</span>' : ($t === '2' ? '<span class="badge bg-secondary">SMA</span>' : ($t === 'RA' ? '<span class="badge bg-info">RA</span>' : '')));
            } else {
                $tingkatBadge = '';
                $kelas = '';
                $name = '';
            }

            $print = ($item->status === 'Success')
                ? '<a href="' . route('transactions.print', $item->id) . '" class="btn btn-warning btn-sm" title="Cetak" target="_blank"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg></a>'
                : '';

            $statusBadge = $item->status === 'Success'
                ? '<span class="badge bg-success">Success</span>'
                : ($item->status === 'Cancel'
                    ? '<span class="badge bg-warning">Cancelled</span>'
                    : '<span class="badge bg-danger">Failed</span>');

            $tanggal = Carbon::parse($item->tgl_transaksi)->format('d M Y');
            $total = $this->currency($item->total);

            $noBuktiText = $item->no_bukti ?? '-';
            $no_bukti = '<a href="' . route('transactions.details', $item->id) . '" class="btn btn-success btn-sm d-flex w-100" title="Detail"><svg xmlns="http://www.w3.org/2000/svg" class="d-inline me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>   ' . $noBuktiText . '</a>';

            $dataTable->push([
                'no' => $no,
                'no_bukti' => $no_bukti,
                'name' => $name,
                'tingkat' => $tingkatBadge,
                'kelas' => $kelas,
                'tanggal' => $tanggal,
                'tahun_ajaran' => $item->tahun_ajaran,
                'total' => $total,
                'status' => $statusBadge,
                'metode' => $item->jenis,
                'print' => $print,
            ]);

            $no++;
        }

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

        if ($tingkat == 'full') {
            $data = Student::where('status', 1)->whereNotNull('bulan_spp_terakhir')->where('bulan_spp_terakhir', '<', Carbon::now()->format('Y-m-d'))->get();
        } else {
            $data = Student::where('status', 1)->whereNotNull('bulan_spp_terakhir')->where('tingkat', $tingkat)->where('bulan_spp_terakhir', '<', Carbon::now()->format('Y-m-d'))->get();
        }

        $jumlah = (object)['RA' => 0, 'SD' => 0, 'SMP' => 0, 'SMA' => 0,];
        $total = (object)['RA' => 0, 'SD' => 0, 'SMP' => 0, 'SMA' => 0];

        $fees = Fee::get()->keyBy('tahun_angkatan');

        foreach ($data as $item) {
            $schoolFeeAmount = $fees->get($item->tahun_angkatan);

            if ($item->kelas == 'RA') {
                $kelasFee = 'ra';
            } else {
                $kelasFee = 'kelas_' . $item->kelas;
            }

            $monthlyFee = $schoolFeeAmount ? intval($schoolFeeAmount[$kelasFee] ?? 0) : 0;

            if ($item->bulan_spp_terakhir != null && $item->bulan_spp_terakhir != 0 && Carbon::parse($item->bulan_spp_terakhir)->format('Y-m') != Carbon::now()->format('Y-m')) {
                $arrears = $this->getArrearsMonths($item->bulan_spp_terakhir);
                $totalArrears = count($arrears) * $monthlyFee;
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
        $tingkatFilter = $request->tingkat;

        $query = Student::where('status', 1)
            ->whereNotNull('bulan_spp_terakhir')
            ->whereDate('bulan_spp_terakhir', '<', Carbon::today());


        if ($tingkatFilter !== 'full') {
            $query->where('tingkat', $tingkatFilter);
        }

        $data = $query->get();

        // preload fee to avoid N+1
        $angkatanList = $data->pluck('tahun_angkatan')->unique()->values();
        $feesByAngkatan = Fee::whereIn('tahun_angkatan', $angkatanList)->get()->keyBy('tahun_angkatan');

        $dataTable = collect([]);
        $no = 1;

        foreach ($data as $item) {
            $feeRow = $feesByAngkatan->get($item->tahun_angkatan);

            $kelasFee = ($item->kelas === 'RA') ? 'ra' : 'kelas_' . $item->kelas;

            $monthlyFee = $feeRow ? (int)($feeRow->{$kelasFee} ?? 0) : 0;

            // badge tingkat (jangan pakai $tingkatFilter)
            if ($item->tingkat === '0') {
                $tingkatBadge = '<span class="badge bg-danger">SD</span>';
            } elseif ($item->tingkat === '1') {
                $tingkatBadge = '<span class="badge bg-primary">SMP</span>';
            } elseif ($item->tingkat === '2') {
                $tingkatBadge = '<span class="badge bg-secondary">SMA</span>';
            } elseif ($item->tingkat === 'RA') {
                $tingkatBadge = '<span class="badge bg-info">RA</span>';
            } else {
                $tingkatBadge = '';
            }

            $nama = $item->nama . ' ' . $tingkatBadge;
            $kelas = $item->kelas . '/' . $item->grup;

            // hitung tunggakan (per hari ini)
            $totalRaw = 0;
            if ($item->bulan_spp_terakhir && $monthlyFee > 0) {
                // FIX compare year-month (optional, tapi aman)
                if (Carbon::parse($item->bulan_spp_terakhir)->format('Y-m') !== Carbon::now()->format('Y-m')) {
                    $arrears = $this->getArrearsMonths($item->bulan_spp_terakhir);
                    $cnt = count($arrears);

                    if ($cnt > 0) {
                        $bulan = $cnt > 1
                            ? Carbon::parse($arrears[0])->format('M Y') . '-' . Carbon::parse($arrears[$cnt - 1])->format('M Y')
                            : Carbon::parse($arrears[0])->format('M Y');

                        // warna by range
                        if ($cnt === 1) $color = 'primary';
                        elseif ($cnt <= 3) $color = 'warning';
                        elseif ($cnt <= 5) $color = 'danger';
                        else $color = 'dark';

                        $bulantext = '<br/><span class="badge bg-' . $color . '">' . $bulan . '</span>';
                        $tunggakan = $cnt . ' Bulan ' . $bulantext;

                        $totalRaw = $cnt * $monthlyFee;
                    } else {
                        $tunggakan = 'Belum ada transaksi';
                    }
                } else {
                    $tunggakan = 'Belum ada transaksi';
                }
            } else {
                $tunggakan = 'Belum ada transaksi';
            }


            $total = $this->currency($totalRaw);
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
        $month = $request->date; // tidak dipakai, tapi tetap dikirim ke view

        // base query (per hari ini)
        $baseQuery = Student::where('status', 1)
            ->whereNotNull('bulan_spp_terakhir')
            ->whereDate('bulan_spp_terakhir', '<', Carbon::today())
            ->orderBy('tingkat', 'asc')
            ->orderBy('kelas', 'asc')
            ->with('transactions');

        if ($tingkat !== 'full') {
            $baseQuery->where('tingkat', $tingkat);
        }

        // data utama untuk print
        $data = $baseQuery->get();

        // dipakai untuk statistik (konsisten dengan filter)
        $students = $data;

        // preload Fee biar tidak N+1
        $angkatanList = $data->pluck('tahun_angkatan')->unique()->values();
        $feesByAngkatan = Fee::whereIn('tahun_angkatan', $angkatanList)->get()->keyBy('tahun_angkatan');

        // total US: ambil student_id list, sum langsung di transaksi (lebih aman daripada whereBelongsTo koleksi)
        $studentIds = $students->pluck('id')->all();

        $transactionsRA  = Transaction::whereIn('id_siswa', $studentIds)->where('status', 'Success')
            ->whereHas('student', fn($q) => $q->where('tingkat', 'RA'))
            ->sum('jumlah_us');

        $transactionsSD  = Transaction::whereIn('id_siswa', $studentIds)->where('status', 'Success')
            ->whereHas('student', fn($q) => $q->where('tingkat', '0'))
            ->sum('jumlah_us');

        $transactionsSMP = Transaction::whereIn('id_siswa', $studentIds)->where('status', 'Success')
            ->whereHas('student', fn($q) => $q->where('tingkat', '1'))
            ->sum('jumlah_us');

        $transactionsSMA = Transaction::whereIn('id_siswa', $studentIds)->where('status', 'Success')
            ->whereHas('student', fn($q) => $q->where('tingkat', '2'))
            ->sum('jumlah_us');

        $jumlah = (object)['RA' => 0, 'SD' => 0, 'SMP' => 0, 'SMA' => 0];
        $total  = (object)['RA' => 0, 'SD' => 0, 'SMP' => 0, 'SMA' => 0];

        $siswa = (object)[
            'RA'  => $students->where('tingkat', 'RA')->count(),
            'SD'  => $students->where('tingkat', '0')->count(),
            'SMP' => $students->where('tingkat', '1')->count(),
            'SMA' => $students->where('tingkat', '2')->count(),
        ];

        $totalUS = (object)[
            'RA'  => $transactionsRA,
            'SD'  => $transactionsSD,
            'SMP' => $transactionsSMP,
            'SMA' => $transactionsSMA,
        ];

        foreach ($data as $key => $item) {
            $feeRow = $feesByAngkatan->get($item->tahun_angkatan); // bisa null

            $kelasFee = ($item->kelas === 'RA') ? 'ra' : 'kelas_' . $item->kelas;

            $uangSekolah = $feeRow ? (int)($feeRow->{$kelasFee} ?? 0) : 0;

            // hitung arrears (fix year-month)
            $totalArrears = 0;
            $bulan = '-';
            $jumlahBulan = 0;

            if ($item->bulan_spp_terakhir && $uangSekolah > 0) {
                // optional: bandingkan Y-m supaya tidak ke-skip beda tahun
                if (Carbon::parse($item->bulan_spp_terakhir)->format('Y-m') !== Carbon::now()->format('Y-m')) {
                    $arrears = $this->getArrearsMonths($item->bulan_spp_terakhir);
                    $jumlahBulan = count($arrears);

                    if ($jumlahBulan > 0) {
                        $totalArrears = $jumlahBulan * $uangSekolah;
                        $bulan = $jumlahBulan > 1
                            ? Carbon::parse($arrears[0])->format('M Y') . '-' . Carbon::parse($arrears[$jumlahBulan - 1])->format('M Y')
                            : Carbon::parse($arrears[0])->format('M Y');
                    }
                }
            }

            $jumlah_up_paid = $item->transactions->where('status', 'Success')->sum('jumlah_up');

            // inject tambahan field untuk view
            $data[$key]['uang_sekolah'] = $uangSekolah;
            $data[$key]['bulan'] = $bulan;
            $data[$key]['total_tunggakan'] = $totalArrears;
            $data[$key]['jumlah_bulan'] = $jumlahBulan;

            if ($item->tingkat === '0') {
                $jumlah->SD += 1;
                $total->SD += $totalArrears;
                $pembangunan = $feeRow ? (int)($feeRow->pembangunan_sd ?? 0) : 0;
                $data[$key]['jumlah_up'] = $pembangunan - $jumlah_up_paid;
            } elseif ($item->tingkat === '1') {
                $jumlah->SMP += 1;
                $total->SMP += $totalArrears;
                $pembangunan = $feeRow ? (int)($feeRow->pembangunan_smp ?? 0) : 0;
                $data[$key]['jumlah_up'] = $pembangunan - $jumlah_up_paid;
            } elseif ($item->tingkat === '2') {
                $jumlah->SMA += 1;
                $total->SMA += $totalArrears;
                $pembangunan = $feeRow ? (int)($feeRow->pembangunan_sma ?? 0) : 0;
                $data[$key]['jumlah_up'] = $pembangunan - $jumlah_up_paid;
            } elseif ($item->tingkat === 'RA') {
                $jumlah->RA += 1;
                $total->RA += $totalArrears;
                $pembangunan = $feeRow ? (int)($feeRow->pembangunan_ra ?? 0) : 0;
                $data[$key]['jumlah_up'] = $pembangunan - $jumlah_up_paid;
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

        // base transaksi sukses
        $tx = Transaction::query()
            ->where('status', 'Success')
            ->with('student');

        // apply date filter
        if ($type === 'daily') {
            $day = Carbon::parse($request->date)->toDateString();
            $tx->whereDate('tgl_transaksi', $day);
        } elseif ($type === 'monthly') {
            $dt = Carbon::parse($request->date);
            $tx->whereMonth('tgl_transaksi', $dt->month)->whereYear('tgl_transaksi', $dt->year);
        } elseif ($type === 'yearly') {
            $tx->whereYear('tgl_transaksi', $request->date);
        } elseif ($type === 'schoolYear') {
            $tx->where('tahun_ajaran', $request->date);
        } elseif ($type === 'semester') {
            $year = $request->date;
            if ($request->semester === 'Ganjil') {
                $start = Carbon::parse($year . '-01-01')->toDateString();
                $end   = Carbon::parse($year . '-06-30')->toDateString();
            } else {
                $start = Carbon::parse($year . '-07-01')->toDateString();
                $end   = Carbon::parse($year . '-12-31')->toDateString();
            }
            $tx->whereBetween('tgl_transaksi', [$start, $end]);
        }

        $success = $tx->get();

        // group by tingkat
        $g = $success->groupBy(fn($t) => optional($t->student)->tingkat);
        $RAc  = $g['RA'] ?? collect();
        $SDc  = $g['0']  ?? collect();
        $SMPc = $g['1']  ?? collect();
        $SMAc = $g['2']  ?? collect();

        $jumlah = (object)[
            'RA' => $RAc->count(),
            'SD' => $SDc->count(),
            'SMP' => $SMPc->count(),
            'SMA' => $SMAc->count(),
        ];

        $total = (object)[
            'RA' => $RAc->sum('total'),
            'SD' => $SDc->sum('total'),
            'SMP' => $SMPc->sum('total'),
            'SMA' => $SMAc->sum('total'),
        ];

        // summary metode/komponen (pakai collection yang sudah grouped)
        $RA  = (object)['manualUS' => $RAc->where('jenis', 'Manual')->sum('jumlah_us'), 'mbankingUS' => $RAc->where('jenis', 'M-Banking')->sum('jumlah_us'), 'UP' => $RAc->sum('jumlah_up'), 'Pot' => $RAc->sum('jumlah_potongan'), 'Lain' => $RAc->sum('jumlah_lainnya')];
        $SD  = (object)['manualUS' => $SDc->where('jenis', 'Manual')->sum('jumlah_us'), 'mbankingUS' => $SDc->where('jenis', 'M-Banking')->sum('jumlah_us'), 'UP' => $SDc->sum('jumlah_up'), 'Pot' => $SDc->sum('jumlah_potongan'), 'Lain' => $SDc->sum('jumlah_lainnya')];
        $SMP = (object)['manualUS' => $SMPc->where('jenis', 'Manual')->sum('jumlah_us'), 'mbankingUS' => $SMPc->where('jenis', 'M-Banking')->sum('jumlah_us'), 'UP' => $SMPc->sum('jumlah_up'), 'Pot' => $SMPc->sum('jumlah_potongan'), 'Lain' => $SMPc->sum('jumlah_lainnya')];
        $SMA = (object)['manualUS' => $SMAc->where('jenis', 'Manual')->sum('jumlah_us'), 'mbankingUS' => $SMAc->where('jenis', 'M-Banking')->sum('jumlah_us'), 'UP' => $SMAc->sum('jumlah_up'), 'Pot' => $SMAc->sum('jumlah_potongan'), 'Lain' => $SMAc->sum('jumlah_lainnya')];

        // Potongan per tingkat (aggregate SQL aman)
        $potByTingkat = collect([
            'RA' => collect(),
            '0' => collect(),
            '1' => collect(),
            '2' => collect()
        ]);

        if ($success->isNotEmpty()) {
            $ids = $success->pluck('id')->all();

            $rows = DiscountTransaction::query()
                ->join('transaksi', 'transaksi.id', '=', 'transaksi_potongan.id_transaksi') // sesuaikan nama table/FK
                ->join('siswa', 'siswa.id', '=', 'transaksi.id_siswa') // sesuaikan FK
                ->whereIn('transaksi_potongan.id_transaksi', $ids)
                ->selectRaw('siswa.tingkat as tingkat, transaksi_potongan.id_potongan, SUM(transaksi_potongan.total) as total_pot')
                ->groupBy('siswa.tingkat', 'transaksi_potongan.id_potongan')
                ->with('discount')
                ->get();

            foreach ($rows as $r) {
                $potByTingkat[$r->tingkat] = ($potByTingkat[$r->tingkat] ?? collect())->push($r);
            }
        }

        $potRA  = $potByTingkat['RA'] ?? collect();
        $potSD  = $potByTingkat['0']  ?? collect();
        $potSMP = $potByTingkat['1']  ?? collect();
        $potSMA = $potByTingkat['2']  ?? collect();

        $filters = (object)['type' => $request->type, 'date' => $request->date];

        return view('pages.report.print.daily', [
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
        ]);
    }


    public function discount(Request $request)
    {
        $startDate = $request->startDate;
        $endDate   = $request->endDate;
        $tingkat   = $request->tingkat;   // 'full' / 'RA' / '0' / '1' / '2'
        $potongan  = $request->potongan;  // 'full' / id_potongan

        // Base query transaksi sukses
        $txQuery = Transaction::query()
            ->whereBetween('tgl_transaksi', [$startDate, $endDate])
            ->where('status', 'Success')
            ->whereHas('discounts')
            ->with('student'); // biar bisa grouping di memory

        if ($tingkat !== 'full') {
            $txQuery->whereHas('student', fn($q) => $q->where('tingkat', $tingkat));
        }

        $data = $txQuery->get();

        // Hitung jumlah & total potongan per tingkat (tanpa query berulang)
        $grouped = $data->groupBy(fn($t) => optional($t->student)->tingkat);

        $jumlah = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->count(),
            'SD'  => ($grouped['0']  ?? collect())->count(),
            'SMP' => ($grouped['1']  ?? collect())->count(),
            'SMA' => ($grouped['2']  ?? collect())->count(),
        ];

        $total = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->sum('jumlah_potongan'),
            'SD'  => ($grouped['0']  ?? collect())->sum('jumlah_potongan'),
            'SMP' => ($grouped['1']  ?? collect())->sum('jumlah_potongan'),
            'SMA' => ($grouped['2']  ?? collect())->sum('jumlah_potongan'),
        ];

        // Potongan summary (lebih aman & efisien)
        $dataPot = collect();
        if ($data->isNotEmpty()) {
            $dataPotQuery = DiscountTransaction::query()
                ->select([
                    'id_potongan',
                    DB::raw('SUM(total) as total_pot'),
                ])
                ->with('discount')
                ->whereHas('transaction', function ($q) use ($startDate, $endDate, $tingkat) {
                    $q->whereBetween('tgl_transaksi', [$startDate, $endDate])
                        ->where('status', 'Success');

                    if ($tingkat !== 'full') {
                        $q->whereHas('student', fn($sq) => $sq->where('tingkat', $tingkat));
                    }
                })
                ->groupBy('id_potongan');

            if ($potongan !== 'full') {
                $dataPotQuery->where('id_potongan', $potongan);
            }

            $dataPot = $dataPotQuery->get();
        }

        $filters = (object)[
            'tingkat'   => $tingkat,
            'potongan'  => $potongan,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ];

        $listDiscount = Discount::all();

        return view('pages.report.discount', [
            'data' => $data,
            'filters' => $filters,
            'jumlah' => $jumlah,
            'total' => $total,
            'listDiscount' => $listDiscount,
            'dataPotongan' => $dataPot,
        ]);
    }

    public function getDataDiscount(Request $request)
    {
        $startDate = $request->startDate;
        $endDate   = $request->endDate;
        $tingkatFilter = $request->tingkat;
        $potongan  = $request->potongan;

        $query = DiscountTransaction::query()
            ->with(['discount', 'transaction.student'])
            ->whereHas('transaction', function ($q) use ($startDate, $endDate, $tingkatFilter) {
                $q->whereBetween('tgl_transaksi', [$startDate, $endDate])
                    ->where('status', 'Success');

                if ($tingkatFilter !== 'full') {
                    $q->whereHas('student', fn($sq) => $sq->where('tingkat', $tingkatFilter));
                }
            });

        if ($potongan !== 'full') {
            $query->where('id_potongan', $potongan);
        }

        $data = $query->get();

        $dataTable = collect([]);
        $no = 1;

        foreach ($data as $item) {
            $tx = $item->transaction;
            $student = $tx?->student;

            $kelas = $student?->kelas ?? '-';
            $name  = $student ? ($student->nis . '/' . $student->nama) : '-';

            // badge tingkat
            $tingkatBadge = '';
            $t = $student?->tingkat;
            if ($t === '0') $tingkatBadge = '<span class="badge bg-danger">SD</span>';
            elseif ($t === '1') $tingkatBadge = '<span class="badge bg-primary">SMP</span>';
            elseif ($t === '2') $tingkatBadge = '<span class="badge bg-secondary">SMA</span>';
            elseif ($t === 'RA') $tingkatBadge = '<span class="badge bg-info">RA</span>';

            $tanggal = $tx?->tgl_transaksi
                ? Carbon::parse($tx->tgl_transaksi)->format('d M Y')
                : '-';

            $total = $this->currency((int)($item->total ?? 0));

            $noBuktiText = $tx?->no_bukti ?? '-';
            $noBuktiLink = $tx
                ? '<a href="' . route('transactions.details', $tx->id) . '" class="btn btn-success btn-sm d-flex w-100" title="Detail"><svg xmlns="http://www.w3.org/2000/svg" class="d-inline me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>' . $noBuktiText . '</a>'
                : $noBuktiText;

            $dataTable->push([
                'no' => $no,
                'no_bukti' => $noBuktiLink,
                'potongan' => $item->discount?->nama ?? '-',
                'name' => $name,
                'tingkat' => $tingkatBadge,
                'kelas' => $kelas,
                'tanggal' => $tanggal,
                'tahun_ajaran' => $tx?->tahun_ajaran ?? '-',
                'total' => $total,
            ]);

            $no++;
        }

        return DataTables::of($dataTable)->escapeColumns([])->toJson();
    }

    public function resumeDiscount(Request $request)
    {
        $startDate = $request->startDate;
        $endDate   = $request->endDate;
        $tingkatFilter = $request->tingkat;
        $potongan  = $request->potongan;

        // (opsional) transaksi base, kalau view masih butuh $data
        $txQuery = Transaction::query()
            ->whereBetween('tgl_transaksi', [$startDate, $endDate])
            ->whereHas('discounts')
            ->where('status', 'Success');

        if ($tingkatFilter !== 'full') {
            $txQuery->whereHas('student', fn($q) => $q->where('tingkat', $tingkatFilter));
        }

        $data = $txQuery->get();

        // Data detail potongan (untuk table print)
        $potQuery = DiscountTransaction::query()
            ->with(['discount', 'transaction.student'])
            ->whereHas('transaction', function ($q) use ($startDate, $endDate, $tingkatFilter) {
                $q->whereBetween('tgl_transaksi', [$startDate, $endDate])
                    ->where('status', 'Success');

                if ($tingkatFilter !== 'full') {
                    $q->whereHas('student', fn($sq) => $sq->where('tingkat', $tingkatFilter));
                }
            });

        if ($potongan !== 'full') {
            $potQuery->where('id_potongan', $potongan);
        }

        $dataPot = $potQuery->get();

        // Total per potongan (untuk resume)
        $totalPotQuery = DiscountTransaction::query()
            ->select([
                'id_potongan',
                DB::raw('SUM(total) as total_pot'),
            ])
            ->with('discount')
            ->whereHas('transaction', function ($q) use ($startDate, $endDate, $tingkatFilter) {
                $q->whereBetween('tgl_transaksi', [$startDate, $endDate])
                    ->where('status', 'Success');

                if ($tingkatFilter !== 'full') {
                    $q->whereHas('student', fn($sq) => $sq->where('tingkat', $tingkatFilter));
                }
            })
            ->groupBy('id_potongan');

        if ($potongan !== 'full') {
            $totalPotQuery->where('id_potongan', $potongan);
        }

        $dataTotalPot = $totalPotQuery->get();

        // Build dataTable untuk print
        $dataTable = [];
        $no = 1;

        foreach ($dataPot as $item) {
            $tx = $item->transaction;
            $student = $tx?->student;

            $kelas = $student?->kelas ?? '-';
            $name  = $student ? ($student->nis . '/' . $student->nama) : '-';

            $t = $student?->tingkat;
            $tingkatText = $t === '0' ? 'SD' : ($t === '1' ? 'SMP' : ($t === '2' ? 'SMA' : ($t === 'RA' ? 'RA' : '-')));

            $tanggal = $tx?->tgl_transaksi ? Carbon::parse($tx->tgl_transaksi)->format('d M Y') : '-';
            $total   = $this->currency((int)($item->total ?? 0));
            $no_bukti = $tx?->no_bukti ?? '-';

            $dataTable[] = [
                'no' => $no,
                'no_bukti' => $no_bukti,
                'potongan' => $item->discount?->nama ?? '-',
                'name' => $name,
                'tingkat' => $tingkatText,
                'kelas' => $kelas,
                'tanggal' => $tanggal,
                'tahun_ajaran' => $tx?->tahun_ajaran ?? '-',
                'total' => $total,
            ];

            $no++;
        }

        $filters = (object)[
            'tingkat' => $tingkatFilter,
            'potongan' => $potongan,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return view('pages.report.print.discount', [
            'filters' => $filters,
            'data' => $data,
            'dataPotongan' => $dataPot,
            'dataTotalPot' => $dataTotalPot,
            'dataTable' => $dataTable,
        ]);
    }


    public function getDiagramDataMonthly(Request $request)
    {
        $month = (int) date("m", strtotime($request->date)); // request->date format: YYYY-MM
        $year  = (int) date("Y", strtotime($request->date));

        $tingkatFilter = $request->tingkat;

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end   = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        // Base query transaksi sukses bulan tsb (untuk statistik & total US/UP/potongan)
        $successQ = Transaction::query()
            ->whereBetween('tgl_transaksi', [$start, $end])
            ->where('status', 'Success')
            ->with([
                'student',
                'schoolFee',
                'schoolDevFee',
                'schoolMaintenanceFee',
                'schoolEquipmentFee',
                'discounts.discount',
            ]);

        if ($tingkatFilter !== 'full') {
            $successQ->whereHas('siswa', fn($q) => $q->where('tingkat', $tingkatFilter));
        }

        $successTransaksi = $successQ->get();

        // jumlah & total per tingkat (tanpa query RA/SD/SMP/SMA)
        $grouped = $successTransaksi->groupBy(fn($t) => optional($t->student)->tingkat);

        $jumlah = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->count(),
            'SD'  => ($grouped['0']  ?? collect())->count(),
            'SMP' => ($grouped['1']  ?? collect())->count(),
            'SMA' => ($grouped['2']  ?? collect())->count(),
            'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count(),
        ];

        $total = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->sum('total'),
            'SD'  => ($grouped['0']  ?? collect())->sum('total'),
            'SMP' => ($grouped['1']  ?? collect())->sum('total'),
            'SMA' => ($grouped['2']  ?? collect())->sum('total'),
            'lainnya' => $successTransaksi->sum('jumlah_lainnya'),
        ];

        // total US/UP & potongan (tanpa N+1 karena with)
        $totalUS = 0;
        $totalUP = 0;
        $totalUPP = 0;
        $totalUPK = 0;
        $totalPotUS = 0;
        $totalPotUP = 0;
        $totalPotUPP = 0;
        foreach ($successTransaksi as $trx) {
            $totalUS += $trx->schoolFee->sum('total');
            $totalUP += $trx->schoolDevFee->sum('total');
            $totalUPP += $trx->schoolMaintenanceFee->sum('total');
            $totalUPK += $trx->schoolEquipmentFee->sum('total');

            foreach ($trx->discounts as $disc) {
                $jenis = $disc->discount?->jenis;
                if ($jenis === 'Uang Sekolah') $totalPotUS += (int) $disc->total;
                elseif ($jenis === 'Uang Pembangunan') $totalPotUP += (int) $disc->total;
                elseif ($jenis === 'Uang Pemeliharaan dan Pengembangan') $totalPotUPP += (int) $disc->total;
            }
        }

        // ====== CHART HARIAN (1 query aggregate) ======
        // hasil: map['YYYY-MM-DD']['RA'|'0'|'1'|'2'] = total_sum
        $dailyRows = Transaction::query()
            ->join('siswa', 'siswa.id', '=', 'transaksi.id_siswa') // sesuaikan FK jika beda
            ->whereBetween('transaksi.tgl_transaksi', [$start, $end])
            ->where('transaksi.status', 'Success')
            ->whereIn('siswa.tingkat', ['RA', '0', '1', '2'])
            ->selectRaw('DATE(transaksi.tgl_transaksi) as tgl, siswa.tingkat as tingkat, SUM(transaksi.total) as total_sum')
            ->groupBy('tgl', 'siswa.tingkat')
            ->get();

        $map = [];
        foreach ($dailyRows as $r) {
            $map[$r->tgl][$r->tingkat] = (int) $r->total_sum;
        }

        $d = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $transaksiRA = [];
        $transaksiSD = [];
        $transaksiSMP = [];
        $transaksiSMA = [];

        for ($i = 1; $i <= $d; $i++) {
            $dateKey = Carbon::create($year, $month, $i)->toDateString();
            $transaksiRA[]  = $map[$dateKey]['RA'] ?? 0;
            $transaksiSD[]  = $map[$dateKey]['0']  ?? 0;
            $transaksiSMP[] = $map[$dateKey]['1']  ?? 0;
            $transaksiSMA[] = $map[$dateKey]['2']  ?? 0;
        }

        $totalTransaksi = $successTransaksi->sum('total');

        return response()->json([
            'totalTransaksi' => $totalTransaksi,
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => $totalUS,
            'total_potongan_us' => $totalPotUS,
            'total_uang_pembangunan' => $totalUP,
            'total_potongan_up' => $totalPotUP,
            'total_upp' => $totalUPP,
            'total_potongan_upp' => $totalPotUPP,
            'total_upk' => $totalUPK,
            'transaksiRA' => $transaksiRA,
            'transaksiSD' => $transaksiSD,
            'transaksiSMP' => $transaksiSMP,
            'transaksiSMA' => $transaksiSMA,
        ]);
    }


    public function getDiagramDataSemester(Request $request)
    {
        $year = (int) $request->date;
        $semester = $request->semester;
        $tingkatFilter = $request->tingkat;

        // Range semester
        if ($semester === 'Ganjil') {
            $startMonth = 1;
            $endMonth = 6;
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end   = Carbon::create($year, 6, 30)->endOfDay();
        } else { // Genap
            $startMonth = 7;
            $endMonth = 12;
            $start = Carbon::create($year, 7, 1)->startOfDay();
            $end   = Carbon::create($year, 12, 31)->endOfDay();
        }

        /**
         * =========================
         * SUCCESS TRANSAKSI (base)
         * =========================
         */
        $successQ = Transaction::query()
            ->whereBetween('tgl_transaksi', [$start, $end])
            ->where('status', 'Success')
            ->with([
                'student',
                'schoolFee',
                'schoolDevFee',
                'schoolMaintenanceFee',
                'schoolEquipmentFee',
                'discounts.discount',
            ]);

        if ($tingkatFilter !== 'full') {
            $successQ->whereHas('siswa', fn($q) => $q->where('tingkat', $tingkatFilter));
        }

        $successTransaksi = $successQ->get();

        /**
         * =========================
         * JUMLAH & TOTAL per tingkat
         * =========================
         */
        $grouped = $successTransaksi->groupBy(fn($t) => optional($t->student)->tingkat);

        $jumlah = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->count(),
            'SD'  => ($grouped['0']  ?? collect())->count(),
            'SMP' => ($grouped['1']  ?? collect())->count(),
            'SMA' => ($grouped['2']  ?? collect())->count(),
            'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count(),
        ];

        $total = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->sum('total'),
            'SD'  => ($grouped['0']  ?? collect())->sum('total'),
            'SMP' => ($grouped['1']  ?? collect())->sum('total'),
            'SMA' => ($grouped['2']  ?? collect())->sum('total'),
            'lainnya' => $successTransaksi->sum('jumlah_lainnya'),
        ];

        /**
         * =========================
         * TOTAL US/UP & POTONGAN
         * =========================
         */
        $totalUS = 0;
        $totalUP = 0;
        $totalUPP = 0;
        $totalUPK = 0;
        $totalPotUS = 0;
        $totalPotUP = 0;
        $totalPotUPP = 0;

        foreach ($successTransaksi as $trx) {
            $totalUS += $trx->schoolFee->sum('total');
            $totalUP += $trx->schoolDevFee->sum('total');
            $totalUPP += $trx->schoolMaintenanceFee->sum('total');
            $totalUPK += $trx->schoolEquipmentFee->sum('total');

            foreach ($trx->discounts as $disc) {
                $jenis = $disc->discount?->jenis;
                if ($jenis === 'Uang Sekolah') {
                    $totalPotUS += (int) $disc->total;
                } elseif ($jenis === 'Uang Pembangunan') {
                    $totalPotUP += (int) $disc->total;
                } elseif ($jenis === 'Uang Pemeliharaan dan Pengembangan') {
                    $totalPotUPP += (int) $disc->total;
                }
            }
        }

        /**
         * =========================
         * LINE CHART: SUM per bulan & tingkat (1 query)
         * =========================
         *
         * NOTE:
         * - sesuaikan join FK kalau bukan transactions.id_siswa
         */
        $aggQ = Transaction::query()
            ->join('siswa', 'siswa.id', '=', 'transaksi.id_siswa')
            ->whereBetween('transaksi.tgl_transaksi', [$start, $end])
            ->where('transaksi.status', 'Success')
            ->whereIn('siswa.tingkat', ['RA', '0', '1', '2'])
            ->selectRaw('MONTH(transaksi.tgl_transaksi) as bulan, siswa.tingkat, SUM(transaksi.total) as total_sum')
            ->groupBy('bulan', 'siswa.tingkat');

        if ($tingkatFilter !== 'full') {
            $aggQ->where('siswa.tingkat', $tingkatFilter);
        }

        $rows = $aggQ->get();

        // mapping [bulan][tingkat] => total
        $map = [];
        foreach ($rows as $r) {
            $map[(int) $r->bulan][$r->tingkat] = (int) $r->total_sum;
        }

        $transaksiRA = [];
        $transaksiSD = [];
        $transaksiSMP = [];
        $transaksiSMA = [];

        for ($m = $startMonth; $m <= $endMonth; $m++) {
            $transaksiRA[]  = $map[$m]['RA'] ?? 0;
            $transaksiSD[]  = $map[$m]['0']  ?? 0;
            $transaksiSMP[] = $map[$m]['1']  ?? 0;
            $transaksiSMA[] = $map[$m]['2']  ?? 0;
        }

        return response()->json([
            'totalTransaksi' => $successTransaksi->sum('total'),
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => $totalUS,
            'total_potongan_us' => $totalPotUS,
            'total_uang_pembangunan' => $totalUP,
            'total_potongan_up' => $totalPotUP,
            'total_upp' => $totalUPP,
            'total_potongan_upp' => $totalPotUPP,
            'total_upk' => $totalUPK,
            'transaksiRA' => $transaksiRA,
            'transaksiSD' => $transaksiSD,
            'transaksiSMP' => $transaksiSMP,
            'transaksiSMA' => $transaksiSMA,
        ]);
    }


    public function getDiagramDataYearly(Request $request)
    {
        $year = (int) $request->date;
        $tingkatFilter = $request->tingkat;

        // Base untuk transaksi sukses (untuk summary & hitung US/UP/Potongan)
        $successBase = Transaction::query()
            ->whereYear('tgl_transaksi', $year)
            ->where('status', 'Success')
            ->with([
                'student',
                'schoolFee',
                'schoolDevFee',
                'schoolMaintenanceFee',
                'schoolEquipmentFee',
                'discounts.discount',
            ]);

        if ($tingkatFilter !== 'full') {
            $successBase->whereHas('student', fn($q) => $q->where('tingkat', $tingkatFilter));
        }

        $successTransaksi = $successBase->get();

        // Group by tingkat (RA / 0 / 1 / 2)
        $grouped = $successTransaksi->groupBy(fn($t) => optional($t->student)->tingkat);

        $jumlah = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->count(),
            'SD'  => ($grouped['0']  ?? collect())->count(),
            'SMP' => ($grouped['1']  ?? collect())->count(),
            'SMA' => ($grouped['2']  ?? collect())->count(),
            'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count(),
        ];

        $total = (object)[
            'RA'  => ($grouped['RA'] ?? collect())->sum('total'),
            'SD'  => ($grouped['0']  ?? collect())->sum('total'),
            'SMP' => ($grouped['1']  ?? collect())->sum('total'),
            'SMA' => ($grouped['2']  ?? collect())->sum('total'),
            'lainnya' => $successTransaksi->sum('jumlah_lainnya'),
        ];

        // Hitung US/UP & Potongan (tanpa N+1 karena sudah eager load)
        $totalUS = 0;
        $totalUP = 0;
        $totalUPP = 0;
        $totalUPK = 0;
        $totalPotUS = 0;
        $totalPotUP = 0;
        $totalPotUPP = 0;

        foreach ($successTransaksi as $trx) {
            $totalUS += $trx->schoolFee->sum('total');
            $totalUP += $trx->schoolDevFee->sum('total');
            $totalUPP += $trx->schoolMaintenanceFee->sum('total');
            $totalUPK += $trx->schoolEquipmentFee->sum('total');

            foreach ($trx->discounts as $disc) {
                $jenis = $disc->discount?->jenis;
                if ($jenis === 'Uang Sekolah') {
                    $totalPotUS += (int) $disc->total;
                } elseif ($jenis === 'Uang Pembangunan') {
                    $totalPotUP += (int) $disc->total;
                } elseif ($jenis === 'Uang Pemeliharaan dan Pengembangan') {
                    $totalPotUPP += (int) $disc->total;
                }
            }
        }

        /**
         * Chart bulanan: 1 query agregasi untuk semua tingkat per bulan
         * (lebih cepat daripada 48 query)
         */
        $chartBase = Transaction::query()
            ->selectRaw("
            MONTH(tgl_transaksi) as m,
            SUM(CASE WHEN siswa.tingkat = 'RA' THEN  transaksi.total ELSE 0 END) as ra_total,
            SUM(CASE WHEN siswa.tingkat = '0'  THEN transaksi.total ELSE 0 END) as sd_total,
            SUM(CASE WHEN siswa.tingkat = '1'  THEN transaksi.total ELSE 0 END) as smp_total,
            SUM(CASE WHEN siswa.tingkat = '2'  THEN transaksi.total ELSE 0 END) as sma_total
        ")
            ->join('siswa', 'siswa.id', '=', 'transaksi.id_siswa')
            ->whereYear('transaksi.tgl_transaksi', $year)
            ->where('transaksi.status', 'Success')
            ->groupBy('m')
            ->orderBy('m');

        // kalau filter tingkat dipilih, tetap bisa apply di chart juga
        if ($tingkatFilter !== 'full') {
            $chartBase->where('siswa.tingkat', $tingkatFilter);
        }

        $rows = $chartBase->get()->keyBy('m');

        $transaksiRA = [];
        $transaksiSD = [];
        $transaksiSMP = [];
        $transaksiSMA = [];

        for ($m = 1; $m <= 12; $m++) {
            $row = $rows->get($m);
            $transaksiRA[]  = $row ? (int) $row->ra_total  : 0;
            $transaksiSD[]  = $row ? (int) $row->sd_total  : 0;
            $transaksiSMP[] = $row ? (int) $row->smp_total : 0;
            $transaksiSMA[] = $row ? (int) $row->sma_total : 0;
        }

        $payload = [
            'totalTransaksi' => (int) $successTransaksi->sum('total'),
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => (int) $totalUS,
            'total_potongan_us' => (int) $totalPotUS,
            'total_uang_pembangunan' => (int) $totalUP,
            'total_potongan_up' => (int) $totalPotUP,
            'transaksiRA' => $transaksiRA,
            'transaksiSD' => $transaksiSD,
            'transaksiSMP' => $transaksiSMP,
            'transaksiSMA' => $transaksiSMA,
        ];

        return response()->json($payload);
    }


    public function getDiagramDataSchoolYear(Request $request)
    {
        $schoolYear = $request->date;
        $tingkatFilter = $request->tingkat;

        // ambil transaksi sukses tahun ajaran (dengan relasi untuk hitung US/UP & potongan)
        $base = Transaction::query()
            ->where('tahun_ajaran', $schoolYear)
            ->where('status', 'Success')
            ->with([
                'student:id,tingkat',
                'schoolFee:id_transaksi,total',
                'schoolDevFee:id_transaksi,total',
                'schoolMaintenanceFee:id_transaksi,total',
                'schoolEquipmentFee:id_transaksi,total',
                'discounts.discount:id,jenis',
            ]);

        if ($tingkatFilter !== 'full') {
            $base->whereHas('student', fn($q) => $q->where('tingkat', $tingkatFilter));
        }

        $successTransaksi = $base->get();

        // ===== Summary cards =====
        $grouped = $successTransaksi->groupBy(fn($t) => optional($t->student)->tingkat);

        $jumlah = (object)[
            'RA' => ($grouped['RA'] ?? collect())->count(),
            'SD' => ($grouped['0']  ?? collect())->count(),
            'SMP' => ($grouped['1']  ?? collect())->count(),
            'SMA' => ($grouped['2']  ?? collect())->count(),
            'lainnya' => $successTransaksi->where('jumlah_lainnya', '!=', 0)->count(),
        ];

        $total = (object)[
            'RA' => ($grouped['RA'] ?? collect())->sum('total'),
            'SD' => ($grouped['0']  ?? collect())->sum('total'),
            'SMP' => ($grouped['1']  ?? collect())->sum('total'),
            'SMA' => ($grouped['2']  ?? collect())->sum('total'),
            'lainnya' => $successTransaksi->sum('jumlah_lainnya'),
        ];

        $totalUS = 0;
        $totalUP = 0;
        $totalUPP = 0;
        $totalUPK = 0;
        $totalPotUS = 0;
        $totalPotUP = 0;
        $totalPotUPP = 0;

        foreach ($successTransaksi as $trx) {
            $totalUS += $trx->schoolFee->sum('total');
            $totalUP += $trx->schoolDevFee->sum('total');
            $totalUPP += $trx->schoolMaintenanceFee->sum('total');
            $totalUPK += $trx->schoolEquipmentFee->sum('total');

            foreach ($trx->discounts as $disc) {
                $jenis = $disc->discount?->jenis;
                if ($jenis === 'Uang Sekolah') $totalPotUS += (int) $disc->total;
                elseif ($jenis === 'Uang Pembangunan') $totalPotUP += (int) $disc->total;
                elseif ($jenis === 'Uang Pemeliharaan dan Pengembangan') $totalPotUPP += (int) $disc->total;
            }
        }

        // ===== Line chart (Jul->Jun) =====
        // query agregat: SUM(total) group by month + tingkat
        $raw = Transaction::query()
            ->selectRaw("MONTH(tgl_transaksi) as m, siswa.tingkat as tingkat, SUM(transaksi.total) as total")
            ->join('siswa', 'siswa.id', '=', 'transaksi.id_siswa')
            ->where('transaksi.tahun_ajaran', $schoolYear)
            ->where('transaksi.status', 'Success')
            ->when($tingkatFilter !== 'full', fn($q) => $q->where('siswa.tingkat', $tingkatFilter))
            ->groupBy('m', 'siswa.tingkat')
            ->get();

        $months = [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6];

        $map = [];
        foreach ($raw as $r) {
            $map[$r->tingkat][$r->m] = (int) $r->total;
        }

        $transaksiRA  = array_map(fn($m) => $map['RA'][$m] ?? 0, $months);
        $transaksiSD  = array_map(fn($m) => $map['0'][$m]  ?? 0, $months);
        $transaksiSMP = array_map(fn($m) => $map['1'][$m]  ?? 0, $months);
        $transaksiSMA = array_map(fn($m) => $map['2'][$m]  ?? 0, $months);

        return response()->json([
            'totalTransaksi' => (int) $successTransaksi->sum('total'),
            'jumlah' => $jumlah,
            'total' => $total,
            'total_uang_sekolah' => (int) $totalUS,
            'total_potongan_us' => (int) $totalPotUS,
            'total_uang_pembangunan' => (int) $totalUP,
            'total_potongan_up' => (int) $totalPotUP,
            'total_upp' => (int) $totalUPP,
            'total_potongan_upp' => (int) $totalPotUPP,
            'total_upk' => (int) $totalUPK,
            'transaksiRA' => $transaksiRA,
            'transaksiSD' => $transaksiSD,
            'transaksiSMP' => $transaksiSMP,
            'transaksiSMA' => $transaksiSMA,
        ]);
    }
}
