@extends('layouts.public')

@section('title', 'Panduan Peminjaman')

@section('content')
    <section class="border-b border-line bg-surface">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
            <p class="section-eyebrow">Panduan</p>
            <h1 class="mt-2 font-display text-2xl font-semibold text-heading sm:text-3xl">Panduan Peminjaman Alat</h1>
            <p class="mt-1 max-w-2xl text-sm text-ink-secondary">
                Pelajari alur pengajuan, pengambilan, dan pengembalian alat agar proses berjalan lancar.
            </p>
        </div>
    </section>

    <section class="bg-canvas">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.6fr_1fr]">
            <div class="card card-pad">
                <ol class="space-y-8">
                    @foreach ([
                        ['Login ke sistem', 'Masuk menggunakan akun peminjam yang sudah terdaftar. Jika belum memiliki akun, lakukan pendaftaran terlebih dahulu.'],
                        ['Pilih alat yang tersedia', 'Buka menu Daftar Alat, gunakan pencarian atau filter kategori untuk menemukan alat yang dibutuhkan.'],
                        ['Isi formulir peminjaman', 'Tentukan jumlah alat, tanggal peminjaman, rencana tanggal pengembalian, dan keperluan peminjaman.'],
                        ['Tunggu persetujuan petugas', 'Petugas akan memeriksa ketersediaan dan kelayakan pengajuan. Anda dapat memantau status melalui menu Peminjaman Saya.'],
                        ['Ambil alat', 'Setelah disetujui, ambil alat di gudang inventaris dengan menunjukkan identitas dan kode peminjaman.'],
                        ['Kembalikan alat sesuai jadwal', 'Ajukan pengembalian melalui sistem dan serahkan alat kepada petugas untuk diverifikasi.'],
                    ] as $index => $step)
                        <li class="flex gap-4">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-navy text-sm font-semibold text-white">{{ $index + 1 }}</span>
                            <div>
                                <p class="font-display text-base font-semibold text-heading">{{ $step[0] }}</p>
                                <p class="mt-1 text-sm leading-relaxed text-ink-secondary">{{ $step[1] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="space-y-6">
                <div class="card card-pad">
                    <h2 class="font-display text-base font-semibold text-heading">Ketentuan Peminjaman</h2>
                    <ul class="mt-4 space-y-3 text-sm text-ink-secondary">
                        @foreach ([
                            'Peminjam wajib memiliki akun yang aktif.',
                            'Jumlah peminjaman tidak boleh melebihi stok tersedia.',
                            'Tanggal pengembalian tidak boleh sebelum tanggal peminjaman.',
                            'Alat harus dikembalikan dalam kondisi sesuai saat dipinjam.',
                            'Keterlambatan dapat dikenakan denda sesuai kebijakan yang berlaku.',
                        ] as $rule)
                            <li class="flex gap-2.5">
                                <i data-lucide="check" class="mt-0.5 h-4 w-4 shrink-0 text-success"></i>
                                <span>{{ $rule }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card card-pad bg-navy text-white">
                    <h2 class="font-display text-base font-semibold text-white">Siap mengajukan peminjaman?</h2>
                    <p class="mt-2 text-sm text-white/70">Masuk ke sistem untuk mulai mengajukan peminjaman alat.</p>
                    <div class="mt-4 flex flex-col gap-2">
                        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn bg-white text-navy hover:bg-white/90">
                            {{ auth()->check() ? 'Buka Dashboard' : 'Masuk ke Sistem' }}
                        </a>
                        <a href="{{ route('landing.tools') }}" class="btn border border-white/25 text-white hover:bg-white/10">Lihat Daftar Alat</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection