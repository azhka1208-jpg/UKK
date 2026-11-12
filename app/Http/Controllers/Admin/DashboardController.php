<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\ReturnRecord;
use App\Models\Tool;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'tools' => Tool::count(),
            'categories' => Category::count(),
            'active' => Borrowing::whereIn('status', [Borrowing::STATUS_DISETUJUI, Borrowing::STATUS_DIPINJAM, Borrowing::STATUS_TERLAMBAT])->count(),
            'pending' => Borrowing::where('status', Borrowing::STATUS_MENUNGGU)->count(),
            'late' => Borrowing::whereIn('status', [Borrowing::STATUS_DISETUJUI, Borrowing::STATUS_DIPINJAM])
                ->whereDate('due_date', '<', today())->count(),
            'return_pending' => ReturnRecord::where('status', 'diajukan')->count(),
        ];

        $monthly = $this->monthlyBorrowings();

        $statusDistribution = Borrowing::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels = [
            Borrowing::STATUS_MENUNGGU => 'Menunggu',
            Borrowing::STATUS_DISETUJUI => 'Disetujui',
            Borrowing::STATUS_DIPINJAM => 'Dipinjam',
            Borrowing::STATUS_TERLAMBAT => 'Terlambat',
            Borrowing::STATUS_SELESAI => 'Selesai',
            Borrowing::STATUS_DITOLAK => 'Ditolak',
        ];

        $statusChart = [
            'labels' => array_values($statusLabels),
            'data' => array_map(fn ($key) => (int) ($statusDistribution[$key] ?? 0), array_keys($statusLabels)),
        ];

        $recentBorrowings = Borrowing::with(['user', 'details.tool'])->latest()->take(6)->get();
        $recentUsers = User::latest()->take(5)->get();
        $recentLogs = ActivityLog::with('user')->latest('created_at')->take(8)->get();

        $topTools = Tool::withCount(['borrowingDetails as borrowed_count' => function ($query) {
            $query->whereHas('borrowing', function ($q) {
                $q->whereIn('status', [
                    Borrowing::STATUS_DISETUJUI,
                    Borrowing::STATUS_DIPINJAM,
                    Borrowing::STATUS_TERLAMBAT,
                    Borrowing::STATUS_SELESAI,
                ]);
            });
        }])->orderByDesc('borrowed_count')->take(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthly',
            'statusChart',
            'recentBorrowings',
            'recentUsers',
            'recentLogs',
            'topTools'
        ));
    }

    private function monthlyBorrowings(): array
    {
        $start = now()->subMonths(5)->startOfMonth();

        $raw = Borrowing::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total")
            ->where('created_at', '>=', $start)
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $labels[] = $date->translatedFormat('M');
            $data[] = (int) ($raw[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}