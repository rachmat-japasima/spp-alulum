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

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'siswa';

    protected $fillable = [
        'nis', 'nama', 'tempat_lahir', 'tgl_lahir', 'alamat', 'jenis_kelamin', 'agama', 'nama_ortu',
        'telp_ortu', 'pekerjaan_ortu', 'tingkat', 'tahun_angkatan', 'kelas', 'grup', 'smasuk', 
        'status', 'sisa_uang_pembangunan', 'bulan_spp_terakhir', 'bulan_spp_terakhir_old', 'asal_kelas', 'asal_sekolah', 'tmt_masuk'
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
}
