<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\SchoolYear;
use App\Models\DiscountTransaction;
use App\Models\SchoolFeeTransaction;
use App\Models\OtherTransaction;
use App\Models\Student;
use App\Models\ChangeTransaction;
use App\Models\Scopes\AncientScope;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'transaksi';

    protected $fillable = [
        'no_bukti', 'id_siswa', 'tahun_ajaran', 'nis', 'tingkat', 'tgl_transaksi',
        'jumlah_bulan', 'bulan', 'jumlah_up', 'jumlah_us', 'jumlah_potongan',
        'jumlah_lainnya', 'total', 'id_user', 'keterangan', 'status', 'jenis'
    ];

    protected $hidden = [];

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'id_user', 'id');
    }

    public function student(): BelongsTo
    {
        return $this->BelongsTo(Student::class, 'id_siswa', 'id');
    }

    public function year(): HasOne
    {
        return $this->HasOne(SchoolYear::class, 'tahun_ajaran', 'tahun_ajaran');
    }

    public function schoolFee(): HasMany
    {
        return $this->hasMany(SchoolFeeTransaction::class, 'id_transaksi', 'id');
    }

    public function schoolDevFee(): HasMany
    {
        return $this->hasMany(SchoolDevFeeTransaction::class, 'id_transaksi', 'id');
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(DiscountTransaction::class, 'id_transaksi', 'id');
    }

    public function otherTransactions(): HasMany
    {
        return $this->hasMany(OtherTransaction::class, 'id_transaksi', 'id');
    }

    public function changeTransaction(): HasMany
    {
        return $this->hasMany(ChangeTransaction::class, 'id_transaksi', 'id');
    }
}
