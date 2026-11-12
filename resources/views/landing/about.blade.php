@extends('layouts.public')

@section('title', 'Tentang Sistem')

@section('content')
    <section class="border-b border-line bg-surface">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
            <p class="section-eyebrow">Tentang</p>
            <h1 class="mt-2 font-display text-2xl font-semibold text-heading sm:text-3xl">Tentang SISTEMA</h1>
            <p class="mt-1 max-w-2xl text-sm text-ink-secondary">
                SISTEMA adalah sistem peminjaman alat yang membantu pengelolaan inventaris secara terstruktur dan transparan.
            </p>
        </div>
    </section>

    <section class="bg-canvas">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
            <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
                <div class="card card-pad">
                    <h2 class="font-display text-lg font-semibold text-heading">Tujuan sistem</h2>
                    <p class="mt-3 text-sm leading-relaxed text-ink-secondary">
                        SISTEMA dirancang untuk menggantikan pencatatan peminjaman alat secara manual. Seluruh data inventaris,
                        pengajuan peminjaman, persetujuan petugas, dan pengembalian alat tercatat dalam satu sistem terpusat.
                    </p>
                    <p class="mt-3 text-sm leading-relaxed text-ink-secondary">
                        Dengan adanya pencatatan yang rapi, stok alat dapat dipantau secara aktual, riwayat peminjaman dapat
                        ditelusuri kembali, dan proses persetujuan menjadi lebih cepat serta akuntabel.
                    </p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        @foreach ([
                            ['layout-dashboard', 'Terpusat', 'Data inventaris dan transaksi dalam satu sistem.'],
                            ['shield-check', 'Terkontrol', 'Hak akses berbeda untuk admin, petugas, dan peminjam.'],
                            ['history', 'Terlacak', 'Riwayat peminjaman dan pengembalian tersimpan.'],
                        ] as $item)
                            <div class="rounded-lg border border-line bg-canvas p-4">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-accent text-heading">
                                    <i data-lucide="{{ $item[0] }}" class="h-[18px] w-[18px]"></i>
                                </span>
                                <p class="mt-3 font-display text-sm font-semibold text-heading">{{ $item[1] }}</p>
                                <p class="mt-1 text-xs leading-relaxed text-ink-secondary">{{ $item[2] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="card card-pad">
                        <h2 class="font-display text-base font-semibold text-heading">Data sistem</h2>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between border-b border-line pb-3">
                                <dt class="text-ink-secondary">Total alat</dt>
                                <dd class="font-semibold text-heading">{{ $stats['total_tools'] }}</dd>
                            </div>
                            <div class="flex items-center justify-between border-b border-line pb-3">
                                <dt class="text-ink-secondary">Kategori alat</dt>
                                <dd class="font-semibold text-heading">{{ $stats['total_categories'] }}</dd>
                            </div>
                            <div class="flex items-center justify-between border-b border-line pb-3">
                                <dt class="text-ink-secondary">Alat tersedia</dt>
                                <dd class="font-semibold text-heading">{{ $stats['available_tools'] }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-ink-secondary">Total peminjaman</dt>
                                <dd class="font-semibold text-heading">{{ $stats['total_borrowings'] }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="card card-pad">
                        <h2 class="font-display text-base font-semibold text-heading">Kontak</h2>
                        <ul class="mt-4 space-y-3 text-sm text-ink-secondary">
                            @if ($contact['address'])
                                <li class="flex gap-2.5"><i data-lucide="map-pin" class="mt-0.5 h-4 w-4 shrink-0"></i><span>{{ $contact['address'] }}</span></li>
                            @endif
                            @if ($contact['phone'])
                                <li class="flex gap-2.5"><i data-lucide="phone" class="mt-0.5 h-4 w-4 shrink-0"></i><span>{{ $contact['phone'] }}</span></li>
                            @endif
                            @if ($contact['email'])
                                <li class="flex gap-2.5"><i data-lucide="mail" class="mt-0.5 h-4 w-4 shrink-0"></i><span>{{ $contact['email'] }}</span></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection