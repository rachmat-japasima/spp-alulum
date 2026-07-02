<?php

namespace App\Services;

use App\Http\Requests\PaymentRequest;
use App\Models\Discount;
use App\Models\DiscountTransaction;
use App\Models\OtherTransaction;
use App\Models\SchoolDevFeeTransaction;
use App\Models\SchoolEquipmentFeeTransaction;
use App\Models\SchoolFeeTransaction;
use App\Models\SchoolMaintenanceFeeTransaction;
use App\Models\Student;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Main payment process.
     */
    public function pay(PaymentRequest $request): Transaction
    {
        return DB::transaction(function () use ($request) {

            /**
             * Generate nomor bukti
             */
            // $this->generateReceiptNumber($request);

            /**
             * Ambil data siswa
             */
            $student = Student::findOrFail($request->student_id);

            /**
             * Ambil semua discount sekali query
             */
            $discounts = $this->loadDiscounts($request);

            /**
             * Build data transaksi
             */
            $calculation = $this->buildTransactionData(
                $request,
                $student,
                $discounts
            );

            /**
             * Simpan transaksi utama
             */
            $transaction = Transaction::create($calculation['data']);

            /**
             * Simpan transaksi SPP
             */
            $this->storeSchoolFee(
                $request,
                $student,
                $transaction,
                $discounts,
                $calculation
            );

            /**
             * Simpan transaksi pembangunan
             */
            $this->storeDevelopmentFee(
                $request,
                $transaction,
                $discounts,
                $calculation
            );

            /**
             * Simpan transaksi pemeliharaan
             */
            $this->storeMaintenanceFee(
                $request,
                $transaction,
                $discounts,
                $calculation
            );

            /**
             * Simpan transaksi perlengkapan
             */
            $this->storeEquipmentFee(
                $request,
                $transaction,
                $calculation
            );

            /**
             * Simpan transaksi lainnya
             */
            $this->storeOtherFee(
                $request,
                $transaction
            );

            /**
             * Update siswa
             */
            $this->updateStudent(
                $student,
                $calculation
            );

            /**
             * WhatsApp Notification
             */
            $this->sendNotification(
                $student,
                $transaction
            );

            return $transaction;
        });
    }

    /**
     * Generate no bukti.
     */
    // private function generateReceiptNumber(PaymentRequest $request): void
    // {
    //     if (
    //         !$request->filled('no_bukti') ||
    //         Transaction::where('no_bukti', $request->no_bukti)->exists()
    //     ) {
    //         $lastNumber = Transaction::whereDate(
    //             'tgl_transaksi',
    //             Carbon::today()
    //         )->count();

    //         $request->merge([
    //             'no_bukti' => Carbon::now('Asia/Jakarta')->format('dmY')
    //                 . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT),
    //         ]);
    //     }
    // }

    /**
     * Load discount sekali query.
     */
    private function loadDiscounts(PaymentRequest $request): Collection
    {
        if (empty($request->potongan)) {
            return collect();
        }

        return Discount::whereIn(
            'id',
            $request->potongan
        )->get();
    }

    /**
     * Menghitung seluruh transaksi.
     */
    private function buildTransactionData(
        PaymentRequest $request,
        Student $student,
        Collection $discounts
    ): array {

        $data = [];

        $data['no_bukti'] = $request->no_bukti;
        $data['id_siswa'] = $request->student_id;
        $data['tahun_ajaran'] = $request->tahun_ajaran;
        $data['nis'] = $student->nis;
        $data['tingkat'] = $request->tingkat;
        $data['tgl_transaksi'] = now();

        $data['jenis'] = $request->boolean('transfer')
            ? 'M-Banking'
            : 'Manual';

        /*
    |--------------------------------------------------------------------------
    | Uang Sekolah
    |--------------------------------------------------------------------------
    */

        $months = collect($request->bulan ?? []);

        $lastMonth = 0;

        if ($months->isNotEmpty()) {

            $lastMonth = $months->last();

            $totalMonth = $months->count();

            $data['jumlah_bulan'] = $totalMonth;

            $data['jumlah_us'] = $totalMonth * $request->uang_sekolah;

            if ($totalMonth > 1) {

                $data['bulan'] =
                    Carbon::parse($months->first())->format('M')
                    . '-'
                    . Carbon::parse($months->last())->format('M');
            } else {

                $data['bulan'] =
                    Carbon::parse($months->first())->format('M');
            }
        } else {

            $data['jumlah_bulan'] = 0;
            $data['jumlah_us'] = 0;
            $data['bulan'] = null;
        }

        /*
    |--------------------------------------------------------------------------
    | Discount
    |--------------------------------------------------------------------------
    */

        $percentUS = 0;
        $percentUP = 0;
        $percentUPP = 0;

        foreach ($discounts as $discount) {

            switch ($discount->jenis) {

                case 'Uang Sekolah':
                    $percentUS += $discount->besaran;
                    break;

                case 'Uang Pembangunan':
                    $percentUP += $discount->besaran;
                    break;

                case 'Uang Pemeliharaan dan Pengembangan':
                    $percentUPP += $discount->besaran;
                    break;
            }
        }

        $potUS = 0;
        $potUP = 0;
        $potUPP = 0;

        if ($months->isNotEmpty()) {

            $potUS =
                (($request->uang_sekolah * $percentUS) / 100)
                * $months->count();
        }

        if ($request->boolean('pembangunan')) {

            $potUP =
                ($request->uang_pembangunan * $percentUP) / 100;
        }

        if ($request->boolean('pemeliharaan')) {

            $potUPP =
                ($request->uang_pemeliharaan * $percentUPP) / 100;
        }

        $data['jumlah_potongan'] =
            $potUS + $potUP + $potUPP;

        /*
    |--------------------------------------------------------------------------
    | Pembangunan
    |--------------------------------------------------------------------------
    */

        $data['jumlah_up'] =
            $request->boolean('pembangunan')
            ? $request->uang_pembangunan
            : 0;

        /*
    |--------------------------------------------------------------------------
    | Pemeliharaan
    |--------------------------------------------------------------------------
    */

        $data['jumlah_upp'] =
            $request->boolean('pemeliharaan')
            ? $request->uang_pemeliharaan
            : 0;

        /*
    |--------------------------------------------------------------------------
    | Perlengkapan
    |--------------------------------------------------------------------------
    */

        $data['jumlah_upk'] =
            $request->boolean('perlengkapan')
            ? $request->uang_perlengkapan
            : 0;

        /*
    |--------------------------------------------------------------------------
    | Lainnya
    |--------------------------------------------------------------------------
    */

        $data['jumlah_lainnya'] =
            $request->boolean('lainnya')
            ? array_sum($request->total_lainnya ?? [])
            : 0;

        /*
    |--------------------------------------------------------------------------
    | Total
    |--------------------------------------------------------------------------
    */

        $data['total'] =

            (

                $data['jumlah_us']
                + $data['jumlah_up']
                + $data['jumlah_upp']
                + $data['jumlah_upk']
                + $data['jumlah_lainnya']

            )

            -

            $data['jumlah_potongan'];

        $data['id_user'] = Auth::id();

        $data['keterangan'] = $request->keterangan;

        $data['status'] = 'Success';

        return [

            'data' => $data,

            'last_month' => $lastMonth,

            'percentUS' => $percentUS,

            'percentUP' => $percentUP,

            'percentUPP' => $percentUPP,

            'potUS' => $potUS,

            'potUP' => $potUP,

            'potUPP' => $potUPP,

            'months' => $months,

        ];
    }

    /**
     * Simpan transaksi uang sekolah.
     */
    private function storeSchoolFee(
        PaymentRequest $request,
        Student $student,
        Transaction $transaction,
        Collection $discounts,
        array $result
    ): void {

        $months = $result['months'];

        if ($months->isEmpty()) {
            return;
        }

        $schoolFee = $request->uang_sekolah;

        /**
         * Apply Discount
         */
        if ($discounts->isNotEmpty()) {

            $schoolFee -=
                ($schoolFee * $result['percentUS']) / 100;

            foreach ($discounts as $discount) {

                if ($discount->jenis !== 'Uang Sekolah') {
                    continue;
                }

                $totalDiscount =
                    (($request->uang_sekolah * $discount->besaran) / 100)
                    * $months->count();

                DiscountTransaction::create([
                    'id_transaksi' => $transaction->id,
                    'id_potongan' => $discount->id,
                    'total' => $totalDiscount,
                ]);
            }
        }

        /**
         * Simpan transaksi SPP per bulan
         */
        foreach ($months as $month) {

            SchoolFeeTransaction::create([

                'id_transaksi' => $transaction->id,

                'bulan' => $this->numberToMonth(
                    Carbon::parse($month)->format('m')
                ),

                'total' => $schoolFee,

            ]);
        }

        /**
         * Update bulan terakhir siswa
         */
        $student->bulan_spp_terakhir = $result['last_month'];

        $student->save();
    }

    /**
     * Simpan transaksi pembangunan.
     */
    private function storeDevelopmentFee(
        PaymentRequest $request,
        Transaction $transaction,
        Collection $discounts,
        array $result
    ): void {

        if (!$request->boolean('pembangunan')) {
            return;
        }

        $developmentFee = $request->uang_pembangunan;

        /**
         * Apply discount
         */
        if ($discounts->isNotEmpty()) {

            if ($result['percentUP'] > 0) {

                $developmentFee -=
                    ($request->uang_pembangunan * $result['percentUP']) / 100;
            }

            foreach ($discounts as $discount) {

                if ($discount->jenis !== 'Uang Pembangunan') {
                    continue;
                }

                $totalDiscount =
                    ($request->uang_pembangunan * $discount->besaran) / 100;

                DiscountTransaction::create([
                    'id_transaksi' => $transaction->id,
                    'id_potongan' => $discount->id,
                    'total' => $totalDiscount,
                ]);
            }
        }

        SchoolDevFeeTransaction::create([

            'id_transaksi' => $transaction->id,

            'total' => $developmentFee,

            'keterangan' => $request->pembangunan_ket,

        ]);
    }

    /**
     * Simpan transaksi pemeliharaan.
     */
    private function storeMaintenanceFee(
        PaymentRequest $request,
        Transaction $transaction,
        Collection $discounts,
        array $result
    ): void {

        if (!$request->boolean('pemeliharaan')) {
            return;
        }

        $maintenanceFee = $request->uang_pemeliharaan;

        /**
         * Apply discount
         */
        if ($discounts->isNotEmpty()) {

            if ($result['percentUPP'] > 0) {

                $maintenanceFee -=
                    ($request->uang_pemeliharaan * $result['percentUPP']) / 100;
            }

            foreach ($discounts as $discount) {

                if (
                    $discount->jenis !==
                    'Uang Pemeliharaan dan Pengembangan'
                ) {
                    continue;
                }

                $totalDiscount =
                    ($request->uang_pemeliharaan * $discount->besaran) / 100;

                DiscountTransaction::create([

                    'id_transaksi' => $transaction->id,

                    'id_potongan' => $discount->id,

                    'total' => $totalDiscount,

                ]);
            }
        }

        SchoolMaintenanceFeeTransaction::create([

            'id_transaksi' => $transaction->id,

            'total' => $maintenanceFee,

            'keterangan' => $request->pemeliharaan_ket,

        ]);
    }

    /**
     * Simpan transaksi perlengkapan.
     */
    private function storeEquipmentFee(
        PaymentRequest $request,
        Transaction $transaction,
        array $result
    ): void {

        if (!$request->boolean('perlengkapan')) {
            return;
        }

        SchoolEquipmentFeeTransaction::create([

            'id_transaksi' => $transaction->id,

            'total' => $request->uang_perlengkapan,

            'keterangan' => $request->perlengkapan_ket,

        ]);
    }

    /**
     * Simpan transaksi lainnya.
     */
    private function storeOtherFee(
        PaymentRequest $request,
        Transaction $transaction
    ): void {

        if (!$request->boolean('lainnya')) {
            return;
        }

        $totals = $request->total_lainnya ?? [];
        $descriptions = $request->lainnya_ket ?? [];

        foreach ($totals as $index => $total) {

            OtherTransaction::create([

                'id_transaksi' => $transaction->id,

                'total' => $total,

                'keterangan' => $descriptions[$index] ?? null,

            ]);
        }
    }

    /**
     * Update data siswa.
     */
    private function updateStudent(
        Student $student,
        array $result
    ): void {

        if (empty($result['last_month'])) {
            return;
        }

        $student->bulan_spp_terakhir = $result['last_month'];

        $student->save();
    }

    /**
     * Kirim notifikasi WA.
     */
    private function sendNotification(
        Student $student,
        Transaction $transaction
    ): void {

        if (
            empty($student->telp_ortu) ||
            strlen($student->telp_ortu) >= 14
        ) {
            return;
        }

        /*
     * Tetap mengikuti controller lama.
     */

        //    $this->sendMessage(
        //        $student->telp_ortu,
        //        $transaction
        //    );
    }

    /**
     * Convert number to month.
     *
     * Pindahkan isi method numberToMonth()
     * dari TransactionController ke sini.
     */
    private function numberToMonth($month)
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
}
