<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Transaction;

class ChangeTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'transaksi_perubahan';

    protected $fillable = [
        'no_bukti', 'id_transaksi', 'tipe_aktifitas',
        'id_siswa', 'up_sebelum', 'up_sesudah', 
        'us_sebelum', 'us_sesudah', 'potongan_sebelum',
        'potongan_sesudah', 'total_sebelum', 'total_sesudah',
        'keterangan'
    ];

    protected $hidden = [];

    public function transaction(): BelongsTo
    {
        return $this->BelongsTo(Transaction::class, 'id_transaksi', 'id');
    }
}
