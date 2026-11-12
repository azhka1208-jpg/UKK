<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $user->update([
            'name' => $data['name'],
            'username' => strtolower($data['username']),
            'email' => strtolower($data['email']),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        ActivityLogger::log('update', 'Memperbarui profil akun.', $user, $user);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(PasswordUpdateRequest $request)
    {
        $data = $request->validated();

        if (! Hash::check($data['current_password'], $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi lama tidak sesuai.']);
        }

        $request->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        ActivityLogger::log('update', 'Mengubah kata sandi akun.', $request->user(), $request->user());

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }
}