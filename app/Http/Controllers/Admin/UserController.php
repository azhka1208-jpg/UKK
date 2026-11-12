<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->get('status')) {
            $query->where('is_active', $status === 'aktif');
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        $counts = [
            'all' => User::count(),
            'admin' => User::where('role', User::ROLE_ADMIN)->count(),
            'petugas' => User::where('role', User::ROLE_PETUGAS)->count(),
            'peminjam' => User::where('role', User::ROLE_PEMINJAM)->count(),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    public function create()
    {
        $roles = $this->roles();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'username' => strtolower($data['username']),
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLogger::log('create', "Menambahkan pengguna {$user->name}.", $user);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->loadCount('borrowings');
        $borrowings = $user->borrowings()->with('details.tool')->latest()->take(5)->get();

        return view('admin.users.show', compact('user', 'borrowings'));
    }

    public function edit(User $user)
    {
        $roles = $this->roles();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if ($user->id === auth()->id() && $data['role'] !== User::ROLE_ADMIN) {
            return back()->withInput()->withErrors([
                'role' => 'Anda tidak dapat mengubah role akun Anda sendiri.',
            ]);
        }

        if ($user->id === auth()->id() && ! $request->boolean('is_active')) {
            return back()->withInput()->withErrors([
                'is_active' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.',
            ]);
        }

        $payload = [
            'name' => $data['name'],
            'username' => strtolower($data['username']),
            'email' => strtolower($data['email']),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        ActivityLogger::log('update', "Memperbarui data pengguna {$user->name}.", $user);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->role === User::ROLE_ADMIN && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->with('error', 'Akun administrator terakhir tidak dapat dihapus.');
        }

        if ($user->borrowings()->exists()) {
            return back()->with('error', 'Pengguna memiliki riwayat peminjaman. Nonaktifkan akun alih-alih menghapusnya.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLogger::log('delete', "Menghapus pengguna {$name}.");

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    private function roles(): array
    {
        return [
            User::ROLE_ADMIN => 'Admin',
            User::ROLE_PETUGAS => 'Petugas',
            User::ROLE_PEMINJAM => 'Peminjam',
        ];
    }
}