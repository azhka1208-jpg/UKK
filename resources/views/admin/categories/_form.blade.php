@php $editing = isset($category) && $category; @endphp

<div class="space-y-5">
    <div>
        <label for="name" class="label">Nama Kategori</label>
        <input id="name" type="text" name="name" value="{{ old('name', $editing ? $category->name : '') }}" required
               class="input @error('name') input-error @enderror" placeholder="Contoh: Alat Multimedia">
        @error('name')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="description" class="label">Deskripsi <span class="font-normal text-ink-secondary">(opsional)</span></label>
        <textarea id="description" name="description" rows="3" class="input @error('description') input-error @enderror"
                  placeholder="Deskripsi singkat kategori">{{ old('description', $editing ? $category->description : '') }}</textarea>
        @error('description')<p class="error-text">{{ $message }}</p>@enderror
    </div>
</div>