<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_user_can_login_with_username(): void
    {
        $user = User::factory()->admin()->create([
            'username' => 'admin_test',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'login' => 'admin_test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_email(): void
    {
        $user = User::factory()->petugas()->create([
            'email' => 'petugas@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', [
            'login' => 'petugas@example.com',
            'password' => 'password',
        ])->assertRedirect(route('petugas.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'username' => 'someone',
            'password' => bcrypt('password'),
        ]);

        $this->from('/login')->post('/login', [
            'login' => 'someone',
            'password' => 'wrong-password',
        ])->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->inactive()->create([
            'username' => 'inactive_user',
            'password' => bcrypt('password'),
        ]);

        $this->from('/login')->post('/login', [
            'login' => 'inactive_user',
            'password' => 'password',
        ])->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_peminjam_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Peminjam Baru',
            'username' => 'peminjam_baru',
            'email' => 'peminjam.baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('peminjam.dashboard'));
        $this->assertDatabaseHas('users', [
            'username' => 'peminjam_baru',
            'role' => User::ROLE_PEMINJAM,
        ]);
    }
}