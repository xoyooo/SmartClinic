<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class JadwalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dokter_id'   => 'required|exists:dokters,id',
            'poli_id'     => 'required|exists:polis,id',
            'tanggal'     => 'required|date|after_or_equal:today',
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kuota'       => 'required|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required'        => 'Tanggal jadwal wajib diisi.',
            'tanggal.after_or_equal'  => 'Tanggal jadwal tidak boleh di masa lalu.',
            'jam_selesai.after'       => 'Jam selesai harus lebih besar dari jam mulai.',
            'jam_mulai.date_format'   => 'Format jam mulai tidak valid (gunakan HH:MM).',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid (gunakan HH:MM).',
        ];
    }
}
