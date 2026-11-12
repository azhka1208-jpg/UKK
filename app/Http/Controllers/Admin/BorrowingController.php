<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBorrowingRequest;
use App\Http\Requests\RejectBorrowingRequest;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Tool;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\BorrowingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingController extends Controller
{
    public function __construct(private BorrowingService $borrowings)
    {
    }

    public function index(Request $request)
    {
        $query = Borrowing::with(['user', 'details.tool']);

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->get('from')) {
            $query->whereDate('borrow_date', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('borrow_date', '<=', $to);
        }

        $borrowings = $query->latest()->paginate(10)->withQueryString();

        $statusCounts = Borrowing::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.borrowings.index', compact('borrowings', 'statusCounts'));
    }

    public function create()
    {
        $borrowers = User::where('role', User::ROLE_PEMINJAM)->where('is_active', true)->orderBy('name')->get();
        $tools = Tool::with('category')->where('status', 'tersedia')->where('stock_available', '>', 0)->orderBy('name')->get();

        return view('admin.borrowings.create', compact('borrowers', 'tools'));
    }

    public function store(StoreBorrowingRequest $request)
    {
        $data = $request->validated();
        $borrower = User::findOrFail($data['user_id']);

        $borrowing = $this->borrowings->create($borrower, $data, $request->user());

        return redirect()->route('admin.borrowings.show', $borrowing)
            ->with('success', 'Data peminjaman berhasil ditambahkan.');
    }

    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['user', 'approver', 'details.tool', 'returnRecord.details.tool']);

        return view('admin.borrowings.show', compact('borrowing'));
    }

    public function edit(Borrowing $borrowing)
    {
        if ($borrowing->status !== Borrowing::STATUS_MENUNGGU) {
            return redirect()->route('admin.borrowings.show', $borrowing)
                ->with('error', 'Peminjaman yang sudah diproses tidak dapat diubah.');
        }

        $borrowing->load('details');
        $borrowers = User::where('role', User::ROLE_PEMINJAM)->where('is_active', true)->orderBy('name')->get();
        $tools = Tool::with('category')->orderBy('name')->get();

        return view('admin.borrowings.edit', compact('borrowing', 'borrowers', 'tools'));
    }

    public function update(StoreBorrowingRequest $request, Borrowing $borrowing)
    {
        if ($borrowing->status !== Borrowing::STATUS_MENUNGGU) {
            return back()->with('error', 'Peminjaman yang sudah diproses tidak dapat diubah.');
        }

        $data = $request->validated();

        DB::transaction(function () use ($data, $borrowing) {
            $borrowing->update([
                'user_id' => $data['user_id'],
                'borrow_date' => $data['borrow_date'],
                'due_date' => $data['due_date'],
                'purpose' => $data['purpose'],
                'notes' => $data['notes'] ?? null,
            ]);

            $borrowing->details()->delete();
            $total = 0;

            foreach ($data['items'] as $item) {
                $tool = Tool::find($item['tool_id']);

                if (! $tool || $item['quantity'] > $tool->stock_available || $tool->status !== 'tersedia') {
                    throw ValidationException::withMessages([
                        'items' => "Stok alat {$tool?->name} tidak mencukupi.",
                    ]);
                }

                BorrowingDetail::create([
                    'borrowing_id' => $borrowing->id,
                    'tool_id' => $tool->id,
                    'quantity' => $item['quantity'],
                ]);

                $total += $item['quantity'];
            }

            $borrowing->update(['total_items' => $total]);
        });

        ActivityLogger::log('update', "Memperbarui peminjaman {$borrowing->code}.", $borrowing);

        return redirect()->route('admin.borrowings.show', $borrowing)
            ->with('success', 'Data peminjaman berhasil diperbarui.');
    }

    public function destroy(Borrowing $borrowing)
    {
        if (! in_array($borrowing->status, [Borrowing::STATUS_MENUNGGU, Borrowing::STATUS_DITOLAK], true)) {
            return back()->with('error', 'Peminjaman aktif atau selesai tidak dapat dihapus.');
        }

        if ($borrowing->returnRecord()->exists()) {
            return back()->with('error', 'Peminjaman memiliki data pengembalian dan tidak dapat dihapus.');
        }

        $code = $borrowing->code;
        $borrowing->delete();

        ActivityLogger::log('delete', "Menghapus peminjaman {$code}.");

        return redirect()->route('admin.borrowings.index')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }

    public function approve(Borrowing $borrowing)
    {
        $this->borrowings->approve($borrowing, request()->user());

        return back()->with('success', 'Peminjaman berhasil disetujui.');
    }

    public function reject(RejectBorrowingRequest $request, Borrowing $borrowing)
    {
        $this->borrowings->reject($borrowing, $request->user(), $request->validated()['rejection_reason']);

        return back()->with('success', 'Peminjaman berhasil ditolak.');
    }

    public function pickup(Borrowing $borrowing)
    {
        $this->borrowings->markPickedUp($borrowing, request()->user());

        return back()->with('success', 'Alat telah ditandai sebagai diserahkan.');
    }
}