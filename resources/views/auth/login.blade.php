@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <div class="mb-6">
        <h1 class="font-display text-2xl font-semibold text-heading">Masuk ke sistem</h1>
        <p class="mt-1 text-sm text-ink-secondary">Gunakan email atau username yang terdaftar.</p>
    </div>

    <form action="{{ route('login') }}" method="POST" class="card card-pad space-y-5">
        @csrf

        <div>
            <label for="login" class="label">Email atau Username</label>
            <div class="relative">
                <i data-lucide="user-round" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
                <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus autocomplete="username"
                       class="input pl-9 @error('login') input-error @enderror" placeholder="nama@email.com atau username">
            </div>
            @error('login')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ show: false }">
            <div class="flex items-center justify-between">
                <label for="password" class="label">Kata Sandi</label>
            </div>
            <div class="relative">
                <i data-lucide="lock" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
                <input id="password" :type="show ? 'text' : 'password'" type="password" name="password" required autocomplete="current-password"
                       class="input pl-9 pr-10 @error('password') input-error @enderror" placeholder="Masukkan kata sandi">
                <button type="button" x-on:click="show = !show" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md p-1.5 text-ink-secondary hover:text-heading" aria-label="Tampilkan kata sandi">
                    <i data-lucide="eye" class="h-4 w-4" x-show="!show"></i>
                    <i data-lucide="eye-off" class="h-4 w-4" x-show="show" x-cloak></i>
                </button>
            </div>
            @error('password')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-ink-secondary">
            <input type="checkbox" name="remember" value="1" class="rounded border-line text-heading focus:ring-navy/30" @checked(old('remember'))>
            Ingat saya
        </label>

        <button type="submit" class="btn-primary w-full">
            <i data-lucide="log-in" class="h-4 w-4"></i>
            Masuk
        </button>

        <p class="text-center text-sm text-ink-secondary">
            Belum memiliki akun?
            <a href="{{ route('register') }}" class="link">Daftar sebagai peminjam</a>
        </p>
    </form>
@endsection