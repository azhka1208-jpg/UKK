<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\ReturnRecord;
use App\Services\BorrowingService;

class DashboardController extends Controller
{
    public function __construct(private BorrowingService $borrowings)
    {
    }

    public function index()
    {
        $this->borrowings->refreshLateStatuses();

        $stats = [
            'pending' => Borrowing::pending()->count(),
            'active' => Borrowing::whereIn('status', [
                Borrowing::STATUS_DISETUJUI,
                Borrowing::STATUS_DIPINJAM,
                Borrowing::STATUS_TERLAMBAT,
            ])->count(),
            'overdue' => Borrowing::whereIn('status', [Borrowing::STATUS_DIPINJAM, Borrowing::STATUS_TERLAMBAT])
                ->whereDate('due_date', '<', today())->count(),
            'return_pending' => ReturnRecord::where('status', 'diajukan')->count(),
        ];

        $pendingBorrowings = Borrowing::with(['user', 'details.tool'])
            ->pending()
            ->oldest()
            ->take(5)
            ->get();

        $pendingReturns = ReturnRecord::with(['borrowing.user', 'borrowing.details.tool'])
            ->where('status', 'diajukan')
            ->latest()
            ->take(5)
            ->get();

        $dueSoon = Borrowing::with(['user', 'details.tool'])
            ->whereIn('status', [Borrowing::STATUS_DIPINJAM, Borrowing::STATUS_TERLAMBAT])
            ->whereDate('due_date', '<=', today()->addDays(3))
            ->orderBy('due_date')
            ->take(6)
            ->get();

        return view('petugas.dashboard', compact('stats', 'pendingBorrowings', 'pendingReturns', 'dueSoon'));
    }
}