<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin', 'petugas') ?? false;
    }

    public function rules(): array
    {
        return [
            'return_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'fine_paid' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'return_date' => 'tanggal pengembalian',
            'fine_paid' => 'status pembayaran denda',
        ];
    }
}