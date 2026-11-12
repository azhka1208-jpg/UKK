<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\ReturnRecord;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_pages_render(): void
    {
        $admin = User::where('username', 'admin')->firstOrFail();
        $peminjam = User::where('username', 'peminjam')->firstOrFail();
        $tool = Tool::firstOrFail();
        $borrowing = Borrowing::firstOrFail();
        $pending = Borrowing::where('status', 'menunggu')->firstOrFail();
        $return = ReturnRecord::firstOrFail();

        $this->actingAs($admin);

        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.users.index'))->assertOk();
        $this->get(route('admin.users.create'))->assertOk();
        $this->get(route('admin.users.show', $peminjam))->assertOk();
        $this->get(route('admin.users.edit', $peminjam))->assertOk();
        $this->get(route('admin.tools.index'))->assertOk();
        $this->get(route('admin.tools.create'))->assertOk();
        $this->get(route('admin.tools.show', $tool))->assertOk();
        $this->get(route('admin.tools.edit', $tool))->assertOk();
        $this->get(route('admin.categories.index'))->assertOk();
        $this->get(route('admin.categories.create'))->assertOk();
        $this->get(route('admin.borrowings.index'))->assertOk();
        $this->get(route('admin.borrowings.create'))->assertOk();
        $this->get(route('admin.borrowings.show', $borrowing))->assertOk();
        $this->get(route('admin.borrowings.edit', $pending))->assertOk();
        $this->get(route('admin.returns.index'))->assertOk();
        $this->get(route('admin.returns.create'))->assertOk();
        $this->get(route('admin.returns.show', $return))->assertOk();
        $this->get(route('admin.logs.index'))->assertOk();
        $this->get(route('admin.settings.edit'))->assertOk();
    }

    public function test_petugas_pages_render(): void
    {
        $petugas = User::where('username', 'petugas')->firstOrFail();
        $borrowing = Borrowing::firstOrFail();
        $return = ReturnRecord::firstOrFail();

        $this->actingAs($petugas);

        $this->get(route('petugas.dashboard'))->assertOk();
        $this->get(route('petugas.approvals.index'))->assertOk();
        $this->get(route('petugas.approvals.show', $borrowing))->assertOk();
        $this->get(route('petugas.monitoring.index'))->assertOk();
        $this->get(route('petugas.monitoring.show', $borrowing))->assertOk();
        $this->get(route('petugas.returns.index'))->assertOk();
        $this->get(route('petugas.returns.show', $return))->assertOk();
        $this->get(route('petugas.borrowings.index'))->assertOk();
        $this->get(route('petugas.borrowings.show', $borrowing))->assertOk();
        $this->get(route('petugas.reports.index'))->assertOk();
        $this->get(route('petugas.reports.print'))->assertOk();
    }

    public function test_reports_export_pdf_and_excel(): void
    {
        $petugas = User::where('username', 'petugas')->firstOrFail();

        $pdf = $this->actingAs($petugas)->get(route('petugas.reports.pdf'));
        $pdf->assertOk();
        $this->assertStringContainsString('application/pdf', $pdf->headers->get('content-type'));

        $excel = $this->actingAs($petugas)->get(route('petugas.reports.excel'));
        $excel->assertOk();
        $this->assertStringContainsString('spreadsheet', $excel->headers->get('content-type'));
    }

    public function test_peminjam_pages_render(): void
    {
        $peminjam = User::where('username', 'peminjam')->firstOrFail();
        $tool = Tool::firstOrFail();
        $borrowing = $peminjam->borrowings()->firstOrFail();

        $this->actingAs($peminjam);

        $this->get(route('peminjam.dashboard'))->assertOk();
        $this->get(route('peminjam.tools.index'))->assertOk();
        $this->get(route('peminjam.tools.show', $tool))->assertOk();
        $this->get(route('peminjam.borrowings.index'))->assertOk();
        $this->get(route('peminjam.borrowings.create'))->assertOk();
        $this->get(route('peminjam.borrowings.show', $borrowing))->assertOk();
        $this->get(route('peminjam.borrowings.history'))->assertOk();
        $this->get(route('peminjam.returns.index'))->assertOk();
    }

    public function test_shared_pages_render(): void
    {
        $admin = User::where('username', 'admin')->firstOrFail();

        $this->actingAs($admin);

        $this->get(route('profile.edit'))->assertOk();
        $this->get(route('notifications.index'))->assertOk();
    }
}