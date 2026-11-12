<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Category;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowingFlowTest extends TestCase
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

    public function test_peminjam_can_submit_borrowing_request(): void
    {
        $peminjam = User::factory()->peminjam()->create();
        $tool = $this->makeTool(5);

        $response = $this->actingAs($peminjam)->post(route('peminjam.borrowings.store'), [
            'borrow_date' => today()->toDateString(),
            'due_date' => today()->addDays(5)->toDateString(),
            'purpose' => 'Kegiatan praktik',
            'items' => [
                ['tool_id' => $tool->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('borrowings', [
            'user_id' => $peminjam->id,
            'status' => Borrowing::STATUS_MENUNGGU,
            'total_items' => 2,
        ]);
        $this->assertDatabaseHas('borrowing_details', [
            'tool_id' => $tool->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('tools', ['id' => $tool->id, 'stock_available' => 5]);
    }

    public function test_borrowing_quantity_cannot_exceed_stock(): void
    {
        $peminjam = User::factory()->peminjam()->create();
        $tool = $this->makeTool(1);

        $this->actingAs($peminjam)->post(route('peminjam.borrowings.store'), [
            'borrow_date' => today()->toDateString(),
            'due_date' => today()->addDays(3)->toDateString(),
            'purpose' => 'Uji stok',
            'items' => [
                ['tool_id' => $tool->id, 'quantity' => 5],
            ],
        ])->assertSessionHasErrors();

        $this->assertDatabaseCount('borrowings', 0);
    }

    public function test_petugas_can_approve_and_stock_decreases(): void
    {
        $peminjam = User::factory()->peminjam()->create();
        $petugas = User::factory()->petugas()->create();
        $tool = $this->makeTool(5);

        $this->actingAs($peminjam)->post(route('peminjam.borrowings.store'), [
            'borrow_date' => today()->toDateString(),
            'due_date' => today()->addDays(5)->toDateString(),
            'purpose' => 'Kegiatan praktik',
            'items' => [['tool_id' => $tool->id, 'quantity' => 2]],
        ]);

        $borrowing = Borrowing::firstOrFail();

        $this->actingAs($petugas)
            ->post(route('petugas.approvals.approve', $borrowing))
            ->assertRedirect();

        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => Borrowing::STATUS_DISETUJUI,
            'approved_by' => $petugas->id,
        ]);
        $this->assertDatabaseHas('tools', ['id' => $tool->id, 'stock_available' => 3]);
    }

    public function test_petugas_can_mark_tool_as_picked_up(): void
    {
        $peminjam = User::factory()->peminjam()->create();
        $petugas = User::factory()->petugas()->create();
        $tool = $this->makeTool(5);

        $borrowing = Borrowing::create([
            'code' => 'PJM-TEST-1',
            'user_id' => $peminjam->id,
            'approved_by' => $petugas->id,
            'status' => Borrowing::STATUS_DISETUJUI,
            'borrow_date' => today(),
            'due_date' => today()->addDays(5),
            'purpose' => 'Uji',
            'total_items' => 1,
        ]);
        BorrowingDetail::create(['borrowing_id' => $borrowing->id, 'tool_id' => $tool->id, 'quantity' => 1]);
        $tool->update(['stock_available' => 4]);

        $this->actingAs($petugas)
            ->post(route('petugas.monitoring.pickup', $borrowing))
            ->assertRedirect();

        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => Borrowing::STATUS_DIPINJAM,
        ]);
    }

    public function test_approval_fails_when_stock_insufficient(): void
    {
        $peminjam = User::factory()->peminjam()->create();
        $petugas = User::factory()->petugas()->create();
        $tool = $this->makeTool(5);

        $borrowing = Borrowing::create([
            'code' => 'PJM-TEST-2',
            'user_id' => $peminjam->id,
            'status' => Borrowing::STATUS_MENUNGGU,
            'borrow_date' => today(),
            'due_date' => today()->addDays(5),
            'purpose' => 'Uji',
            'total_items' => 3,
        ]);
        BorrowingDetail::create(['borrowing_id' => $borrowing->id, 'tool_id' => $tool->id, 'quantity' => 3]);
        $tool->update(['stock_available' => 1]);

        $this->actingAs($petugas)
            ->post(route('petugas.approvals.approve', $borrowing))
            ->assertSessionHasErrors('stock');

        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => Borrowing::STATUS_MENUNGGU,
        ]);
        $this->assertDatabaseHas('tools', ['id' => $tool->id, 'stock_available' => 1]);
    }

    public function test_peminjam_cannot_approve_borrowing(): void
    {
        $peminjam = User::factory()->peminjam()->create();
        $tool = $this->makeTool(5);

        $borrowing = Borrowing::create([
            'code' => 'PJM-TEST-3',
            'user_id' => $peminjam->id,
            'status' => Borrowing::STATUS_MENUNGGU,
            'borrow_date' => today(),
            'due_date' => today()->addDays(5),
            'purpose' => 'Uji',
            'total_items' => 1,
        ]);
        BorrowingDetail::create(['borrowing_id' => $borrowing->id, 'tool_id' => $tool->id, 'quantity' => 1]);

        $this->actingAs($peminjam)
            ->post(route('petugas.approvals.approve', $borrowing))
            ->assertForbidden();
    }
}