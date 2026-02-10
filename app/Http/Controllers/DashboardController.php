<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $siswa = Student::where('status', 1)->get();

        // $schoolYear = SchoolYear::all();

        // $year = [null];
        // $totalRA = [];
        // $totalSD = [];
        // $totalSMP = [];
        // $totalSMA = [];

        // foreach ($schoolYear as $item) {
        //     array_push($year, $item->tahun_ajaran);

        //     $RATransaksi = Transaction::where('tahun_ajaran', $item->tahun_ajaran)->where('status', 'Success')
        //         ->whereHas('student', function ($query) {
        //             $query->where('tingkat', 'RA');
        //         })->sum('total');

        //     array_push($totalRA, intval($RATransaksi));

        //     $SDTransaksi = Transaction::where('tahun_ajaran', $item->tahun_ajaran)->where('status', 'Success')
        //         ->whereHas('student', function ($query) {
        //             $query->where('tingkat', '0');
        //         })->sum('total');

        //     array_push($totalSD, intval($SDTransaksi));

        //     $SMPTransaksi = Transaction::where('tahun_ajaran', $item->tahun_ajaran)->where('status', 'Success')
        //         ->whereHas('student', function ($query) {
        //             $query->where('tingkat', '1');
        //         })->sum('total');

        //     array_push($totalSMP, intval($SMPTransaksi));

        //     $SMATransaksi = Transaction::where('tahun_ajaran', $item->tahun_ajaran)->where('status', 'Success')
        //         ->whereHas('student', function ($query) {
        //             $query->where('tingkat', '2');
        //         })->sum('total');

        //     array_push($totalSMA, intval($SMATransaksi));
        // }

        // dd($totalYear);

        return view('dashboard', [
            'siswa' => $siswa,
            // 'year' => $year,
            // 'totalRA' => $totalRA,
            // 'totalSD' => $totalSD,
            // 'totalSMP' => $totalSMP,
            // 'totalSMA' => $totalSMA
        ]);
    }
    public function getData()
    {
        $schoolYears = SchoolYear::orderBy('tahun_ajaran')->pluck('tahun_ajaran')->values();

        $labels = $schoolYears->all();

        $totalRA  = array_fill(0, count($labels), 0);
        $totalSD  = array_fill(0, count($labels), 0);
        $totalSMP = array_fill(0, count($labels), 0);
        $totalSMA = array_fill(0, count($labels), 0);

        $rows = Transaction::query()
            ->join('siswa', 'siswa.id', '=', 'transaksi.id_siswa')
            ->where('transaksi.status', 'Success')
            ->whereIn('transaksi.tahun_ajaran', $labels)
            ->selectRaw('transaksi.tahun_ajaran as tahun_ajaran, siswa.tingkat as tingkat, SUM(transaksi.total) as total_sum')
            ->groupBy('transaksi.tahun_ajaran', 'siswa.tingkat')
            ->get();

        $indexByYear = array_flip($labels);

        foreach ($rows as $r) {
            $i = $indexByYear[$r->tahun_ajaran] ?? null;
            if ($i === null) continue;

            $sum = (int) $r->total_sum;

            if ($r->tingkat === 'RA') $totalRA[$i] = $sum;
            elseif ($r->tingkat === '0') $totalSD[$i] = $sum;
            elseif ($r->tingkat === '1') $totalSMP[$i] = $sum;
            elseif ($r->tingkat === '2') $totalSMA[$i] = $sum;
        }

        // prepend NULL untuk Highcharts
        array_unshift($labels, null);
        array_unshift($totalRA, 0);
        array_unshift($totalSD, 0);
        array_unshift($totalSMP, 0);
        array_unshift($totalSMA, 0);

        return response()->json([
            'year' => $labels,
            'totalRA' => $totalRA,
            'totalSD' => $totalSD,
            'totalSMP' => $totalSMP,
            'totalSMA' => $totalSMA,
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
