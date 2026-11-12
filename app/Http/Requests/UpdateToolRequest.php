<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $tool = $this->route('tool');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('tools', 'code')->ignore($tool?->id)],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'stock_total' => ['required', 'integer', 'min:0', 'max:100000'],
            'condition' => ['required', Rule::in(['baik', 'rusak_ringan', 'rusak_berat'])],
            'location' => ['nullable', 'string', 'max:150'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(['tersedia', 'perbaikan', 'tidak_tersedia'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'kode alat',
            'name' => 'nama alat',
            'category_id' => 'kategori',
            'stock_total' => 'jumlah stok',
            'condition' => 'kondisi',
            'location' => 'lokasi penyimpanan',
            'photo' => 'foto alat',
            'status' => 'status',
        ];
    }
}