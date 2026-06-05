<?php
namespace App\Http\Requests\Dokter;

use Illuminate\Foundation\Http\FormRequest;

class PemeriksaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // We authorize via Controller level authorizeBooking
    }

    public function rules(): array
    {
        return [
            'diagnosis' => ['required', 'string'],
            'catatan' => ['nullable', 'string'],
            'reseps' => ['nullable', 'array'],
            'reseps.*.nama_obat' => ['nullable', 'string', 'max:255'],
            'reseps.*.dosis' => ['nullable', 'string', 'max:255'],
            'reseps.*.aturan_pakai' => ['nullable', 'string', 'max:255'],
        ];
    }
}
