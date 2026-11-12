<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'borrowing_id' => ['required', 'integer', 'exists:borrowings,id'],
            'return_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'conditions' => ['nullable', 'array'],
            'conditions.*' => [Rule::in(['baik', 'rusak_ringan', 'rusak_berat'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'borrowing_id' => 'data peminjaman',
            'return_date' => 'tanggal pengembalian',
            'conditions' => 'kondisi alat',
        ];
    }
}