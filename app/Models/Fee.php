<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'daftar_biaya';

    protected $fillable = [
        'tahun_angkatan', 'pembangunan_ra', 'pembangunan_sd', 'pembangunan_smp',
        'pembangunan_sma', 'seleksi_masuk', 'kelas_1', 'kelas_2', 'kelas_3',
        'kelas_4', 'kelas_5', 'kelas_6', 'kelas_7', 'kelas_8', 'kelas_9',
        'kelas_10', 'kelas_11', 'kelas_12', 'ra'
    ];


    protected $hidden = [
        // 
    ];
}
