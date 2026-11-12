<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Services\BorrowingService;

class DashboardController extends Controller
{
    public function __construct(private BorrowingService $borrowings)
    {
    }

    public function index()
    {
        $this->borrowings->refreshLateStatuses();

        $user = auth()->user();

        $stats = [
            'total' => $user->borrowings()->count(),
            'active' => $user->borrowings()->whereIn('status', [
                Borrowing::STATUS_DISETUJUI,
                Borrowing::STATUS_DIPINJAM,
                Borrowing::STATUS_TERLAMBAT,
            ])->count(),
            'pending' => $user->borrowings()->where('status', Borrowing::STATUS_MENUNGGU)->count(),
            'to_return' => $user->borrowings()->whereIn('status', [
                Borrowing::STATUS_DIPINJAM,
                Borrowing::STATUS_TERLAMBAT,
            ])->count(),
        ];

        $recent = $user->borrowings()->with('details.tool')->latest()->take(5)->get();

        $activeBorrowings = $user->borrowings()
            ->with(['details.tool', 'returnRecord'])
            ->whereIn('status', [Borrowing::STATUS_DIPINJAM, Borrowing::STATUS_TERLAMBAT])
            ->orderBy('due_date')
            ->get();

        $notifications = $user->notifications()->latest()->take(5)->get();

        return view('peminjam.dashboard', compact('stats', 'recent', 'activeBorrowings', 'notifications'));
    }
}