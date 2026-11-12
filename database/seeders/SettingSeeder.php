<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'SISTEMA', 'type' => 'string', 'group' => 'general', 'label' => 'Nama Aplikasi'],
            ['key' => 'app_tagline', 'value' => 'Sistem Peminjaman Alat', 'type' => 'string', 'group' => 'general', 'label' => 'Tagline'],
            ['key' => 'app_description', 'value' => 'Platform digital untuk membantu pengelolaan inventaris, peminjaman, dan pengembalian alat secara terstruktur, transparan, dan efisien.', 'type' => 'string', 'group' => 'general', 'label' => 'Deskripsi Singkat'],
            ['key' => 'contact_email', 'value' => 'admin@sistema.test', 'type' => 'string', 'group' => 'contact', 'label' => 'Email Kontak'],
            ['key' => 'contact_phone', 'value' => '(021) 1234-5678', 'type' => 'string', 'group' => 'contact', 'label' => 'Telepon Kontak'],
            ['key' => 'contact_address', 'value' => 'Jl. Pendidikan No. 10, Jakarta', 'type' => 'string', 'group' => 'contact', 'label' => 'Alamat'],
            ['key' => 'fine_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'loan', 'label' => 'Aktifkan Denda Keterlambatan'],
            ['key' => 'fine_per_day', 'value' => '5000', 'type' => 'integer', 'group' => 'loan', 'label' => 'Denda per Hari (Rp)'],
            ['key' => 'max_loan_days', 'value' => '14', 'type' => 'integer', 'group' => 'loan', 'label' => 'Maksimal Lama Peminjaman (hari)'],
            ['key' => 'announcement_title', 'value' => 'Pengambilan Alat Wajib Menunjukkan Identitas', 'type' => 'string', 'group' => 'announcement', 'label' => 'Judul Pengumuman'],
            ['key' => 'announcement_content', 'value' => 'Peminjam wajib membawa kartu identitas saat mengambil alat di gudang inventaris. Pengembalian dilakukan paling lambat pada tanggal yang tertera di formulir peminjaman.', 'type' => 'string', 'group' => 'announcement', 'label' => 'Isi Pengumuman'],
            ['key' => 'announcement_active', 'value' => '1', 'type' => 'boolean', 'group' => 'announcement', 'label' => 'Tampilkan Pengumuman'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}