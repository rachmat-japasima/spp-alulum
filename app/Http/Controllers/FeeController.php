<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $data = Fee::orderBy('tahun_angkatan', 'desc')
            ->get();

        return view('pages.fee.table', ([
            'data' => $data
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        return view('pages.fee.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tahun_angkatan' => ['required', 'integer', Rule::unique('daftar_biaya')->whereNull('deleted_at')],
            'pembangunan_ra' => ['required', 'numeric', 'min:0'],
            'pembangunan_sd' => ['required', 'numeric', 'min:0'],
            'pembangunan_smp' => ['required', 'numeric', 'min:0'],
            'pembangunan_sma' => ['required', 'numeric', 'min:0'],
            'seleksi_masuk' => ['required', 'numeric', 'min:0'],
            'kelas_1' => ['required', 'numeric', 'min:0'],
            'kelas_2' => ['required', 'numeric', 'min:0'],
            'kelas_3' => ['required', 'numeric', 'min:0'],
            'kelas_4' => ['required', 'numeric', 'min:0'],
            'kelas_5' => ['required', 'numeric', 'min:0'],
            'kelas_6' => ['required', 'numeric', 'min:0'],
            'kelas_7' => ['required', 'numeric', 'min:0'],
            'kelas_8' => ['required', 'numeric', 'min:0'],
            'kelas_9' => ['required', 'numeric', 'min:0'],
            'kelas_10' => ['required', 'numeric', 'min:0'],
            'kelas_11' => ['required', 'numeric', 'min:0'],
            'kelas_12' => ['required', 'numeric', 'min:0'],
            'ra' => ['required', 'numeric', 'min:0'],
            'pemeliharaan_ra' => ['required', 'numeric', 'min:0'],
            'perlengkapan_ra' => ['required', 'numeric', 'min:0'],
            'pemeliharaan_sd' => ['required', 'numeric', 'min:0'],
            'perlengkapan_sd' => ['required', 'numeric', 'min:0'],
            'pemeliharaan_smp' => ['required', 'numeric', 'min:0'],
            'perlengkapan_smp' => ['required', 'numeric', 'min:0'],
            'pemeliharaan_sma' => ['required', 'numeric', 'min:0'],
            'perlengkapan_sma' => ['required', 'numeric', 'min:0'],
        ]);

        $data = $request->all();
        Fee::create($data);

        Alert::success('Berhasil', 'Data Biaya Berhasil Disimpan!');
        return redirect()->route('fees.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function show(Fee $fee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $data = Fee::findorFail($id);

        return view('pages.fee.edit', ([
            'data' => $data
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'pembangunan_ra' => ['numeric', 'min:0'],
            'pembangunan_sd' => ['numeric', 'min:0'],
            'pembangunan_smp' => ['numeric', 'min:0'],
            'pembangunan_sma' => ['numeric', 'min:0'],
            'seleksi_masuk' => ['required', 'numeric', 'min:0'],
            'kelas_1' => ['required', 'numeric', 'min:0'],
            'kelas_2' => ['required', 'numeric', 'min:0'],
            'kelas_3' => ['required', 'numeric', 'min:0'],
            'kelas_4' => ['required', 'numeric', 'min:0'],
            'kelas_5' => ['required', 'numeric', 'min:0'],
            'kelas_6' => ['required', 'numeric', 'min:0'],
            'kelas_7' => ['required', 'numeric', 'min:0'],
            'kelas_8' => ['required', 'numeric', 'min:0'],
            'kelas_9' => ['required', 'numeric', 'min:0'],
            'kelas_10' => ['required', 'numeric', 'min:0'],
            'kelas_11' => ['required', 'numeric', 'min:0'],
            'kelas_12' => ['required', 'numeric', 'min:0'],
            'ra' => ['required', 'numeric', 'min:0'],
            'pemeliharaan_ra' => ['numeric', 'min:0'],
            'perlengkapan_ra' => ['numeric', 'min:0'],
            'pemeliharaan_sd' => ['numeric', 'min:0'],
            'perlengkapan_sd' => ['numeric', 'min:0'],
            'pemeliharaan_smp' => ['numeric', 'min:0'],
            'perlengkapan_smp' => ['numeric', 'min:0'],
            'pemeliharaan_sma' => ['numeric', 'min:0'],
            'perlengkapan_sma' => ['numeric', 'min:0'],
        ]);
        $data       = $request->all();
        $fee        = fee::findorFail($id);

        $fee->update($data);
        Alert::success('Berhasil', 'Data Biaya Berhasil Diubah!');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        $fee = Fee::find($id);
        $fee->delete();

        Alert::success('Berhasil', 'Data Biaya Berhasil Dihapus!');

        return back();
    }
}
