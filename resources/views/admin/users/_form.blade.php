@php
    $editing = isset($user) && $user;
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label for="name" class="label">Nama Lengkap</label>
        <input id="name" type="text" name="name" value="{{ old('name', $editing ? $user->name : '') }}" required
               class="input @error('name') input-error @enderror" placeholder="Nama lengkap pengguna">
        @error('name')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="username" class="label">Username</label>
        <input id="username" type="text" name="username" value="{{ old('username', $editing ? $user->username : '') }}" required
               class="input @error('username') input-error @enderror" placeholder="username">
        @error('username')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="email" class="label">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $editing ? $user->email : '') }}" required
               class="input @error('email') input-error @enderror" placeholder="nama@email.com">
        @error('email')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="phone" class="label">Nomor Telepon <span class="font-normal text-ink-secondary">(opsional)</span></label>
        <input id="phone" type="text" name="phone" value="{{ old('phone', $editing ? $user->phone : '') }}"
               class="input @error('phone') input-error @enderror" placeholder="08xxxxxxxxxx">
        @error('phone')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="role" class="label">Role</label>
        <select id="role" name="role" class="input @error('role') input-error @enderror" required>
            @foreach ($roles as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $editing ? $user->role : 'peminjam') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('role')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label for="address" class="label">Alamat <span class="font-normal text-ink-secondary">(opsional)</span></label>
        <textarea id="address" name="address" rows="2" class="input @error('address') input-error @enderror" placeholder="Alamat lengkap">{{ old('address', $editing ? $user->address : '') }}</textarea>
        @error('address')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password" class="label">Kata Sandi {{ $editing ? '(kosongkan bila tidak diubah)' : '' }}</label>
        <input id="password" type="password" name="password" {{ $editing ? '' : 'required' }} autocomplete="new-password"
               class="input @error('password') input-error @enderror" placeholder="Minimal 8 karakter">
        @error('password')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password_confirmation" class="label">Konfirmasi Kata Sandi</label>
        <input id="password_confirmation" type="password" name="password_confirmation" {{ $editing ? '' : 'required' }} autocomplete="new-password"
               class="input" placeholder="Ulangi kata sandi">
    </div>

    <div class="sm:col-span-2">
        <label class="flex items-center gap-2 text-sm text-ink">
            <input type="checkbox" name="is_active" value="1" class="rounded border-line text-heading focus:ring-navy/30"
                @checked(old('is_active', $editing ? $user->is_active : true))>
            Akun aktif dan dapat masuk ke sistem
        </label>
        @error('is_active')<p class="error-text">{{ $message }}</p>@enderror
    </div>
</div>