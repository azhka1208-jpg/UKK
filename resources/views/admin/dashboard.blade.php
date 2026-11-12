@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@section('content')
    <x-page-header title="Dashboard Admin" description="Ringkasan kondisi inventaris dan aktivitas peminjaman.">
        <x-slot:actions>
            <a href="{{ route('admin.borrowings.index') }}" class="btn-secondary btn-sm">Data Peminjaman</a>
            <a href="{{ route('admin.tools.create') }}" class="btn-primary btn-sm">
                <i data-lucide="plus" class="h-4 w-4"></i> Tambah Alat
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-stat-card label="Total Pengguna" :value="$stats['users']" icon="users" tone="navy" hint="Seluruh akun terdaftar" :href="route('admin.users.index')" />
        <x-stat-card label="Total Alat" :value="$stats['tools']" icon="wrench" tone="navy" hint="Jenis alat terdata" :href="route('admin.tools.index')" />
        <x-stat-card label="Total Kategori" :value="$stats['categories']" icon="tags" tone="slate" hint="Kelompok inventaris" :href="route('admin.categories.index')" />
        <x-stat-card label="Peminjaman Aktif" :value="$stats['active']" icon="clipboard-check" tone="warning" hint="Disetujui & sedang dipinjam" :href="route('admin.borrowings.index', ['status' => 'dipinjam'])" />
        <x-stat-card label="Menunggu Persetujuan" :value="$stats['pending']" icon="hourglass" tone="warning" hint="Perlu ditinjau petugas" :href="route('admin.borrowings.index', ['status' => 'menunggu'])" />
        <x-stat-card label="Pengembalian Terlambat" :value="$stats['late']" icon="alarm-clock" tone="danger" hint="Melewati batas waktu" :href="route('admin.borrowings.index')" />
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-3">
        <div class="card card-pad lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display text-base font-semibold text-heading">Peminjaman Bulanan</h2>
                    <p class="text-xs text-ink-secondary">Jumlah pengajuan enam bulan terakhir.</p>
                </div>
                <span class="badge-info">6 bulan</span>
            </div>
            <div class="mt-5 h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <div class="card card-pad">
            <div>
                <h2 class="font-display text-base font-semibold text-heading">Status Peminjaman</h2>
                <p class="text-xs text-ink-secondary">Distribusi status transaksi.</p>
            </div>
            <div class="mt-5 h-64">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <h2 class="font-display text-base font-semibold text-heading">Peminjaman Terbaru</h2>
                <a href="{{ route('admin.borrowings.index') }}" class="link text-sm">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Peminjam</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentBorrowings as $borrowing)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.borrowings.show', $borrowing) }}" class="font-medium text-heading hover:underline">{{ $borrowing->code }}</a>
                                    <p class="text-xs text-ink-secondary">{{ $borrowing->total_items }} item</p>
                                </td>
                                <td>{{ $borrowing->user?->name ?? '-' }}</td>
                                <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->borrow_date?->format('d/m/Y') }}</td>
                                <td><x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state title="Belum ada peminjaman" description="Data peminjaman akan muncul setelah ada pengajuan." icon="clipboard-list" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-5">
            <div class="card card-pad">
                <h2 class="font-display text-base font-semibold text-heading">Pengguna Baru</h2>
                <ul class="mt-4 space-y-3">
                    @forelse ($recentUsers as $item)
                        <li class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-accent text-[11px] font-semibold text-heading">{{ $item->initials() }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-heading">{{ $item->name }}</p>
                                <p class="text-xs text-ink-secondary">{{ $item->role_label }}</p>
                            </div>
                            <span class="text-[11px] text-ink-secondary">{{ $item->created_at?->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-ink-secondary">Belum ada pengguna.</li>
                    @endforelse
                </ul>
            </div>

            <div class="card card-pad">
                <h2 class="font-display text-base font-semibold text-heading">Alat Paling Sering Dipinjam</h2>
                <ul class="mt-4 space-y-3">
                    @forelse ($topTools as $tool)
                        <li class="flex items-center justify-between gap-3 text-sm">
                            <span class="min-w-0 truncate text-ink">{{ $tool->name }}</span>
                            <span class="shrink-0 rounded-full bg-canvas px-2 py-0.5 text-xs text-ink-secondary">{{ $tool->borrowed_count }}x</span>
                        </li>
                    @empty
                        <li class="text-sm text-ink-secondary">Belum ada data.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="mt-6 card">
        <div class="flex items-center justify-between border-b border-line px-5 py-4">
            <h2 class="font-display text-base font-semibold text-heading">Aktivitas Terbaru</h2>
            <a href="{{ route('admin.logs.index') }}" class="link text-sm">Log aktivitas</a>
        </div>
        <ul class="divide-y divide-line">
            @forelse ($recentLogs as $log)
                <li class="flex items-start gap-3 px-5 py-3.5">
                    <span class="mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-canvas text-ink-secondary">
                        <i data-lucide="activity" class="h-3.5 w-3.5"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-ink">{{ $log->description ?: ucfirst($log->action) }}</p>
                        <p class="text-xs text-ink-secondary">{{ $log->user?->name ?? 'Sistem' }} &middot; {{ $log->created_at?->translatedFormat('d M Y H:i') }}</p>
                    </div>
                    <span class="badge-neutral">{{ $log->action }}</span>
                </li>
            @empty
                <li class="px-5 py-6 text-sm text-ink-secondary">Belum ada aktivitas tercatat.</li>
            @endforelse
        </ul>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navy = '#1E3A5F';
            const slateBlue = '#476B91';

            const monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx) {
                new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($monthly['labels']),
                        datasets: [{
                            label: 'Peminjaman',
                            data: @json($monthly['data']),
                            backgroundColor: slateBlue,
                            hoverBackgroundColor: navy,
                            borderRadius: 6,
                            maxBarThickness: 42,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { backgroundColor: navy, padding: 10, cornerRadius: 6 }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: '#718096' } },
                            y: { beginAtZero: true, ticks: { precision: 0, color: '#718096' }, grid: { color: '#E5EAF0' } }
                        }
                    }
                });
            }

            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($statusChart['labels']),
                        datasets: [{
                            data: @json($statusChart['data']),
                            backgroundColor: ['#E6A23C', '#476B91', '#1E3A5F', '#DC5252', '#198754', '#718096'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, color: '#718096', font: { size: 11 } } },
                            tooltip: { backgroundColor: navy, padding: 10, cornerRadius: 6 }
                        }
                    }
                });
            }
        });
    </script>
@endpush