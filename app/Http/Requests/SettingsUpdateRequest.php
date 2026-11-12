<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'app_name' => ['required', 'string', 'max:80'],
            'app_tagline' => ['nullable', 'string', 'max:120'],
            'app_description' => ['nullable', 'string', 'max:500'],
            'contact_email' => ['nullable', 'email', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'contact_address' => ['nullable', 'string', 'max:200'],
            'fine_enabled' => ['nullable', 'boolean'],
            'fine_per_day' => ['required', 'integer', 'min:0', 'max:100000000'],
            'max_loan_days' => ['required', 'integer', 'min:1', 'max:365'],
            'announcement_title' => ['nullable', 'string', 'max:150'],
            'announcement_content' => ['nullable', 'string', 'max:1000'],
            'announcement_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'app_name' => 'nama aplikasi',
            'app_tagline' => 'tagline',
            'app_description' => 'deskripsi singkat',
            'contact_email' => 'email kontak',
            'contact_phone' => 'telepon kontak',
            'contact_address' => 'alamat',
            'fine_per_day' => 'denda per hari',
            'max_loan_days' => 'maksimal lama peminjaman',
            'announcement_title' => 'judul pengumuman',
            'announcement_content' => 'isi pengumuman',
        ];
    }
}