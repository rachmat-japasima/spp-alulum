<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Transaction;

class SchoolFeeTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'transaksi_uang_sekolah';

    protected $fillable = ['id_transaksi', 'bulan', 'total'];

    protected $hidden = [];

    public function transaction(): BelongsTo
    {
        return $this->BelongsTo(Transaction::class, 'id_transaksi', 'id');
    }
}
