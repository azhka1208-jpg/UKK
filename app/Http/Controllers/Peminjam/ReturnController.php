<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReturnRequest;
use App\Models\Borrowing;
use App\Models\ReturnRecord;
use App\Services\ReturnService;

class ReturnController extends Controller
{
    public function __construct(private ReturnService $returns)
    {
    }

    public function index()
    {
        $returns = ReturnRecord::with(['borrowing.details.tool', 'details.tool', 'receiver'])
            ->whereHas('borrowing', fn ($q) => $q->where('user_id', auth()->id()))
            ->latest()
            ->paginate(10);

        return view('peminjam.returns.index', compact('returns'));
    }

    public function create(Borrowing $borrowing)
    {
        $this->authorizeOwner($borrowing);

        if (! in_array($borrowing->status, [Borrowing::STATUS_DIPINJAM, Borrowing::STATUS_TERLAMBAT], true)) {
            return redirect()->route('peminjam.borrowings.show', $borrowing)
                ->with('error', 'Pengembalian hanya dapat diajukan untuk alat yang sedang dipinjam.');
        }

        if ($borrowing->returnRecord()->exists()) {
            return redirect()->route('peminjam.returns.show', $borrowing->returnRecord)
                ->with('error', 'Pengembalian untuk peminjaman ini sudah diajukan.');
        }

        $borrowing->load('details.tool');

        return view('peminjam.returns.create', compact('borrowing'));
    }

    public function store(StoreReturnRequest $request, Borrowing $borrowing)
    {
        $this->authorizeOwner($borrowing);

        $return = $this->returns->submit($borrowing, $request->user(), $request->validated());

        return redirect()->route('peminjam.returns.show', $return)
            ->with('success', 'Pengajuan pengembalian berhasil dikirim dan menunggu verifikasi petugas.');
    }

    public function show(ReturnRecord $return)
    {
        abort_unless($return->borrowing?->user_id === auth()->id(), 403, 'Anda tidak memiliki akses ke data ini.');

        $return->load(['borrowing.details.tool', 'details.tool', 'receiver']);

        return view('peminjam.returns.show', compact('return'));
    }

    private function authorizeOwner(Borrowing $borrowing): void
    {
        abort_unless($borrowing->user_id === auth()->id(), 403, 'Anda tidak memiliki akses ke data ini.');
    }
}