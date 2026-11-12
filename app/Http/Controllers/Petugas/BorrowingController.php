<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
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

        return view('petugas.borrowings.index', compact('borrowings'));
    }

    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['user', 'approver', 'details.tool', 'returnRecord.details.tool']);

        return view('petugas.borrowings.show', compact('borrowing'));
    }
}