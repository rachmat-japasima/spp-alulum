<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Student;
use App\Models\Discount;

class DiscountStudent extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'siswa_potongan';

    protected $fillable = ['id_siswa', 'id_potongan', 'keterangan', 'status'];

    protected $hidden = [];

    public function student(): BelongsTo
    {
        return $this->BelongsTo(Student::class, 'id_siswa', 'id');
    }

    public function discount(): BelongsTo
    {
        return $this->BelongsTo(Discount::class, 'id_potongan', 'id');
    }
}
