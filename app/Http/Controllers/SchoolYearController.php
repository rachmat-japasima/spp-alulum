<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;

class SchoolYearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() : View
    {
        $data = SchoolYear::orderBy('tahun_ajaran', 'desc')
        ->get();

        return view('pages.schoolYear.table', ([
            'data' => $data
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'tahun_ajaran' => ['required', 'string', 'max:9', Rule::unique('tahun_ajaran')->whereNull('deleted_at')]
        ]);

        $data = $request->all();
        SchoolYear::create($data);

        Alert::success('Berhasil', 'Data tahun ajaran Berhasil Disimpan!');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SchoolYear  $schoolYear
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolYear $schoolYear)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SchoolYear  $schoolYear
     * @return \Illuminate\Http\Response
     */
    public function edit(SchoolYear $schoolYear)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SchoolYear  $schoolYear
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id) : RedirectResponse
    {
        $request->validate([
            'tahun_ajaran' => ['required', 'string', 'max:9', Rule::unique('tahun_ajaran')->whereNull('deleted_at')]
        ]);
        $data       = $request->all();
        $fee        = SchoolYear::findorFail($request->id);
            
        $fee->update($data);
        Alert::success('Berhasil', 'Data Tahun Ajaran Berhasil Diubah!');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SchoolYear  $schoolYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id) : RedirectResponse
    {
        $fee = SchoolYear::find($id);
        $fee->delete();

        Alert::success('Berhasil', 'Data Tahun Ajaran Berhasil Dihapus!');

        return back();
    }

    /**
    * Activate school year by Admin
    */
    public function active($id) : RedirectResponse
    {
        $discount = SchoolYear::find($id);
        $discount->status = 1;
        $discount->save();
        Alert::success('Berhasil', 'Tahun Ajaran Berhasil Diaktifkan!');

        return back();
    }

     /**
    * In-activate Discount by Admin
    */
    public function hold($id) : RedirectResponse
    {
        $discount = SchoolYear::find($id);
        $discount->status = 0;
        $discount->save();
        Alert::success('Berhasil', 'Tahun Ajaran Berhasil Dinon-aktifkan!');

        return back();
    }
}
