@php
    $editing = isset($tool) && $tool;
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="code" class="label">Kode Alat</label>
        <input id="code" type="text" name="code" value="{{ old('code', $editing ? $tool->code : '') }}" required
               class="input font-mono uppercase @error('code') input-error @enderror" placeholder="Contoh: ELK-001">
        @error('code')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="name" class="label">Nama Alat</label>
        <input id="name" type="text" name="name" value="{{ old('name', $editing ? $tool->name : '') }}" required
               class="input @error('name') input-error @enderror" placeholder="Nama alat">
        @error('name')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="category_id" class="label">Kategori</label>
        <select id="category_id" name="category_id" class="input @error('category_id') input-error @enderror" required>
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $editing ? $tool->category_id : '') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="location" class="label">Lokasi Penyimpanan <span class="font-normal text-ink-secondary">(opsional)</span></label>
        <input id="location" type="text" name="location" value="{{ old('location', $editing ? $tool->location : '') }}"
               class="input @error('location') input-error @enderror" placeholder="Contoh: Gudang Lt. 1 - Rak A1">
        @error('location')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="stock_total" class="label">Jumlah Stok</label>
        <input id="stock_total" type="number" name="stock_total" min="0" value="{{ old('stock_total', $editing ? $tool->stock_total : 0) }}" required
               class="input @error('stock_total') input-error @enderror">
        @if ($editing)
            <p class="help-text">Tersedia saat ini: {{ $tool->stock_available }} unit. Stok tersedia dihitung otomatis dari jumlah yang sedang dipinjam.</p>
        @else
            <p class="help-text">Stok tersedia akan disamakan dengan jumlah stok saat dibuat.</p>
        @endif
        @error('stock_total')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="condition" class="label">Kondisi</label>
        <select id="condition" name="condition" class="input @error('condition') input-error @enderror" required>
            <option value="baik" @selected(old('condition', $editing ? $tool->condition : 'baik') === 'baik')>Baik</option>
            <option value="rusak_ringan" @selected(old('condition', $editing ? $tool->condition : '') === 'rusak_ringan')>Rusak Ringan</option>
            <option value="rusak_berat" @selected(old('condition', $editing ? $tool->condition : '') === 'rusak_berat')>Rusak Berat</option>
        </select>
        @error('condition')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="status" class="label">Status Ketersediaan</label>
        <select id="status" name="status" class="input @error('status') input-error @enderror" required>
            <option value="tersedia" @selected(old('status', $editing ? $tool->status : 'tersedia') === 'tersedia')>Tersedia</option>
            <option value="perbaikan" @selected(old('status', $editing ? $tool->status : '') === 'perbaikan')>Dalam Perbaikan</option>
            <option value="tidak_tersedia" @selected(old('status', $editing ? $tool->status : '') === 'tidak_tersedia')>Tidak Tersedia</option>
        </select>
        @error('status')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label for="description" class="label">Deskripsi <span class="font-normal text-ink-secondary">(opsional)</span></label>
        <textarea id="description" name="description" rows="3" class="input @error('description') input-error @enderror" placeholder="Deskripsi singkat alat">{{ old('description', $editing ? $tool->description : '') }}</textarea>
        @error('description')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label for="photo" class="label">Foto Alat <span class="font-normal text-ink-secondary">(opsional, maks. 2 MB)</span></label>
        @if ($editing && $tool->photo)
            <div class="mb-2 flex items-center gap-3">
                <img src="{{ $tool->photo_url }}" alt="Foto {{ $tool->name }}" class="h-16 w-24 rounded-lg border border-line object-cover">
                <span class="text-xs text-ink-secondary">Foto saat ini. Unggah foto baru untuk mengganti.</span>
            </div>
        @endif
        <input id="photo" type="file" name="photo" accept="image/*"
               class="block w-full rounded-lg border border-line bg-surface text-sm file:mr-3 file:border-0 file:bg-accent file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-heading hover:file:bg-accent-dark">
        @error('photo')<p class="error-text">{{ $message }}</p>@enderror
    </div>
</div>