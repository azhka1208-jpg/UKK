<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/petugas')->assertRedirect('/login');
        $this->get('/peminjam')->assertRedirect('/login');
    }

    public function test_peminjam_cannot_access_admin_or_petugas_area(): void
    {
        $peminjam = User::factory()->peminjam()->create();

        $this->actingAs($peminjam)->get('/admin')->assertForbidden();
        $this->actingAs($peminjam)->get('/admin/users')->assertForbidden();
        $this->actingAs($peminjam)->get('/petugas')->assertForbidden();
    }

    public function test_petugas_cannot_access_admin_area(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($petugas)->get('/admin')->assertForbidden();
        $this->actingAs($petugas)->get('/admin/settings')->assertForbidden();
    }

    public function test_admin_cannot_access_peminjam_area(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/peminjam')->assertForbidden();
    }

    public function test_each_role_can_access_its_own_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->petugas()->create();
        $peminjam = User::factory()->peminjam()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($petugas)->get('/petugas')->assertOk();
        $this->actingAs($peminjam)->get('/peminjam')->assertOk();
    }
}