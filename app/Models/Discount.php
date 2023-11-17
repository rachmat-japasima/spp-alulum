<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\DiscountTransaction;
use App\Models\DiscountStudent;

class Discount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'potongan';

    protected $fillable = ['nama', 'besaran', 'jenis', 'keterangan', 'status'];

    protected $hidden = [];

    public function discountTransactions(): HasMany
    {
        return $this->hasMany(DiscountTransaction::class, 'id_potongan', 'id');
    }

    public function discountStudent(): HasMany
    {
        return $this->hasMany(DiscountStudent::class, 'id_potongan', 'id');
    }
}
