<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyReturnRequest;
use App\Models\Borrowing;
use App\Models\ReturnRecord;
use App\Services\BorrowingService;
use App\Services\ReturnService;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function __construct(
        private BorrowingService $borrowings,
        private ReturnService $returns
    ) {
    }

    public function index(Request $request)
    {
        $this->borrowings->refreshLateStatuses();

        $query = Borrowing::with(['user', 'details.tool', 'returnRecord'])
            ->whereIn('status', [
                Borrowing::STATUS_DISETUJUI,
                Borrowing::STATUS_DIPINJAM,
                Borrowing::STATUS_TERLAMBAT,
            ]);

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->get('status') === 'terlambat') {
            $query->whereDate('due_date', '<', today());
        }

        $borrowings = $query->orderBy('due_date')->paginate(10, ['*'], 'page')->withQueryString();

        $pendingReturns = ReturnRecord::with(['borrowing.user', 'borrowing.details.tool'])
            ->where('status', 'diajukan')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'active' => Borrowing::whereIn('status', [Borrowing::STATUS_DIPINJAM, Borrowing::STATUS_TERLAMBAT])->count(),
            'overdue' => Borrowing::whereIn('status', [Borrowing::STATUS_DIPINJAM, Borrowing::STATUS_TERLAMBAT])
                ->whereDate('due_date', '<', today())->count(),
            'return_pending' => ReturnRecord::where('status', 'diajukan')->count(),
        ];

        return view('petugas.monitoring.index', compact('borrowings', 'pendingReturns', 'stats'));
    }

    public function showBorrowing(Borrowing $borrowing)
    {
        $borrowing->load(['user', 'approver', 'details.tool', 'returnRecord.details.tool']);

        return view('petugas.monitoring.show', compact('borrowing'));
    }

    public function pickup(Borrowing $borrowing)
    {
        $this->borrowings->markPickedUp($borrowing, request()->user());

        return redirect()->route('petugas.monitoring.show', $borrowing)
            ->with('success', 'Alat telah ditandai sebagai diserahkan kepada peminjam.');
    }

    public function returns(Request $request)
    {
        $query = ReturnRecord::with(['borrowing.user', 'receiver']);

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('borrowing', fn ($b) => $b->where('code', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $returns = $query->latest()->paginate(10)->withQueryString();

        return view('petugas.returns.index', compact('returns'));
    }

    public function showReturn(ReturnRecord $return)
    {
        $return->load(['borrowing.user', 'borrowing.details.tool', 'details.tool', 'receiver']);

        return view('petugas.returns.show', compact('return'));
    }

    public function verifyReturn(VerifyReturnRequest $request, ReturnRecord $return)
    {
        $this->returns->verify($return, $request->user(), $request->validated());

        return redirect()->route('petugas.returns.show', $return)
            ->with('success', 'Pengembalian berhasil diverifikasi.');
    }
}