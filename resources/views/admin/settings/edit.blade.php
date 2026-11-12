@extends('layouts.dashboard')

@section('title', 'Pengaturan')

@section('content')
    @php
        $value = function (string $key, $default = '') use ($settings) {
            return old($key, $settings[$key]->value ?? $default);
        };
        $checked = function (string $key, bool $default = false) use ($settings) {
            return (bool) old($key, $settings[$key]->typedValue() ?? $default);
        };
    @endphp

    <x-page-header title="Pengaturan Sistem" description="Atur identitas aplikasi, kontak, kebijakan peminjaman, dan pengumuman." />

    <form action="{{ route('admin.settings.update') }}" method="POST" class="mt-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="card card-pad">
            <h2 class="font-display text-base font-semibold text-heading">Identitas Aplikasi</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="app_name" class="label">Nama Aplikasi</label>
                    <input id="app_name" type="text" name="app_name" value="{{ $value('app_name', 'SISTEMA') }}" required class="input @error('app_name') input-error @enderror">
                    @error('app_name')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="app_tagline" class="label">Tagline</label>
                    <input id="app_tagline" type="text" name="app_tagline" value="{{ $value('app_tagline') }}" class="input">
                </div>
                <div class="sm:col-span-2">
                    <label for="app_description" class="label">Deskripsi Singkat</label>
                    <textarea id="app_description" name="app_description" rows="2" class="input">{{ $value('app_description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card card-pad">
            <h2 class="font-display text-base font-semibold text-heading">Kontak</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="contact_email" class="label">Email</label>
                    <input id="contact_email" type="email" name="contact_email" value="{{ $value('contact_email') }}" class="input @error('contact_email') input-error @enderror">
                    @error('contact_email')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="contact_phone" class="label">Telepon</label>
                    <input id="contact_phone" type="text" name="contact_phone" value="{{ $value('contact_phone') }}" class="input">
                </div>
                <div class="sm:col-span-2">
                    <label for="contact_address" class="label">Alamat</label>
                    <input id="contact_address" type="text" name="contact_address" value="{{ $value('contact_address') }}" class="input">
                </div>
            </div>
        </div>

        <div class="card card-pad">
            <h2 class="font-display text-base font-semibold text-heading">Kebijakan Peminjaman</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="max_loan_days" class="label">Maksimal Lama Peminjaman (hari)</label>
                    <input id="max_loan_days" type="number" name="max_loan_days" min="1" value="{{ $value('max_loan_days', 14) }}" required class="input @error('max_loan_days') input-error @enderror">
                    @error('max_loan_days')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="fine_per_day" class="label">Denda per Hari (Rp)</label>
                    <input id="fine_per_day" type="number" name="fine_per_day" min="0" value="{{ $value('fine_per_day', 5000) }}" required class="input @error('fine_per_day') input-error @enderror">
                    @error('fine_per_day')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm text-ink">
                        <input type="checkbox" name="fine_enabled" value="1" class="rounded border-line text-heading focus:ring-navy/30" @checked($checked('fine_enabled', true))>
                        Aktifkan denda keterlambatan pengembalian
                    </label>
                </div>
            </div>
        </div>

        <div class="card card-pad">
            <h2 class="font-display text-base font-semibold text-heading">Pengumuman Beranda</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="announcement_title" class="label">Judul Pengumuman</label>
                    <input id="announcement_title" type="text" name="announcement_title" value="{{ $value('announcement_title') }}" class="input">
                </div>
                <div>
                    <label for="announcement_content" class="label">Isi Pengumuman</label>
                    <textarea id="announcement_content" name="announcement_content" rows="3" class="input">{{ $value('announcement_content') }}</textarea>
                </div>
                <label class="flex items-center gap-2 text-sm text-ink">
                    <input type="checkbox" name="announcement_active" value="1" class="rounded border-line text-heading focus:ring-navy/30" @checked($checked('announcement_active', true))>
                    Tampilkan pengumuman di halaman beranda
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                <i data-lucide="save" class="h-4 w-4"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
@endsection