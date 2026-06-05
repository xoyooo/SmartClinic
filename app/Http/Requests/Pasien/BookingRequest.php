<?php
namespace App\Http\Requests\Pasien;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jadwal_id'  => ['required', 'exists:jadwal_praktiks,id'],
            'tanggal'    => ['required', 'date', 'after_or_equal:today'],
            'slot_waktu' => ['required', 'date_format:H:i'],
            'keluhan'    => ['required', 'string', 'min:5'],
        ];
    }
}
