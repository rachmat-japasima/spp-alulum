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
        // $siswa = Student::where('status', 1)->get();

        $schoolYear = SchoolYear::all();

        $year = [null];
        $totalRA = [];
        $totalSD = [];
        $totalSMP = [];
        $totalSMA = [];

        foreach ($schoolYear as $item) {
            array_push($year, $item->tahun_ajaran);

            $RATransaksi = Transaction::where('tahun_ajaran', $item->tahun_ajaran)->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', 'RA');
                })->sum('total');

            array_push($totalRA, intval($RATransaksi));

            $SDTransaksi = Transaction::where('tahun_ajaran', $item->tahun_ajaran)->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', '0');
                })->sum('total');

            array_push($totalSD, intval($SDTransaksi));

            $SMPTransaksi = Transaction::where('tahun_ajaran', $item->tahun_ajaran)->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', '1');
                })->sum('total');

            array_push($totalSMP, intval($SMPTransaksi));

            $SMATransaksi = Transaction::where('tahun_ajaran', $item->tahun_ajaran)->where('status', 'Success')
                ->whereHas('student', function ($query) {
                    $query->where('tingkat', '2');
                })->sum('total');

            array_push($totalSMA, intval($SMATransaksi));
        }

        $data = [
            // 'siswa' => $siswa,
            'year' => $year,
            'totalRA' => $totalRA,
            'totalSD' => $totalSD,
            'totalSMP' => $totalSMP,
            'totalSMA' => $totalSMA
        ];

        return json_encode($data);
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
