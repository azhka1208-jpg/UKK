<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Notification;
use App\Models\ReturnDetail;
use App\Models\ReturnRecord;
use App\Models\Setting;
use App\Models\Tool;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnService
{
    public function submit(Borrowing $borrowing, User $user, array $data, ?User $actor = null): ReturnRecord
    {
        $actor = $actor ?? $user;

        return DB::transaction(function () use ($borrowing, $user, $data, $actor) {
            $borrowing = Borrowing::whereKey($borrowing->id)->lockForUpdate()->firstOrFail();

            if (! in_array($borrowing->status, [Borrowing::STATUS_DIPINJAM, Borrowing::STATUS_TERLAMBAT], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Alat belum berada dalam status dipinjam.',
                ]);
            }

            if ($borrowing->returnRecord()->exists()) {
                throw ValidationException::withMessages([
                    'status' => 'Pengembalian untuk peminjaman ini sudah diajukan.',
                ]);
            }

            $returnDate = isset($data['return_date'])
                ? Carbon::parse($data['return_date'])->startOfDay()
                : today();

            if ($returnDate->isBefore($borrowing->borrow_date)) {
                throw ValidationException::withMessages([
                    'return_date' => 'Tanggal pengembalian tidak boleh sebelum tanggal peminjaman.',
                ]);
            }

            [$lateDays, $fine] = $this->calculateLateFee($borrowing, $returnDate);

            $return = ReturnRecord::create([
                'code' => $this->generateCode(),
                'borrowing_id' => $borrowing->id,
                'return_date' => $returnDate->toDateString(),
                'status' => 'diajukan',
                'late_days' => $lateDays,
                'fine_amount' => $fine,
                'fine_paid' => false,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($borrowing->details as $detail) {
                ReturnDetail::create([
                    'return_id' => $return->id,
                    'tool_id' => $detail->tool_id,
                    'quantity' => $detail->quantity,
                    'condition' => $data['conditions'][$detail->tool_id] ?? 'baik',
                    'note' => $data['notes'] ?? null,
                ]);
            }

            $this->notifyStaff(
                'Pengajuan pengembalian',
                "{$user->name} mengajukan pengembalian untuk {$borrowing->code}.",
                route('petugas.returns.show', $return)
            );

            ActivityLogger::log(
                'return_request',
                "Mengajukan pengembalian {$return->code} untuk {$user->name}.",
                $return,
                $actor
            );

            return $return->fresh(['details.tool', 'borrowing.user', 'borrowing.details.tool']);
        });
    }

    public function verify(ReturnRecord $return, User $officer, array $data): ReturnRecord
    {
        return DB::transaction(function () use ($return, $officer, $data) {
            $return = ReturnRecord::whereKey($return->id)->lockForUpdate()->firstOrFail();

            if ($return->status !== 'diajukan') {
                throw ValidationException::withMessages([
                    'status' => 'Pengembalian ini sudah diverifikasi.',
                ]);
            }

            $borrowing = Borrowing::whereKey($return->borrowing_id)->lockForUpdate()->firstOrFail();

            $returnDate = isset($data['return_date'])
                ? Carbon::parse($data['return_date'])->startOfDay()
                : $return->return_date;

            [$lateDays, $fine] = $this->calculateLateFee($borrowing, $returnDate);

            foreach ($return->details as $detail) {
                $tool = Tool::whereKey($detail->tool_id)->lockForUpdate()->first();

                if (! $tool) {
                    continue;
                }

                $newAvailable = min($tool->stock_available + $detail->quantity, $tool->stock_total);
                $newCondition = $this->worstCondition($tool->condition, $detail->condition);

                $tool->update([
                    'stock_available' => $newAvailable,
                    'condition' => $newCondition,
                ]);
            }

            $return->update([
                'status' => 'diverifikasi',
                'received_by' => $officer->id,
                'return_date' => $returnDate->toDateString(),
                'late_days' => $lateDays,
                'fine_amount' => $fine,
                'fine_paid' => (bool) ($data['fine_paid'] ?? false),
                'notes' => $data['notes'] ?? $return->notes,
                'verified_at' => now(),
            ]);

            $borrowing->update(['status' => Borrowing::STATUS_SELESAI]);

            Notification::create([
                'user_id' => $borrowing->user_id,
                'title' => 'Pengembalian diverifikasi',
                'message' => "Pengembalian untuk {$borrowing->code} telah diverifikasi. Terima kasih.",
                'type' => 'success',
                'url' => route('peminjam.borrowings.show', $borrowing),
            ]);

            ActivityLogger::log(
                'return_verify',
                "Memverifikasi pengembalian {$return->code}.",
                $return,
                $officer
            );

            return $return->fresh(['details.tool', 'borrowing.user']);
        });
    }

    public function calculateLateFee(Borrowing $borrowing, Carbon $returnDate): array
    {
        $due = $borrowing->due_date->copy()->startOfDay();
        $lateDays = $returnDate->greaterThan($due) ? (int) $due->diffInDays($returnDate) : 0;

        $fine = 0.0;

        if ($lateDays > 0 && Setting::get('fine_enabled', true)) {
            $fine = $lateDays * (float) Setting::get('fine_per_day', 0);
        }

        return [$lateDays, $fine];
    }

    public function generateCode(): string
    {
        $year = now()->year;

        $last = ReturnRecord::where('code', 'like', "PGB-{$year}-%")
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $sequence = 1;

        if ($last) {
            $sequence = (int) substr($last->code, -4) + 1;
        }

        return sprintf('PGB-%s-%04d', $year, $sequence);
    }

    private function worstCondition(string $current, string $incoming): string
    {
        $ranking = ['baik' => 0, 'rusak_ringan' => 1, 'rusak_berat' => 2];

        return $ranking[$incoming] > ($ranking[$current] ?? 0) ? $incoming : $current;
    }

    private function notifyStaff(string $title, string $message, ?string $url = null): void
    {
        $staff = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_PETUGAS])
            ->where('is_active', true)
            ->get();

        foreach ($staff as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => 'info',
                'url' => $url,
            ]);
        }
    }
}