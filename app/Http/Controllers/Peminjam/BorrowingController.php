<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBorrowingRequest;
use App\Models\Borrowing;
use App\Models\Tool;
use App\Services\BorrowingService;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function __construct(private BorrowingService $borrowings)
    {
    }

    public function index(Request $request)
    {
        $query = auth()->user()->borrowings()->with(['details.tool', 'returnRecord']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $borrowings = $query->latest()->paginate(10)->withQueryString();

        return view('peminjam.borrowings.index', compact('borrowings'));
    }

    public function create(Request $request)
    {
        $tools = Tool::with('category')
            ->where('status', 'tersedia')
            ->where('stock_available', '>', 0)
            ->orderBy('name')
            ->get();

        $selectedId = $request->get('tool');

        return view('peminjam.borrowings.create', compact('tools', 'selectedId'));
    }

    public function store(StoreBorrowingRequest $request)
    {
        $borrowing = $this->borrowings->create($request->user(), $request->validated());

        return redirect()->route('peminjam.borrowings.show', $borrowing)
            ->with('success', 'Pengajuan peminjaman berhasil dikirim dan menunggu persetujuan petugas.');
    }

    public function show(Borrowing $borrowing)
    {
        $this->authorizeOwner($borrowing);

        $borrowing->load(['details.tool', 'approver', 'returnRecord.details.tool']);

        return view('peminjam.borrowings.show', compact('borrowing'));
    }

    public function history(Request $request)
    {
        $query = auth()->user()->borrowings()
            ->with(['details.tool', 'returnRecord'])
            ->whereIn('status', [Borrowing::STATUS_SELESAI, Borrowing::STATUS_DITOLAK]);

        $borrowings = $query->latest()->paginate(10)->withQueryString();

        return view('peminjam.borrowings.history', compact('borrowings'));
    }

    private function authorizeOwner(Borrowing $borrowing): void
    {
        abort_unless($borrowing->user_id === auth()->id(), 403, 'Anda tidak memiliki akses ke data ini.');
    }
}