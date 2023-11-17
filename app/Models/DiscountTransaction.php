<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Transaction;
use App\Models\Discount;

class DiscountTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'transaksi_potongan';

    protected $fillable = [
        'id_transaksi', 'id_potongan', 'total',
    ];

    protected $hidden = [];

    public function transaction(): BelongsTo
    {
        return $this->BelongsTo(Transaction::class, 'id_transaksi', 'id');
    }

    public function discount(): BelongsTo
    {
        return $this->BelongsTo(Discount::class, 'id_potongan', 'id');
    }
}
