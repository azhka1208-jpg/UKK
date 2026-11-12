@php
    $editing = isset($borrowing) && $borrowing;
    $defaultItems = $editing
        ? $borrowing->details->map(fn ($detail) => ['tool_id' => $detail->tool_id, 'quantity' => $detail->quantity])->all()
        : [];
    $initialItems = old('items', $defaultItems);
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label for="user_id" class="label">Peminjam</label>
        <select id="user_id" name="user_id" class="input @error('user_id') input-error @enderror" required>
            <option value="">Pilih peminjam</option>
            @foreach ($borrowers as $borrower)
                <option value="{{ $borrower->id }}" @selected((string) old('user_id', $editing ? $borrowing->user_id : '') === (string) $borrower->id)>
                    {{ $borrower->name }} — {{ $borrower->username }}
                </option>
            @endforeach
        </select>
        @error('user_id')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="borrow_date" class="label">Tanggal Peminjaman</label>
        <input id="borrow_date" type="date" name="borrow_date"
               value="{{ old('borrow_date', $editing ? $borrowing->borrow_date->format('Y-m-d') : now()->toDateString()) }}" required
               class="input @error('borrow_date') input-error @enderror">
        @error('borrow_date')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="due_date" class="label">Rencana Tanggal Pengembalian</label>
        <input id="due_date" type="date" name="due_date"
               value="{{ old('due_date', $editing ? $borrowing->due_date->format('Y-m-d') : now()->addDays(7)->toDateString()) }}" required
               class="input @error('due_date') input-error @enderror">
        @error('due_date')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label for="purpose" class="label">Keperluan Peminjaman</label>
        <textarea id="purpose" name="purpose" rows="3" required class="input @error('purpose') input-error @enderror"
                  placeholder="Jelaskan tujuan penggunaan alat">{{ old('purpose', $editing ? $borrowing->purpose : '') }}</textarea>
        @error('purpose')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label for="notes" class="label">Catatan Tambahan <span class="font-normal text-ink-secondary">(opsional)</span></label>
        <textarea id="notes" name="notes" rows="2" class="input @error('notes') input-error @enderror"
                  placeholder="Catatan untuk petugas">{{ old('notes', $editing ? $borrowing->notes : '') }}</textarea>
        @error('notes')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    @include('partials.borrowing-items', ['tools' => $tools, 'initialItems' => $initialItems])
</div>