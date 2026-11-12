@extends('layouts.dashboard')

@section('title', 'Profil Saya')

@section('content')
    <x-page-header title="Profil Saya" description="Kelola informasi akun dan keamanan kata sandi." />

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.4fr_1fr]">
        <div class="card card-pad">
            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-navy text-sm font-semibold text-white">{{ $user->initials() }}</span>
                <div>
                    <p class="font-display text-base font-semibold text-heading">{{ $user->name }}</p>
                    <p class="text-xs text-ink-secondary">{{ $user->role_label }} &middot; {{ $user->username }}</p>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="mt-5 space-y-4 border-t border-line pt-5">
                @csrf
                @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="label">Nama Lengkap</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="input @error('name') input-error @enderror">
                        @error('name')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="username" class="label">Username</label>
                        <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" required class="input @error('username') input-error @enderror">
                        @error('username')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="input @error('email') input-error @enderror">
                        @error('email')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="label">Nomor Telepon</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input @error('phone') input-error @enderror">
                        @error('phone')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="address" class="label">Alamat</label>
                        <textarea id="address" name="address" rows="2" class="input @error('address') input-error @enderror">{{ old('address', $user->address) }}</textarea>
                        @error('address')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i data-lucide="save" class="h-4 w-4"></i> Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        <div class="card card-pad">
            <h2 class="font-display text-base font-semibold text-heading">Ubah Kata Sandi</h2>
            <p class="mt-1 text-xs text-ink-secondary">Gunakan kata sandi yang kuat dan mudah Anda ingat.</p>

            <form action="{{ route('profile.password') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="current_password" class="label">Kata Sandi Lama</label>
                    <input id="current_password" type="password" name="current_password" required class="input @error('current_password') input-error @enderror">
                    @error('current_password')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="label">Kata Sandi Baru</label>
                    <input id="password" type="password" name="password" required class="input @error('password') input-error @enderror">
                    @error('password')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="label">Konfirmasi Kata Sandi Baru</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="input">
                </div>
                <button type="submit" class="btn-primary w-full">
                    <i data-lucide="lock" class="h-4 w-4"></i> Ubah Kata Sandi
                </button>
            </form>
        </div>
    </div>
@endsection