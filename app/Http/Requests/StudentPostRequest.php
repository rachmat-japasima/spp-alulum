<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'nis' => 'required|string|max:25',
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'alamat' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|in:0,1',
            'agama' => 'required|string|in:0,1',
            'nama_ortu' => 'required|string|max:255',
            'telp_ortu' => 'required|string|max:255',
            'pekerjaan_ortu' => 'required|string|max:255',
            'tingkat' => 'required|string|in:RA,0,1,2',
            'tahun_angkatan' => 'required|string|max:4',
            'kelas' => 'required|string|in:RA,1,2,3,4,5,6,7,8,9,10,11,12',
            'grup' => 'required|string|max:255',
            'smasuk' => 'required|string|in:0,1',
            'smasuk' => 'required|integer|max:10',
            'asal_kelas' => 'required|string|max:255',
            'asal_sekolah' => 'required|string|max:255',
            'tmt_masuk' => 'required|date',
        ];
    }
}
