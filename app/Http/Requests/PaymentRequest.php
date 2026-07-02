<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $exists = false;

        if ($this->filled('no_bukti')) {
            $exists = Transaction::where(
                'no_bukti',
                $this->no_bukti
            )->exists();
        }

        if (!$this->filled('no_bukti') || $exists) {

            $lastNumber = Transaction::whereDate(
                'tgl_transaksi',
                today()
            )->count();

            $this->merge([
                'no_bukti' => now()->format('dmY')
                    . str_pad(
                        $lastNumber + 1,
                        3,
                        '0',
                        STR_PAD_LEFT
                    ),
            ]);
        }
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'no_bukti' => [
                'required',
                'string',
                'max:255',
                Rule::unique('transaksi')->where(function ($query) {
                    return $query->where('status', 'Success');
                }),
            ],

            'tahun_ajaran' => [
                'required',
                'string',
            ],

            'student_id' => [
                'required',
                'integer',
            ],

            'uang_sekolah' => [
                'required',
                'integer',
            ],

            'tingkat' => [
                'required',
                'string',
            ],

            'uang_pembangunan' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'uang_pemeliharaan' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'uang_perlengkapan' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ];
    }

    /**
     * Optional custom validation messages.
     */
    public function messages(): array
    {
        return [
            'no_bukti.required' => 'Nomor bukti wajib diisi.',
            'no_bukti.unique' => 'Nomor bukti sudah digunakan.',

            'tahun_ajaran.required' => 'Tahun ajaran wajib dipilih.',

            'id.required' => 'Siswa wajib dipilih.',

            'uang_sekolah.required' => 'Nominal uang sekolah wajib diisi.',

            'tingkat.required' => 'Tingkat wajib dipilih.',
        ];
    }
}
