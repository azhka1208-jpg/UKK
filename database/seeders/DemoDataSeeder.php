<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\ReturnDetail;
use App\Models\ReturnRecord;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Borrowing::exists()) {
            return;
        }

        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $petugas = User::where('role', User::ROLE_PETUGAS)->first();
        $peminjam = User::where('username', 'peminjam')->first();
        $budi = User::where('username', 'budi')->first();

        if (! $admin || ! $petugas || ! $peminjam || ! $budi) {
            return;
        }

        $year = now()->year;

        $this->createBorrowing([
            'code' => "PJM-{$year}-0001",
            'user_id' => $peminjam->id,
            'approved_by' => $petugas->id,
            'status' => Borrowing::STATUS_SELESAI,
            'borrow_date' => now()->subDays(20)->toDateString(),
            'due_date' => now()->subDays(10)->toDateString(),
            'purpose' => 'Presentasi laporan akhir semester.',
            'approved_at' => now()->subDays(20),
            'picked_up_at' => now()->subDays(20),
            'items' => [['code' => 'MLT-001', 'quantity' => 1]],
            'return' => [
                'code' => "PGB-{$year}-0001",
                'return_date' => now()->subDays(12)->toDateString(),
                'status' => 'diverifikasi',
                'late_days' => 0,
                'fine_amount' => 0,
                'verified_at' => now()->subDays(12),
            ],
        ]);

        $this->createBorrowing([
            'code' => "PJM-{$year}-0002",
            'user_id' => $budi->id,
            'approved_by' => $petugas->id,
            'status' => Borrowing::STATUS_DIPINJAM,
            'borrow_date' => now()->subDays(3)->toDateString(),
            'due_date' => now()->addDays(5)->toDateString(),
            'purpose' => 'Praktik konfigurasi jaringan di laboratorium.',
            'approved_at' => now()->subDays(3),
            'picked_up_at' => now()->subDays(3),
            'items' => [['code' => 'JRG-001', 'quantity' => 2]],
        ]);

        $this->createBorrowing([
            'code' => "PJM-{$year}-0003",
            'user_id' => $peminjam->id,
            'approved_by' => null,
            'status' => Borrowing::STATUS_MENUNGGU,
            'borrow_date' => now()->addDay()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'purpose' => 'Pelatihan penggunaan aplikasi perkantoran.',
            'items' => [['code' => 'ELK-001', 'quantity' => 1]],
        ]);

        $this->createBorrowing([
            'code' => "PJM-{$year}-0004",
            'user_id' => $budi->id,
            'approved_by' => $petugas->id,
            'status' => Borrowing::STATUS_DISETUJUI,
            'borrow_date' => now()->subDay()->toDateString(),
            'due_date' => now()->addDays(6)->toDateString(),
            'purpose' => 'Kegiatan sosialisasi di aula sekolah.',
            'approved_at' => now()->subDay(),
            'items' => [['code' => 'MLT-003', 'quantity' => 1]],
        ]);

        $this->createBorrowing([
            'code' => "PJM-{$year}-0005",
            'user_id' => $peminjam->id,
            'approved_by' => $petugas->id,
            'status' => Borrowing::STATUS_TERLAMBAT,
            'borrow_date' => now()->subDays(15)->toDateString(),
            'due_date' => now()->subDays(4)->toDateString(),
            'purpose' => 'Dokumentasi kegiatan sekolah.',
            'approved_at' => now()->subDays(15),
            'picked_up_at' => now()->subDays(15),
            'items' => [['code' => 'MLT-002', 'quantity' => 1]],
        ]);

        $this->createBorrowing([
            'code' => "PJM-{$year}-0006",
            'user_id' => $budi->id,
            'approved_by' => $petugas->id,
            'status' => Borrowing::STATUS_DITOLAK,
            'borrow_date' => now()->subDays(8)->toDateString(),
            'due_date' => now()->subDays(1)->toDateString(),
            'purpose' => 'Kegiatan ekstrakurikuler.',
            'rejection_reason' => 'Alat sudah dialokasikan untuk kegiatan lain pada tanggal yang sama.',
            'approved_at' => now()->subDays(8),
            'items' => [['code' => 'JRG-003', 'quantity' => 1]],
        ]);

        ActivityLog::insert([
            [
                'user_id' => $admin->id,
                'action' => 'seed',
                'description' => 'Data awal aplikasi dimuat.',
                'ip_address' => '127.0.0.1',
                'created_at' => now(),
            ],
            [
                'user_id' => $petugas->id,
                'action' => 'approve',
                'description' => 'Menyetujui peminjaman PJM-' . $year . '-0002.',
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subDays(3),
            ],
        ]);
    }

    private function createBorrowing(array $data): void
    {
        $borrowing = Borrowing::create([
            'code' => $data['code'],
            'user_id' => $data['user_id'],
            'approved_by' => $data['approved_by'],
            'status' => $data['status'],
            'borrow_date' => $data['borrow_date'],
            'due_date' => $data['due_date'],
            'purpose' => $data['purpose'],
            'notes' => $data['notes'] ?? null,
            'rejection_reason' => $data['rejection_reason'] ?? null,
            'approved_at' => $data['approved_at'] ?? null,
            'picked_up_at' => $data['picked_up_at'] ?? null,
        ]);

        $total = 0;

        foreach ($data['items'] as $item) {
            $tool = Tool::where('code', $item['code'])->first();

            if (! $tool) {
                continue;
            }

            BorrowingDetail::create([
                'borrowing_id' => $borrowing->id,
                'tool_id' => $tool->id,
                'quantity' => $item['quantity'],
            ]);

            $total += $item['quantity'];

            if (in_array($data['status'], Borrowing::ACTIVE_STATUSES, true)) {
                $tool->decrement('stock_available', $item['quantity']);
            }
        }

        $borrowing->update(['total_items' => $total]);

        if (isset($data['return'])) {
            $returnData = $data['return'];

            $return = ReturnRecord::create([
                'code' => $returnData['code'],
                'borrowing_id' => $borrowing->id,
                'received_by' => $data['approved_by'],
                'return_date' => $returnData['return_date'],
                'status' => $returnData['status'],
                'late_days' => $returnData['late_days'],
                'fine_amount' => $returnData['fine_amount'],
                'fine_paid' => true,
                'verified_at' => $returnData['verified_at'],
            ]);

            foreach ($data['items'] as $item) {
                $tool = Tool::where('code', $item['code'])->first();

                if (! $tool) {
                    continue;
                }

                ReturnDetail::create([
                    'return_id' => $return->id,
                    'tool_id' => $tool->id,
                    'quantity' => $item['quantity'],
                    'condition' => 'baik',
                ]);
            }
        }
    }
}