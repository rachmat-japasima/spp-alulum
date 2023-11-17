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
    public function index() : View
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
    public function create() : View
    {
        return view('pages.fee.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) : RedirectResponse
    {
        $request->validate([
            'tahun_angkatan' => ['required', 'integer', Rule::unique('daftar_biaya')->whereNull('deleted_at')],
            'pembangunan_ra' => ['required', 'integer'],
            'pembangunan_sd' => ['required', 'integer'],
            'pembangunan_smp' => ['required', 'integer'],
            'pembangunan_sma' => ['required', 'integer'],
            'seleksi_masuk' => ['required', 'integer'],
            'kelas_1' => ['required', 'integer'],
            'kelas_2' => ['required', 'integer'],
            'kelas_3' => ['required', 'integer'],
            'kelas_4' => ['required', 'integer'],
            'kelas_5' => ['required', 'integer'],
            'kelas_6' => ['required', 'integer'],
            'kelas_7' => ['required', 'integer'],
            'kelas_8' => ['required', 'integer'],
            'kelas_9' => ['required', 'integer'],
            'kelas_10' => ['required', 'integer'],
            'kelas_11' => ['required', 'integer'],
            'kelas_12' => ['required', 'integer'],
            'ra' => ['required', 'integer'],
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
    public function edit($id) : View
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
    public function update(Request $request, string $id) : RedirectResponse
    {
        $request->validate([
            'pembangunan_ra' => ['required', 'integer'],
            'pembangunan_sd' => ['required', 'integer'],
            'pembangunan_smp' => ['required', 'integer'],
            'pembangunan_sma' => ['required', 'integer'],
            'seleksi_masuk' => ['required', 'integer'],
            'kelas_1' => ['required', 'integer'],
            'kelas_2' => ['required', 'integer'],
            'kelas_3' => ['required', 'integer'],
            'kelas_4' => ['required', 'integer'],
            'kelas_5' => ['required', 'integer'],
            'kelas_6' => ['required', 'integer'],
            'kelas_7' => ['required', 'integer'],
            'kelas_8' => ['required', 'integer'],
            'kelas_9' => ['required', 'integer'],
            'kelas_10' => ['required', 'integer'],
            'kelas_11' => ['required', 'integer'],
            'kelas_12' => ['required', 'integer'],
            'ra' => ['required', 'integer'],
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
    public function destroy($id) : RedirectResponse
    {
        $fee = Fee::find($id);
        $fee->delete();

        Alert::success('Berhasil', 'Data Biaya Berhasil Dihapus!');

        return back();
    }
}
