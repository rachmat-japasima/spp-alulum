<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Transaction;

class SchoolYear extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tahun_ajaran';

    protected $fillable = ['tahun_ajaran', 'status'];

    protected $hidden = [];

    public function transctions(): BelongsTo
    {
        return $this->BelongsTo(Transaction::class, 'tahun_ajaran', 'tahun_ajaran');
    }
}
