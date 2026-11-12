<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Notification;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingService
{
    public function create(User $user, array $data, ?User $actor = null): Borrowing
    {
        $actor = $actor ?? $user;

        return DB::transaction(function () use ($user, $data, $actor) {
            $borrowing = Borrowing::create([
                'code' => $this->generateCode(),
                'user_id' => $user->id,
                'status' => Borrowing::STATUS_MENUNGGU,
                'borrow_date' => $data['borrow_date'],
                'due_date' => $data['due_date'],
                'purpose' => $data['purpose'],
                'notes' => $data['notes'] ?? null,
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $tool = Tool::whereKey($item['tool_id'])->lockForUpdate()->first();

                if (! $tool) {
                    throw ValidationException::withMessages([
                        'items' => 'Alat yang dipilih tidak ditemukan.',
                    ]);
                }

                if ($tool->status !== 'tersedia') {
                    throw ValidationException::withMessages([
                        'items' => "Alat {$tool->name} sedang tidak tersedia untuk dipinjam.",
                    ]);
                }

                if ($item['quantity'] > $tool->stock_available) {
                    throw ValidationException::withMessages([
                        'items' => "Stok alat {$tool->name} tidak mencukupi. Tersedia {$tool->stock_available} unit.",
                    ]);
                }

                BorrowingDetail::create([
                    'borrowing_id' => $borrowing->id,
                    'tool_id' => $tool->id,
                    'quantity' => $item['quantity'],
                    'note' => $item['note'] ?? null,
                ]);

                $total += $item['quantity'];
            }

            $borrowing->update(['total_items' => $total]);

            $this->notifyStaff(
                'Pengajuan peminjaman baru',
                "{$user->name} mengajukan peminjaman {$borrowing->code}.",
                'info',
                route('petugas.approvals.show', $borrowing)
            );

            ActivityLogger::log(
                'create',
                "Mengajukan peminjaman {$borrowing->code} untuk {$user->name}.",
                $borrowing,
                $actor
            );

            return $borrowing->fresh(['details.tool', 'user']);
        });
    }

    public function approve(Borrowing $borrowing, User $approver): Borrowing
    {
        return DB::transaction(function () use ($borrowing, $approver) {
            $borrowing = Borrowing::whereKey($borrowing->id)->lockForUpdate()->firstOrFail();

            if ($borrowing->status !== Borrowing::STATUS_MENUNGGU) {
                throw ValidationException::withMessages([
                    'status' => 'Peminjaman ini sudah diproses sebelumnya.',
                ]);
            }

            foreach ($borrowing->details as $detail) {
                $tool = Tool::whereKey($detail->tool_id)->lockForUpdate()->first();

                if (! $tool || $tool->stock_available < $detail->quantity) {
                    throw ValidationException::withMessages([
                        'stock' => "Stok alat {$tool?->name} tidak mencukupi untuk disetujui.",
                    ]);
                }

                $tool->decrement('stock_available', $detail->quantity);
            }

            $borrowing->update([
                'status' => Borrowing::STATUS_DISETUJUI,
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            Notification::create([
                'user_id' => $borrowing->user_id,
                'title' => 'Peminjaman disetujui',
                'message' => "Pengajuan {$borrowing->code} telah disetujui. Silakan ambil alat sesuai jadwal.",
                'type' => 'success',
                'url' => route('peminjam.borrowings.show', $borrowing),
            ]);

            ActivityLogger::log(
                'approve',
                "Menyetujui peminjaman {$borrowing->code}.",
                $borrowing,
                $approver
            );

            return $borrowing->fresh();
        });
    }

    public function reject(Borrowing $borrowing, User $approver, string $reason): Borrowing
    {
        return DB::transaction(function () use ($borrowing, $approver, $reason) {
            $borrowing = Borrowing::whereKey($borrowing->id)->lockForUpdate()->firstOrFail();

            if ($borrowing->status !== Borrowing::STATUS_MENUNGGU) {
                throw ValidationException::withMessages([
                    'status' => 'Peminjaman ini sudah diproses sebelumnya.',
                ]);
            }

            $borrowing->update([
                'status' => Borrowing::STATUS_DITOLAK,
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'rejection_reason' => $reason,
            ]);

            Notification::create([
                'user_id' => $borrowing->user_id,
                'title' => 'Peminjaman ditolak',
                'message' => "Pengajuan {$borrowing->code} ditolak. Alasan: {$reason}",
                'type' => 'danger',
                'url' => route('peminjam.borrowings.show', $borrowing),
            ]);

            ActivityLogger::log(
                'reject',
                "Menolak peminjaman {$borrowing->code}.",
                $borrowing,
                $approver
            );

            return $borrowing->fresh();
        });
    }

    public function markPickedUp(Borrowing $borrowing, User $petugas): Borrowing
    {
        return DB::transaction(function () use ($borrowing, $petugas) {
            $borrowing = Borrowing::whereKey($borrowing->id)->lockForUpdate()->firstOrFail();

            if ($borrowing->status !== Borrowing::STATUS_DISETUJUI) {
                throw ValidationException::withMessages([
                    'status' => 'Peminjaman belum disetujui atau sudah diambil.',
                ]);
            }

            $borrowing->update([
                'status' => Borrowing::STATUS_DIPINJAM,
                'picked_up_at' => now(),
            ]);

            Notification::create([
                'user_id' => $borrowing->user_id,
                'title' => 'Alat telah diserahkan',
                'message' => "Alat untuk peminjaman {$borrowing->code} telah diserahkan. Harap kembalikan sebelum {$borrowing->due_date->format('d/m/Y')}.",
                'type' => 'info',
                'url' => route('peminjam.borrowings.show', $borrowing),
            ]);

            ActivityLogger::log(
                'pickup',
                "Menyerahkan alat untuk peminjaman {$borrowing->code}.",
                $borrowing,
                $petugas
            );

            return $borrowing->fresh();
        });
    }

    public function refreshLateStatuses(): void
    {
        Borrowing::whereIn('status', [Borrowing::STATUS_DISETUJUI, Borrowing::STATUS_DIPINJAM])
            ->whereDate('due_date', '<', today())
            ->update(['status' => Borrowing::STATUS_TERLAMBAT]);
    }

    public function generateCode(): string
    {
        $year = now()->year;

        $last = Borrowing::where('code', 'like', "PJM-{$year}-%")
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $sequence = 1;

        if ($last) {
            $sequence = (int) substr($last->code, -4) + 1;
        }

        return sprintf('PJM-%s-%04d', $year, $sequence);
    }

    private function notifyStaff(string $title, string $message, string $type, ?string $url = null): void
    {
        $staff = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_PETUGAS])
            ->where('is_active', true)
            ->get();

        foreach ($staff as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'url' => $url,
            ]);
        }
    }
}