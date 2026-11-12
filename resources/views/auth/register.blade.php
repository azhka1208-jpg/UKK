@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
    <div class="mb-6">
        <h1 class="font-display text-2xl font-semibold text-heading">Daftar akun peminjam</h1>
        <p class="mt-1 text-sm text-ink-secondary">Lengkapi data berikut untuk mengajukan peminjaman alat.</p>
    </div>

    <form action="{{ route('register') }}" method="POST" class="card card-pad space-y-5">
        @csrf

        <div>
            <label for="name" class="label">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="input @error('name') input-error @enderror" placeholder="Nama lengkap">
            @error('name')<p class="error-text">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="username" class="label">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username"
                       class="input @error('username') input-error @enderror" placeholder="username">
                @error('username')<p class="error-text">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="label">Nomor Telepon <span class="font-normal text-ink-secondary">(opsional)</span></label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                       class="input @error('phone') input-error @enderror" placeholder="08xxxxxxxxxx">
                @error('phone')<p class="error-text">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                   class="input @error('email') input-error @enderror" placeholder="nama@email.com">
            @error('email')<p class="error-text">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="label">Kata Sandi</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="input @error('password') input-error @enderror" placeholder="Minimal 8 karakter">
            @error('password')<p class="error-text">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Konfirmasi Kata Sandi</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="input" placeholder="Ulangi kata sandi">
        </div>

        <button type="submit" class="btn-primary w-full">
            <i data-lucide="user-plus" class="h-4 w-4"></i>
            Daftar
        </button>

        <p class="text-center text-sm text-ink-secondary">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="link">Masuk di sini</a>
        </p>
    </form>
@endsection