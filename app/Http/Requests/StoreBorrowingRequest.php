<?php

namespace App\Http\Requests;

use App\Models\Setting;
use App\Models\Tool;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPeminjam() ?? false;
    }

    public function rules(): array
    {
        return [
            'borrow_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:borrow_date'],
            'purpose' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.tool_id' => ['required', 'integer', 'exists:tools,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $items = collect($this->input('items', []));

            if ($items->pluck('tool_id')->filter()->duplicates()->isNotEmpty()) {
                $validator->errors()->add('items', 'Tidak boleh memilih alat yang sama lebih dari satu kali.');
            }

            $tools = Tool::whereIn('id', $items->pluck('tool_id')->filter())->get()->keyBy('id');

            foreach ($items as $index => $item) {
                $tool = $tools[$item['tool_id'] ?? null] ?? null;

                if (! $tool) {
                    continue;
                }

                if ($tool->status !== 'tersedia') {
                    $validator->errors()->add("items.{$index}.quantity", "Alat {$tool->name} sedang tidak tersedia.");
                } elseif ((int) ($item['quantity'] ?? 0) > $tool->stock_available) {
                    $validator->errors()->add("items.{$index}.quantity", "Stok {$tool->name} hanya tersedia {$tool->stock_available} unit.");
                }
            }

            if ($this->user()?->isPeminjam() && $this->filled('borrow_date')
                && Carbon::parse($this->input('borrow_date'))->lt(today())) {
                $validator->errors()->add('borrow_date', 'Tanggal peminjaman tidak boleh di masa lalu.');
            }

            $maxDays = (int) Setting::get('max_loan_days', 0);

            if ($maxDays > 0 && $this->filled('borrow_date') && $this->filled('due_date')) {
                $borrow = Carbon::parse($this->input('borrow_date'));
                $due = Carbon::parse($this->input('due_date'));

                if ($due->diffInDays($borrow) > $maxDays) {
                    $validator->errors()->add('due_date', "Lama peminjaman maksimal {$maxDays} hari.");
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'borrow_date' => 'tanggal peminjaman',
            'due_date' => 'rencana tanggal pengembalian',
            'purpose' => 'keperluan peminjaman',
            'items' => 'daftar alat',
            'items.*.tool_id' => 'alat',
            'items.*.quantity' => 'jumlah',
        ];
    }
}