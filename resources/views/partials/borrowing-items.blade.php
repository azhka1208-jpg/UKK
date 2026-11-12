@php
    $toolsFormData = $tools->map(fn ($tool) => [
        'id' => (string) $tool->id,
        'label' => $tool->name,
        'code' => $tool->code,
        'stock' => (int) $tool->stock_available,
        'status' => $tool->status,
    ])->values();

    $initialItems = collect($initialItems ?? [])
        ->map(fn ($item) => [
            'tool_id' => (string) ($item['tool_id'] ?? ''),
            'quantity' => (int) ($item['quantity'] ?? 1),
        ])
        ->values();

    if ($initialItems->isEmpty()) {
        $initialItems = collect([['tool_id' => '', 'quantity' => 1]]);
    }
@endphp

<div
    class="sm:col-span-2"
    x-data="{
        items: {{ Js::from($initialItems) }},
        tools: {{ Js::from($toolsFormData) }},
        add() {
            this.items.push({ tool_id: '', quantity: 1 });
        },
        remove(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        }
    }"
>
    <div class="flex items-center justify-between">
        <label class="label mb-0">Daftar Alat</label>
        <button type="button" class="btn-soft btn-sm" x-on:click="add()">
            <i data-lucide="plus" class="h-4 w-4"></i> Tambah Baris
        </button>
    </div>

    <div class="mt-2 space-y-2">
        <template x-for="(item, index) in items" :key="index">
            <div class="flex flex-col gap-2 rounded-lg border border-line bg-canvas p-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label class="text-xs text-ink-secondary">Alat</label>
                    <select :name="'items[' + index + '][tool_id]'" x-model="item.tool_id" class="input mt-1">
                        <option value="">Pilih alat</option>
                        <template x-for="tool in tools" :key="tool.id">
                            <option :value="tool.id"
                                x-text="tool.label + ' — ' + tool.code + ' · ' + (tool.status === 'tersedia' ? ('tersedia ' + tool.stock) : 'tidak tersedia')"></option>
                        </template>
                    </select>
                </div>
                <div class="w-full sm:w-28">
                    <label class="text-xs text-ink-secondary">Jumlah</label>
                    <input type="number" min="1" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" class="input mt-1">
                </div>
                <button type="button" class="btn-icon text-danger hover:border-danger/40 hover:text-danger" x-on:click="remove(index)" title="Hapus baris">
                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                </button>
            </div>
        </template>
    </div>

    @error('items')
        <p class="error-text">{{ $message }}</p>
    @enderror
    @error('items.*.tool_id')
        <p class="error-text">{{ $message }}</p>
    @enderror
    @error('items.*.quantity')
        <p class="error-text">{{ $message }}</p>
    @enderror
</div>