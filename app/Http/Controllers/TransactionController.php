<?php

namespace App\Http\Controllers;

use App\Jobs\SendMessageJob;
use App\Models\Discount;
use App\Models\DiscountStudent;
use App\Models\DiscountTransaction;
use App\Models\Fee;
use App\Models\Message;
use App\Models\OtherTransaction;
use App\Models\SchoolDevFeeTransaction;
use App\Models\SchoolFeeTransaction;
use App\Models\Transaction;
use App\Models\SchoolYear;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleHttpRequest;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $data = Student::Where('status', 1)
            ->get();

        return view('pages.transaction.index', ([
            'data' => $data
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(string $id)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): view
    {
        $data = Student::findOrFail($request->id);
        $discountStudent = DiscountStudent::whereBelongsTo($data)->where('status', 'Active')->get();
        $list_siswa = Student::Where('status', 1)->get();
        $schoolFeeAmount = Fee::where('tahun_angkatan', $data->tahun_angkatan)->first();

        $discountList = Discount::where('status', 1)->get();
        $schoolYear = SchoolYear::where('status', 1)
            ->orderBy('tahun_ajaran', 'DESC')
            ->get();

        $transactions = Transaction::where('id_siswa', $data->id)
            ->orderBy('tgl_transaksi', 'desc')
            ->with('schoolFee')
            ->get();

        if ($transactions->count() > 0) {
            $successTransactions = Transaction::where('id_siswa', $data->id)
                ->where('status', 'Success')
                ->orderBy('tgl_transaksi', 'desc')
                ->with('schoolFee')
                ->get();

            $totalTransaksi = $successTransactions->sum('total');

            if ($successTransactions->count() > 0) {
                $schoolFee = SchoolFeeTransaction::whereBelongsTo($successTransactions)->orderBy('created_at', 'desc')->get();
                $schoolDevFee = SchoolDevFeeTransaction::whereBelongsTo($successTransactions)->orderBy('created_at', 'desc')->get();
                $discounts = DiscountTransaction::whereBelongsTo($successTransactions)->orderBy('created_at', 'desc')->get();
                $otherTransactions = OtherTransaction::whereBelongsTo($successTransactions)->orderBy('created_at', 'desc')->get();
            } else {
                $schoolFee = SchoolFeeTransaction::whereBelongsTo($transactions)->orderBy('created_at', 'desc')->get();
                $schoolDevFee = SchoolDevFeeTransaction::whereBelongsTo($transactions)->orderBy('created_at', 'desc')->get();
                $discounts = DiscountTransaction::whereBelongsTo($transactions)->orderBy('created_at', 'desc')->get();
                $otherTransactions = OtherTransaction::whereBelongsTo($transactions)->orderBy('created_at', 'desc')->get();
            }
        } else {
            $schoolFee = [];
            $schoolDevFee = [];
            $discounts = [];
            $otherTransactions = [];
            $totalTransaksi = 0;
        }

        if ($data->bulan_spp_terakhir != null) {
            $listMonths = $this->getArrearsMonths($data->bulan_spp_terakhir);
        } else {
            $listMonths = $this->getArrearsMonths(date('y') . '-06-01');
        }

        $lastNumber = Transaction::whereDate('tgl_transaksi', Carbon::today())->count();

        $transactionId = Carbon::now('Asia/Jakarta')->format('dmY') . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return view('pages.transaction.detail', ([
            'data' => $data,
            'list_siswa' => $list_siswa,
            'discountStudent' => $discountStudent,
            'schoolFeeAmount' => $schoolFeeAmount,
            'transactions' => $transactions,
            'schoolFee' => $schoolFee,
            'schoolDevFee' => $schoolDevFee,
            'discounts' => $discounts,
            'otherTransactions' => $otherTransactions,
            'schoolYear' => $schoolYear,
            'discountList' => $discountList,
            'totalTransaksi' => $totalTransaksi,
            'months' => $listMonths,
            'transactionId' => $transactionId
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): View
    {
        $data = Student::findOrFail($id);
        $discountStudent = DiscountStudent::whereBelongsTo($data)->where('status', 'Active')->get();
        $list_siswa = Student::Where('status', 1)->get();
        $schoolFeeAmount = Fee::where('tahun_angkatan', $data->tahun_angkatan)->first();

        $discountList = Discount::where('status', 1)->get();
        $schoolYear = SchoolYear::where('status', 1)
            ->orderBy('tahun_ajaran', 'DESC')
            ->get();

        $transactions = Transaction::where('id_siswa', $data->id)
            ->orderBy('tgl_transaksi', 'desc')
            ->with('schoolFee')
            ->get();

        if ($transactions->count() > 0) {
            $successTransactions = Transaction::where('id_siswa', $data->id)
                ->where('status', 'Success')
                ->orderBy('tgl_transaksi', 'desc')
                ->with('schoolFee')
                ->get();

            $totalTransaksi = $successTransactions->sum('total');

            if ($successTransactions->count() > 0) {
                $schoolFee = SchoolFeeTransaction::whereBelongsTo($successTransactions)->orderBy('created_at', 'desc')->get();
                $schoolDevFee = SchoolDevFeeTransaction::whereBelongsTo($successTransactions)->orderBy('created_at', 'desc')->get();
                $discounts = DiscountTransaction::whereBelongsTo($successTransactions)->orderBy('created_at', 'desc')->get();
                $otherTransactions = OtherTransaction::whereBelongsTo($successTransactions)->orderBy('created_at', 'desc')->get();
            } else {
                $schoolFee = SchoolFeeTransaction::whereBelongsTo($transactions)->orderBy('created_at', 'desc')->get();
                $schoolDevFee = SchoolDevFeeTransaction::whereBelongsTo($transactions)->orderBy('created_at', 'desc')->get();
                $discounts = DiscountTransaction::whereBelongsTo($transactions)->orderBy('created_at', 'desc')->get();
                $otherTransactions = OtherTransaction::whereBelongsTo($transactions)->orderBy('created_at', 'desc')->get();
            }
        } else {
            $schoolFee = [];
            $schoolDevFee = [];
            $discounts = [];
            $otherTransactions = [];
            $totalTransaksi = 0;
        }

        if ($data->bulan_spp_terakhir != null) {
            $listMonths = $this->getArrearsMonths($data->bulan_spp_terakhir);
        } else {
            $listMonths = $this->getArrearsMonths(date('y') . '-06-01');
        }

        $lastNumber = Transaction::whereDate('tgl_transaksi', Carbon::today())->count();

        $transactionId = Carbon::now('Asia/Jakarta')->format('dmY') . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return view('pages.transaction.detail', ([
            'data' => $data,
            'list_siswa' => $list_siswa,
            'discountStudent' => $discountStudent,
            'schoolFeeAmount' => $schoolFeeAmount,
            'transactions' => $transactions,
            'schoolFee' => $schoolFee,
            'schoolDevFee' => $schoolDevFee,
            'discounts' => $discounts,
            'otherTransactions' => $otherTransactions,
            'schoolYear' => $schoolYear,
            'discountList' => $discountList,
            'totalTransaksi' => $totalTransaksi,
            'months' => $listMonths,
            'transactionId' => $transactionId
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(string $id): RedirectResponse
    {
        $transaction = Transaction::find($id);
        $transaction->status = 'Cancel';
        $transaction->save();
        Alert::success('Berhasil', 'Transaksi Pembayaran Berhasil Dibatalkan!');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    public function pay(Request $request): RedirectResponse
    {
        if (Transaction::where('no_bukti', $request->no_bukti)) {
            $lastNumber = Transaction::whereDate('tgl_transaksi', Carbon::today())->count();
            $request['no_bukti'] = Carbon::now('Asia/Jakarta')->format('dmY') . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        $request->validate([
            'no_bukti' => ['required', 'string', 'max:255', Rule::unique('transaksi')->where('status', 'Success')],
            'tahun_ajaran' => ['required', 'string'],
            'id' => ['required', 'integer'],
            'uang_sekolah' => ['required', 'integer'],
            'tingkat' => ['required', 'string'],
        ]);

        $student = Student::findorFail($request->id);

        $data = [];
        $data['no_bukti'] = $request->no_bukti;
        $data['id_siswa'] = $request->id;
        $data['tahun_ajaran'] = $request->tahun_ajaran;
        $data['nis'] = $student->nis;
        $data['tingkat'] = $request->tingkat;
        $data['tgl_transaksi'] = Carbon::now();

        if ($request->transfer) {
            $data['jenis'] = "M-Banking";
        } else {
            $data['jenis'] = "Manual";
        }


        // uang sekolah
        $last_month = 0;
        if ($request->bulan != null) {
            $last_month = last($request->bulan);
            if (count($request->bulan) > 0) {
                $total_bulan = count($request->bulan);
                $data['jumlah_bulan'] = $total_bulan;
                $data['jumlah_us'] = $total_bulan * $request->uang_sekolah;
                if ($total_bulan > 1) {
                    $listBulan = Carbon::parse($request->bulan[0])->format('M') . '-' . Carbon::parse($request->bulan[$total_bulan - 1])->format('M');
                } else {
                    $listBulan = Carbon::parse($request->bulan[0])->format('M');
                }
                $data['bulan'] = $listBulan;
            } else {
                $data['jumlah_bulan'] = 0;
                $data['jumlah_us'] = 0;
                $data['bulan'] = null;
            }
        } else {
            $data['jumlah_bulan'] = 0;
            $data['jumlah_us'] = 0;
            $data['bulan'] = null;
        }

        // potongan
        $percentUS = 0;
        $percentUP = 0;
        $potUS = 0;
        $potUP = 0;
        if ($request->potongan != null) {
            if (count($request->potongan) > 0) {
                foreach ($request->potongan as $potongan) {
                    $data_pot = Discount::findorFail($potongan);
                    if ($data_pot->jenis == "Uang Sekolah") {
                        $percentUS += $data_pot->besaran;
                    } else {
                        $percentUP += $data_pot->besaran;
                    }
                }

                if ($request->bulan != null) {
                    $total_bulan = count($request->bulan);
                    $potUS = (($request->uang_sekolah * $percentUS) / 100) * $total_bulan;
                }

                if ($request->pembangunan) {
                    $potUP = ($request->uang_pembangunan * $percentUP) / 100;
                }
                $data['jumlah_potongan'] = $potUS + $potUP;
            } else {
                $data['jumlah_potongan'] = 0;
            }
        } else {
            $data['jumlah_potongan'] = 0;
        }


        if ($request->pembangunan) {
            $uang_pembangunan = $request->uang_pembangunan;
            $data['jumlah_up'] = $uang_pembangunan;
        } else {
            $data['jumlah_up'] = 0;
        }

        // uang lainnya
        if ($request->lainnya) {
            if (count($request->total_lainnya) > 0) {
                $jumlah_lainnya = 0;
                foreach ($request->total_lainnya as $lainnya) {
                    $jumlah_lainnya += $lainnya;
                }
                $data['jumlah_lainnya'] = $jumlah_lainnya;
            } else {
                $data['jumlah_lainnya'] = 0;
            }
        } else {
            $data['jumlah_lainnya'] = 0;
        }

        $data['total'] = ($data['jumlah_up'] + $data['jumlah_us'] + $data['jumlah_lainnya']) - $data['jumlah_potongan'];
        $data['id_user'] = Auth::user()->id;
        $data['keterangan'] = $request->keterangan;
        $data['status'] = 'Success';

        // print_r($data);

        $transaction = Transaction::create($data);

        if ($transaction) {

            if ($request->bulan != null) {
                $us = $request->uang_sekolah;

                if ($request->potongan != null) {
                    if (count($request->potongan) > 0) {
                        $us = $us - (($us * $percentUS) / 100);
                        foreach ($request->potongan as $potongan) {
                            $data_pot = Discount::findorFail($potongan);

                            if ($data_pot->jenis == 'Uang Sekolah') {
                                $total_pot = (($request->uang_sekolah * $data_pot->besaran) / 100) * count($request->bulan);
                                DiscountTransaction::create(['id_transaksi' => $transaction->id, 'id_potongan' => $potongan, 'total' => $total_pot]);
                            }
                        }
                    }
                }


                foreach ($request->bulan as $bulan) {
                    $textBulan = $this->numberToMonth(Carbon::parse($bulan)->format('m'));
                    SchoolFeeTransaction::create(['id_transaksi' => $transaction->id, 'bulan' => $textBulan, 'total' => $us]);
                }

                $student->bulan_spp_terakhir = $last_month;
                $student->save();
            }

            if ($request->pembangunan) {
                $up = $uang_pembangunan;
                if ($request->potongan != null) {
                    if (count($request->potongan) > 0) {
                        if ($percentUP > 0) {
                            $up = $uang_pembangunan - (($uang_pembangunan * $percentUP) / 100);
                        }

                        foreach ($request->potongan as $potongan) {
                            $data_pot = Discount::findorFail($potongan);

                            if ($data_pot->jenis == 'Uang Pembangunan') {
                                $total_pot = ($uang_pembangunan * $data_pot->besaran) / 100;
                                DiscountTransaction::create(['id_transaksi' => $transaction->id, 'id_potongan' => $potongan, 'total' => $total_pot]);
                            }
                        }
                    }
                }

                SchoolDevFeeTransaction::create(['id_transaksi' => $transaction->id, 'total' => $up, 'keterangan' => $request->pembangunan_ket]);
            }

            if ($request->lainnya) {
                if (count($request->total_lainnya) > 0) {
                    for ($i = 0; $i <= (count($request->total_lainnya) - 1); $i++) {
                        OtherTransaction::create(['id_transaksi' => $transaction->id, 'total' => $request->total_lainnya[$i], 'keterangan' => $request->lainnya_ket[$i]]);
                    }
                }
            }

            if ($student->telp_ortu != null && $student->telp_ortu != '' && strlen($student->telp_ortu) > 0 && strlen($student->telp_ortu) < 14) {
                $this->sendMessage($student->telp_ortu, $transaction);
            }

            session()->flash(route('transactions.print', $transaction->id));
        }

        Alert::success('Berhasil', 'Pembayaran berhasil diproses!');
        return redirect()->route('transactions.show', $request->id);
    }

    // transaction details
    public function details(string $id): View
    {
        $data = Transaction::findOrFail($id);
        $student = Student::findOrFail($data->id_siswa);
        $schoolFeeAmount = Fee::where('tahun_angkatan', $student->tahun_angkatan)->first();

        return view('pages.transaction.view', ([
            'data' => $data,
            'student' => $student,
            'schoolFeeAmount' => $schoolFeeAmount
        ]));
    }

    // print
    public function print(string $id): View
    {
        $data = Transaction::findOrFail($id);
        $student = Student::findOrFail($data->id_siswa);
        $schoolFeeAmount = Fee::where('tahun_angkatan', $student->tahun_angkatan)->first();

        return view('pages.transaction.print', [
            'data' => $data,
            'student' => $student,
            'schoolFeeAmount' => $schoolFeeAmount
        ]);
    }

    public function fillId()
    {
        DB::table('transaksi')->where('id_siswa', 0)->orderBy('id')->lazy()->each(function (object $data) {
            if ($data->nis != '' || $data->nis != null) {
                $transaction = Transaction::find($data->id);
                $student = Student::where('nis', $transaction->nis)->first();

                if ($student) {
                    $transaction->id_siswa = $student->id;
                    $transaction->saveQuietly();
                }
            }
        });

        // return Redirect::to('/');
        // return $data;
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
        $listMonths = array();
        $lastMonth = strtotime($lastMonth);

        for ($i = 1; $i <= 18; $i++) {
            $month = date("Y-m-d", strtotime("+" . $i . " month", $lastMonth));
            array_push($listMonths, $month);
        }



        return $listMonths;
    }

    function sendMessage($telp, $transaction)
    {
        $message = Message::findOrFail(1);

        $pesanUtama =
            'Halo ' . $transaction->student->nama . ',
Selamat! Pembayaran uang sekolah Anda telah berhasil diproses.

Rincian Pembayaran :
Nama Siswa: ' . $transaction->student->nama . '
Nomor Induk Siswa: ' . $transaction->nis . '
Kelas: ' . $transaction->student->kelas . '/' . $transaction->student->grup . '
Uang Sekolah : ' . $this->currency($transaction->jumlah_us) . '(' . $transaction->bulan . ')' . '
Uang Pembangunan: ' . $this->currency($transaction->jumlah_up) . '
Uang Lainnya: ' . $this->currency($transaction->jumlah_lainnya) . '
Potongan: ' . $this->currency($transaction->jumlah_potongan) . '
*Jumlah Pembayaran: ' . $this->currency($transaction->total) . '*
Tanggal Pembayaran: ' . date('d/m/Y', strtotime($transaction->tgl_transaksi)) . '

        ';
        $pesan = $pesanUtama . '' . $message->pesan_tambahan;

        // send to jobs
        $data = ['telp' => $telp, 'pesan' => $pesan];
        dispatch(new SendMessageJob($data));

        return true;

        // $client = new Client();
        // $headers = [
        //     'Content-Type' => 'application/x-www-form-urlencoded'
        // ];
        // $options = [
        //     'form_params' => [
        //         'api_key' => $message->api_key,
        //         'sender' => $message->sender,
        //         'number' => $telp,
        //         'message' => $pesan
        //     ]
        // ];
        // $endPoint = new GuzzleHttpRequest('POST', env('WHATSAPP_ENDPOINT') . '/send-message', $headers);
        // $res = $client->sendAsync($endPoint, $options)->wait();
        // $response = json_decode($res->getBody());

        // if ($response->msg == true) {
        //     return 'Pesan notifikasi berhasil dikirim';
        // } else {
        //     return 'Pesan notifikasi gagal dikirim';
        // }
    }

    function currency($expression)
    {
        return "Rp. " . number_format($expression, 0, ',', '.');
    }

    function numberToMonth($month)
    {
        switch ($month) {
            case 1:
                return 'Januari';
            case 2:
                return 'Februari';
            case 3:
                return 'Maret';
            case 4:
                return 'April';
            case 5:
                return 'Mei';
            case 6:
                return 'Juni';
            case 7:
                return 'Juli';
            case 8:
                return 'Agustus';
            case 9:
                return 'September';
            case 10:
                return 'Oktober';
            case 11:
                return 'November';
            case 12:
                return 'Desember';
            default:
                return 'None';
        }
    }
}
