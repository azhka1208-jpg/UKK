<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Category;
use App\Models\ReturnRecord;
use App\Models\Setting;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnFlowTest extends TestCase
{
    use RefreshDatabase;

    private function makeTool(int $stock = 5): Tool
    {
        $category = Category::create(['name' => 'Kategori Uji ' . uniqid()]);

        return Tool::create([
            'code' => 'T-' . uniqid(),
            'name' => 'Alat Uji',
            'category_id' => $category->id,
            'stock_total' => $stock,
            'stock_available' => $stock,
            'condition' => 'baik',
            'status' => 'tersedia',
        ]);
    }

    private function makeActiveBorrowing(User $peminjam, User $petugas, Tool $tool, int $quantity, $dueDate, string $status = Borrowing::STATUS_DIPINJAM): Borrowing
    {
        $borrowing = Borrowing::create([
            'code' => 'PJM-RET-' . uniqid(),
            'user_id' => $peminjam->id,
            'approved_by' => $petugas->id,
            'status' => $status,
            'borrow_date' => today()->subDays(5),
            'due_date' => $dueDate,
            'purpose' => 'Kegiatan praktik',
            'approved_at' => now(),
            'picked_up_at' => now(),
            'total_items' => $quantity,
        ]);

        BorrowingDetail::create([
            'borrowing_id' => $borrowing->id,
            'tool_id' => $tool->id,
            'quantity' => $quantity,
        ]);

        $tool->decrement('stock_available', $quantity);

        return $borrowing;
    }

    public function test_peminjam_can_submit_return_request(): void
    {
        $peminjam = User::factory()->peminjam()->create();
        $petugas = User::factory()->petugas()->create();
        $tool = $this->makeTool(5);
        $borrowing = $this->makeActiveBorrowing($peminjam, $petugas, $tool, 2, today()->addDays(3));

        $this->actingAs($peminjam)->post(route('peminjam.returns.store', $borrowing), [
            'return_date' => today()->toDateString(),
            'notes' => 'Dikembalikan lengkap',
            'conditions' => [$tool->id => 'baik'],
        ])->assertRedirect();

        $this->assertDatabaseHas('returns', [
            'borrowing_id' => $borrowing->id,
            'status' => 'diajukan',
            'late_days' => 0,
        ]);
        $this->assertDatabaseHas('tools', ['id' => $tool->id, 'stock_available' => 3]);
    }

    public function test_petugas_verification_restores_stock_and_completes_borrowing(): void
    {
        $peminjam = User::factory()->peminjam()->create();
        $petugas = User::factory()->petugas()->create();
        $tool = $this->makeTool(5);
        $borrowing = $this->makeActiveBorrowing($peminjam, $petugas, $tool, 2, today()->addDays(3));

        $this->actingAs($peminjam)->post(route('peminjam.returns.store', $borrowing), [
            'return_date' => today()->toDateString(),
            'conditions' => [$tool->id => 'baik'],
        ]);

        $return = ReturnRecord::firstOrFail();

        $this->actingAs($petugas)->post(route('petugas.returns.verify', $return), [
            'return_date' => today()->toDateString(),
            'fine_paid' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('returns', [
            'id' => $return->id,
            'status' => 'diverifikasi',
            'received_by' => $petugas->id,
        ]);
        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => Borrowing::STATUS_SELESAI,
        ]);
        $this->assertDatabaseHas('tools', ['id' => $tool->id, 'stock_available' => 5]);
    }

    public function test_late_return_calculates_fine(): void
    {
        Setting::create(['key' => 'fine_enabled', 'value' => '1', 'type' => 'boolean']);
        Setting::create(['key' => 'fine_per_day', 'value' => '5000', 'type' => 'integer']);

        $peminjam = User::factory()->peminjam()->create();
        $petugas = User::factory()->petugas()->create();
        $tool = $this->makeTool(5);
        $borrowing = $this->makeActiveBorrowing($peminjam, $petugas, $tool, 1, today()->subDays(2));

        $this->actingAs($peminjam)->post(route('peminjam.returns.store', $borrowing), [
            'return_date' => today()->toDateString(),
            'conditions' => [$tool->id => 'baik'],
        ])->assertRedirect();

        $return = ReturnRecord::firstOrFail();

        $this->assertSame(2, $return->late_days);
        $this->assertEquals(10000, (float) $return->fine_amount);
    }

    public function test_peminjam_cannot_submit_return_for_other_user_borrowing(): void
    {
        $peminjam = User::factory()->peminjam()->create();
        $lain = User::factory()->peminjam()->create();
        $petugas = User::factory()->petugas()->create();
        $tool = $this->makeTool(5);
        $borrowing = $this->makeActiveBorrowing($lain, $petugas, $tool, 1, today()->addDays(3));

        $this->actingAs($peminjam)
            ->get(route('peminjam.returns.create', $borrowing))
            ->assertForbidden();
    }
}