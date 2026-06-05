<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PoliRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_poli' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ];
    }
}
