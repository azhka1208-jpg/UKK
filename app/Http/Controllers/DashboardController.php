<?php

namespace App\Http\Controllers;

use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return match ($user->role) {
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_PETUGAS => redirect()->route('petugas.dashboard'),
            default => redirect()->route('peminjam.dashboard'),
        };
    }
}