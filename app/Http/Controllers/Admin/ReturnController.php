<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReturnRequest;
use App\Http\Requests\VerifyReturnRequest;
use App\Models\Borrowing;
use App\Models\ReturnRecord;
use App\Services\ActivityLogger;
use App\Services\ReturnService;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function __construct(private ReturnService $returns)
    {
    }

    public function index(Request $request)
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

        $counts = [
            'all' => ReturnRecord::count(),
            'diajukan' => ReturnRecord::where('status', 'diajukan')->count(),
            'diverifikasi' => ReturnRecord::where('status', 'diverifikasi')->count(),
        ];

        return view('admin.returns.index', compact('returns', 'counts'));
    }

    public function create(Request $request)
    {
        $selected = null;

        if ($request->filled('borrowing_id')) {
            $selected = Borrowing::with(['user', 'details.tool', 'returnRecord'])
                ->findOrFail($request->get('borrowing_id'));

            if (! in_array($selected->status, [Borrowing::STATUS_DIPINJAM, Borrowing::STATUS_TERLAMBAT], true)) {
                return redirect()->route('admin.returns.create')
                    ->with('error', 'Peminjaman tersebut belum berada dalam status dipinjam.');
            }

            if ($selected->returnRecord) {
                return redirect()->route('admin.returns.show', $selected->returnRecord)
                    ->with('error', 'Pengembalian untuk peminjaman ini sudah tercatat.');
            }
        }

        $activeBorrowings = Borrowing::with(['user', 'details.tool'])
            ->whereIn('status', [Borrowing::STATUS_DIPINJAM, Borrowing::STATUS_TERLAMBAT])
            ->whereDoesntHave('returnRecord')
            ->latest()
            ->get();

        return view('admin.returns.create', compact('selected', 'activeBorrowings'));
    }

    public function store(StoreReturnRequest $request)
    {
        $data = $request->validated();
        $borrowing = Borrowing::with('details')->findOrFail($data['borrowing_id']);

        $return = $this->returns->submit($borrowing, $borrowing->user, $data, $request->user());

        return redirect()->route('admin.returns.show', $return)
            ->with('success', 'Pengembalian berhasil dicatat dan menunggu verifikasi.');
    }

    public function show(ReturnRecord $return)
    {
        $return->load(['borrowing.user', 'borrowing.details.tool', 'details.tool', 'receiver']);

        return view('admin.returns.show', compact('return'));
    }

    public function update(Request $request, ReturnRecord $return)
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'fine_paid' => ['nullable', 'boolean'],
        ]);

        if ($return->status !== 'diverifikasi') {
            return back()->with('error', 'Hanya pengembalian yang sudah diverifikasi yang dapat diperbarui catatannya.');
        }

        $return->update([
            'notes' => $data['notes'] ?? $return->notes,
            'fine_paid' => $request->boolean('fine_paid'),
        ]);

        ActivityLogger::log('update', "Memperbarui catatan pengembalian {$return->code}.", $return);

        return back()->with('success', 'Data pengembalian berhasil diperbarui.');
    }

    public function verify(VerifyReturnRequest $request, ReturnRecord $return)
    {
        $this->returns->verify($return, $request->user(), $request->validated());

        return redirect()->route('admin.returns.show', $return)
            ->with('success', 'Pengembalian berhasil diverifikasi.');
    }

    public function destroy(ReturnRecord $return)
    {
        if ($return->status !== 'diajukan') {
            return back()->with('error', 'Pengembalian yang sudah diverifikasi tidak dapat dihapus.');
        }

        $borrowing = $return->borrowing;
        $code = $return->code;
        $return->delete();

        if ($borrowing) {
            $status = $borrowing->due_date->isBefore(today())
                ? Borrowing::STATUS_TERLAMBAT
                : Borrowing::STATUS_DIPINJAM;

            $borrowing->update(['status' => $status]);
        }

        ActivityLogger::log('delete', "Menghapus pengajuan pengembalian {$code}.");

        return redirect()->route('admin.returns.index')
            ->with('success', 'Pengajuan pengembalian berhasil dihapus.');
    }
}