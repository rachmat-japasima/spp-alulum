<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Transaction;
use App\Models\Fee;
use App\Models\DiscountStudent;
use Str;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nama',
        'tempat_lahir',
        'tgl_lahir',
        'alamat',
        'jenis_kelamin',
        'agama',
        'nama_ortu',
        'telp_ortu',
        'pekerjaan_ortu',
        'tingkat',
        'tahun_angkatan',
        'kelas',
        'grup',
        'smasuk',
        'status',
        'sisa_uang_pembangunan',
        'bulan_spp_terakhir',
        'bulan_spp_terakhir_old',
        'asal_kelas',
        'asal_sekolah',
        'tmt_masuk'
    ];

    protected $hidden = [];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'id_siswa', 'id');
    }

    public function fee(): HasOne
    {
        return $this->hasOne(Fee::class, 'tahun_angkatan', 'tahun_angkatan');
    }

    public function discountStudent(): HasMany
    {
        return $this->hasMany(DiscountStudent::class, 'id_siswa', 'id');
    }

    public function getDevelopmentFeeStatus(): array
    {
        $level = strtolower($this->convertLevel($this->tingkat));

        $transactionIds = $this->transactions()
            ->where('status', 'Success')
            ->pluck('id');

        $upkBill = $this->fee?->{"pemeliharaan_{$level}"} ?? 0;
        $uppBill = $this->fee?->{"perlengkapan_{$level}"} ?? 0;

        if ($this->tahun_angkatan >= 2026) {

            $upkPaid = SchoolEquipmentFeeTransaction::whereIn(
                'id_transaksi',
                $transactionIds
            )->sum('total');

            $uppPaid = SchoolMaintenanceFeeTransaction::whereIn(
                'id_transaksi',
                $transactionIds
            )->sum('total');

            $uppDiscount = DiscountTransaction::whereIn(
                'id_transaksi',
                $transactionIds
            )
                ->whereHas('discount', function ($query) {
                    $query->where('jenis', 'Uang Pemeliharaan dan Pengembangan');
                })
                ->sum('total');

            $totalBill = $upkBill + $uppBill;
            $totalPaid = $upkPaid + $uppPaid;
            $totalDiscount = $uppDiscount;
        } else {

            $totalBill = $this->fee?->{"pembangunan_{$level}"} ?? 0;
            $upkPaid = 0;
            $uppPaid = 0;

            $totalPaid = SchoolDevFeeTransaction::whereIn(
                'id_transaksi',
                $transactionIds
            )->sum('total');

            $totalDiscount = DiscountTransaction::whereIn(
                'id_transaksi',
                $transactionIds
            )
                ->whereHas('discount', function ($query) {
                    $query->where('jenis', 'Uang Pembangunan');
                })
                ->sum('total');
        }

        $effectiveBill = max(0, $totalBill - $totalDiscount);
        $effectiveBillUPP = max(0, $uppBill - $totalDiscount);
        $effectiveBillUPK = max(0, $upkBill);

        return [
            'bill' => $totalBill,
            'upp_bill' => $uppBill,
            'upk_bill' => $upkBill,
            'discount' => $totalDiscount,
            'effective_bill' => $effectiveBill,
            'paid' => $totalPaid,
            'upk_paid' => $upkPaid,
            'upp_paid' => $uppPaid,
            'remaining' => max(0, $effectiveBill - $totalPaid),
            'is_paid' => $totalPaid >= $effectiveBill,
            'is_upp_paid' => $uppPaid >= $effectiveBillUPP,
            'is_upk_paid' => $uppPaid >= $effectiveBillUPK,
        ];
    }

    public function convertLevel($level)
    {
        $strLevel = '';
        if ($level == '0') {
            $strLevel = 'sd';
        } elseif ($level == '1') {
            $strLevel = 'smp';
        } elseif ($level == '2') {
            $strLevel = 'sma';
        } elseif ($level == 'RA') {
            $strLevel = 'ra';
        }
        return $strLevel;
    }
}
