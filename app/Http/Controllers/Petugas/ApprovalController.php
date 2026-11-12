<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectBorrowingRequest;
use App\Models\Borrowing;
use App\Services\BorrowingService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function __construct(private BorrowingService $borrowings)
    {
    }

    public function index(Request $request)
    {
        $status = $request->get('status', Borrowing::STATUS_MENUNGGU);

        $query = Borrowing::with(['user', 'details.tool', 'approver']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $borrowings = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'menunggu' => Borrowing::where('status', Borrowing::STATUS_MENUNGGU)->count(),
            'disetujui' => Borrowing::where('status', Borrowing::STATUS_DISETUJUI)->count(),
            'ditolak' => Borrowing::where('status', Borrowing::STATUS_DITOLAK)->count(),
        ];

        return view('petugas.approvals.index', compact('borrowings', 'counts', 'status'));
    }

    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['user', 'approver', 'details.tool', 'returnRecord']);

        return view('petugas.approvals.show', compact('borrowing'));
    }

    public function approve(Borrowing $borrowing)
    {
        $this->borrowings->approve($borrowing, request()->user());

        return redirect()->route('petugas.approvals.show', $borrowing)
            ->with('success', 'Peminjaman berhasil disetujui.');
    }

    public function reject(RejectBorrowingRequest $request, Borrowing $borrowing)
    {
        $this->borrowings->reject($borrowing, $request->user(), $request->validated()['rejection_reason']);

        return redirect()->route('petugas.approvals.show', $borrowing)
            ->with('success', 'Peminjaman berhasil ditolak.');
    }
}