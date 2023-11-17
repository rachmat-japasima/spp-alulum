<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Student;
use GuzzleHttp\Client;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Request as GuzzleHttpRequest;
use RealRashid\SweetAlert\Facades\Alert;
use App\Jobs\SendMessageJob;

class BroadcastController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() : View
    {
        $data = Student::Where('status', 1)
        ->get();

        
        return view('pages.broadcast.index', ([
            'data' => $data
        ]));
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
        $pesan = $request->pesan;

        foreach ($request->no_telp as $no){
            $data = ['telp' => $no, 'pesan' => $pesan];
            dispatch(new SendMessageJob($data));
        }

        Alert::success('Berhasil', 'Pesan Broadcast Berhasil Terkirim!');
        return back();
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

    public function all(Request $request)
    {
        $pesan = $request->pesan;
        $tingkat = $request->tingkat;

        if($tingkat == 'full'){
            $student = Student::where('status', 1)->get(['telp_ortu']); 
        }else{
            $student = Student::where('status', 1)->where('tingkat', $tingkat)->get(['telp_ortu']); 
        }   

        foreach ($student as $no){
            if ($no->telp_ortu != null && $no->telp_ortu != '' && strlen($no->telp_ortu) > 0 && strlen($no->telp_ortu) < 14){
                $data = ['telp' => $no->telp_ortu, 'pesan' => $pesan];
                dispatch(new SendMessageJob($data));
            }
        }

        Alert::success('Berhasil', 'Pesan Broadcast Berhasil Terkirim!');
        return back();
    }

}
