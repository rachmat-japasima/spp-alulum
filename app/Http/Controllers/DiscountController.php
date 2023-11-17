<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Student;
use App\Models\DiscountStudent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() : View
    {
        $data = Discount::orderBy('nama')
        ->get();

        return view('pages.discount.table', ([
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
        return view('pages.discount.add');
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
            'nama' => ['required', 'string', 'max:255'],
            'besaran' => ['required', 'integer'],
            'jenis' => ['required', 'string'],
            'keterangan' => ['string'],
            'status' => ['required','integer','in:0,1'],
        ]);

        $data = $request->all();
        Discount::create($data);

        Alert::success('Berhasil', 'Data Potongan Berhasil Disimpan!');
        return redirect()->route('discount.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function show(Discount $discount) : RedirectResponse
    {
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function edit($id) : View
    {
        $data = Discount::findorFail($id);
        $students = Student::Where('status', 1)->get();
        $discountStudent = DiscountStudent::whereBelongsTo($data)->get();

        return view('pages.discount.edit', ([
            'data' => $data,
            'students' => $students,
            'discountStudent' => $discountStudent
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id) : RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'besaran' => ['required', 'integer'],
            'jenis' => ['required', 'string'],
            'keterangan' => ['string'],
            'status' => ['required','integer','in:0,1'],
        ]);

        $data       = $request->all();
        $discount    = Discount::findorFail($id);
            
        $discount->update($data);
        Alert::success('Berhasil', 'Data Potongan Berhasil Diubah!');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        $discount = Discount::find($id);
        $discount->delete();

        Alert::success('Berhasil', 'Data Potongan Berhasil Dihapus!');

        return back();
    }

    /**
    * Activate Discount by Admin
    */
    public function active($id) : RedirectResponse
    {
        $discount = Discount::find($id);
        $discount->status = 1;
        $discount->save();
        Alert::success('Berhasil', 'Potongan Berhasil Diaktifkan!');

        return back();
    }

     /**
    * In-activate Discount by Admin
    */
    public function hold($id) : RedirectResponse
    {
        $discount = Discount::find($id);
        $discount->status = 0;
        $discount->save();
        Alert::success('Berhasil', 'Potongan Berhasil Dinon-aktifkan!');

        return back();
    }

    /**
    * Add student to Discount by Admin
    */
    public function addStudent(Request $request) : RedirectResponse
    {
        $data = $request->all();

        if(DiscountStudent::where('id_siswa', $request->id_siswa)->where('id_potongan', $request->id_potongan)->exists()){
            Alert::warning('Maaf!', 'Data siswa sudah ada di Potongan ini!');
        }else{
            DiscountStudent::create($data);
            Alert::success('Berhasil', 'Siswa Berhasil di tambahkan ke Potongan!');
        }   
        
        return back();
    }

    /**
    * Add student to Discount by Admin
    */
    public function removeStudent(string $id) : RedirectResponse
    {
        $discount = DiscountStudent::findOrFail($id);

        $discount->delete();
        Alert::success('Berhasil', 'Siswa berhasil dihapus dari Potongan!');

        return back();
    }
}
