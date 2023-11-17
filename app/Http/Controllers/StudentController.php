<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentPostRequest;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;


class StudentController extends Controller
{
    /**
     * Display a listing of the active resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() : View
    {
        $data = Student::orderBy('nama')
        ->Where('status', 1)
        ->get();

        return view('pages.students.table', ([
            'data' => $data
        ]));
    }

    /**
     * Display a listing of the In-active resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function old() : View
    {
        $data = Student::orderBy('nama')
        ->Where('status', 0)
        ->get();

        return view('pages.students.oldTable', ([
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
        return view('pages.students.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StudentPostRequest $request)
    {
        // $request->validate();

        $data = $request->all();

        $data['bulan_spp_terakhir'] = date("Y-m-d", strtotime("-1 month", strtotime($request->tmt_masuk)));

        Student::create($data);

        Alert::success('Berhasil', 'Data Siswa Berhasil Disimpan!');
        return redirect()->route('students.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit($id) : View
    {
        $data = Student::findorFail($id);

        return view('pages.students.edit', ([
            'data' => $data
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(StudentPostRequest $request, string $id) : RedirectResponse
    {
        $data       = $request->all();
        $data['bulan_spp_terakhir'] = date('Y-m-d', strtotime('-1 month', $request->tmt_masuk));

        
        $student    = Student::findorFail($id);
            
        $student->update($data);
        Alert::success('Berhasil', 'Data Siswa Berhasil Diubah!');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id) : RedirectResponse
    {
            $student = Student::find($id);
            $student->delete();

            Alert::success('Berhasil', 'Data Siswa Berhasil Dihapus!');

            return back();
    }

     /**
    * Activate student by Admin
    */
    public function active($id)
    {
        $user = 
        Student::find($id);
        $user->status = 1;
        $user->save();
        Alert::success('Berhasil', 'Siswa Berhasil Diaktifkan!');

        return back();
    }

     /**
    * In-activate student by Admin
    */
    public function hold($id)
    {
        $user = Student::find($id);
        $user->status = 0;
        $user->save();
        Alert::success('Berhasil', 'Siswa Berhasil Dinon-aktifkan!');

        return back();
    }

    public function getData(Request $request) 
    {
        $data = Student::orderBy('nama')
                ->Where('status', $request->status)
                ->get();

        $dataTable = collect([]);
        $no = 1;
        foreach ($data as $item){
            
            if ($item->tingkat == '0'){
                $tingkat = '<span class="badge bg-danger">SD</span>';
            }elseif ($item->tingkat == '1') {
                $tingkat = '<span class="badge bg-primary">SMP</span>';
            }elseif ($item->tingkat == '2') {
                $tingkat = '<span class="badge bg-secondary">SMA</span>';
            }elseif ($item->tingkat == 'RA') {
                $tingkat = '<span class="badge bg-info">RA</span>';
            }else{
                $tingkat = '';
            }

            $nama = $item->nama.' '.$tingkat;
            $jenisKelamin = $item->jenis_kelamin == 1 ? 'Perempuan' : 'Laki-laki';
            $kelas = $item->kelas.'/'.$item->grup;
            $spp = 'Bulan '.\Carbon\Carbon::parse($item->bulan_spp_terakhir)->format('m Y');

            if ($item->status == 1){
                $status = '<span class="badge bg-info">Active</span>';
                $statusButton = '<a href="'.route('students.inActive', $item->id).'" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Inactive"><img src="'.url('/assets/img/global/lock.svg').'" width="20" alt="Inactive"></a>';
            }else{
                $status = '<span class="badge bg-warning">In-Active</span>';
                $statusButton = '<a href="'.route('students.active', $item->id).'" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Aktifkan"><img src="'.url('/assets/img/global/check.svg').'" width="20" alt="Aktifkan"></a>';
            }

            $editButton = '<a href="'.route('students.edit', $item->id).'" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><img src="'.url('/assets/img/global/edit.svg').'" width="20" alt="Edit"> </a>';
            $deleteButton = '
            <form action="'.route('students.destroy', $item->id).'" method="POST" class="d-inline">
                <input type="hidden" name="_method" value="delete">
                <input type="hidden" name="_token" value="'.csrf_token().'">
                <button type="submit" onclick="return confirm("Apakah kamu yakin ingin menghapus ini ?");" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><img src="'.url('/assets/img/global/trash.svg').'" width="20" alt="Hapus"></button>
            </form>
            ';

            $action = '';
            $action = $statusButton.' '.$editButton.' '.$deleteButton;

            $dataTable->push([
                    'no' => $no,
                    'nis' => $item->nis, 
                    'nama' => $nama, 
                    'jenis_kelamin' => $jenisKelamin, 
                    'kelas' => $kelas, 
                    'tahun_angkatan' => $item->tahun_angkatan, 
                    'spp_terakhir' => $spp, 
                    'status' => $status,
                    'action' => $action
                ]);
                $no++;
        }

        // dd($dataTable);
  
       return DataTables::of($dataTable)->escapeColumns([])->toJson();
        
    }

    public function fillLastSPP()
    {
        DB::table('siswa')->where('status', 1)->orWhereNull('bulan_spp_terakhir_new')->orderBy('id')->lazy()->each(function (object $data) {
                if ($data->bulan_spp_terakhir != 0){
                    $student = Student::findOrFail($data->id);
                    echo $student->id;

                    if ($student){
                        $student->bulan_spp_terakhir_new = '2023-'.$data->bulan_spp_terakhir.'-01';
                        $student->saveQuietly();
                    }   
                }
        });

        // return Redirect::to('/');
        // return $data;
    }

}

