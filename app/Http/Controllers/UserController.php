<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Registered;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() : View
    {
        $data = User::where('id', '!=', 7)
        ->orderBy('created_at')
        ->get();

        return view('pages.users.table', ([
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
        return view('pages.users.add');
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'string'],
            'remark' => ['string'],
            'email_verified' => ['string'],
        ]);

        if($request->email_verified == 'verified'){
            $email_verified = Carbon::now();
        }else{
            $email_verified = null;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'roles' => $request->role,
            'status' => 'Active',
            'remark' => $request->remark,
            'email_verified_at' => $email_verified
        ]);

        // event(new Registered($user));
        Alert::success('Berhasil', 'Data Berhasil Disimpan!');

        return redirect()->route('user.table');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) : View
    {
        $data = User::findorFail($id);

        return view('pages.users.edit', ([
            'data' => $data
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) : RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'roles' => ['required', Rule::in(['Admin', 'User'])],
            'remark' => ['string'],
            'status' => ['required', Rule::in(['Active', 'In-active'])],
         ]);

        $data   = $request->all();
        $user   = User::findorFail($id);
        $user->update($data);
        Alert::success('Berhasil', 'Data Berhasil Diubah!');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) : RedirectResponse
    {
        $user = User::find($id);
        $user->delete();

        Alert::success('Berhasil', 'Akun Berhasil Dihapus!');

        return back();
    }

    public function changePassword(Request $request, string $id) : RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::find($id);
        $user->password = Hash::make($request->password);

        $user->save();
        Alert::success('Berhasil', 'Password Berhasil Diubah!');


        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return redirect()->back();
    }

    public function active($id) : RedirectResponse
    {
        $user = User::find($id);
        $user->status = 'Active';
        $user->save();
        Alert::success('Berhasil', 'Akun Berhasil Diaktifkan!');

        return back();
    }

    public function hold($id) : RedirectResponse
    {
        $user = User::find($id);
        $user->status = 'In-active';
        $user->save();
        Alert::success('Berhasil', 'Akun Berhasil Dinon-aktifkan!');

        return back();
    }
}
