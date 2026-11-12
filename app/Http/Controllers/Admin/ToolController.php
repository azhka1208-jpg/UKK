<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreToolRequest;
use App\Http\Requests\UpdateToolRequest;
use App\Models\Category;
use App\Models\Tool;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ToolController extends Controller
{
    public function index(Request $request)
    {
        $query = Tool::with('category');

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }

        if ($condition = $request->get('condition')) {
            $query->where('condition', $condition);
        }

        if ($status = $request->get('status')) {
            if ($status === 'tersedia') {
                $query->where('status', 'tersedia')->where('stock_available', '>', 0);
            } else {
                $query->where(function ($q) {
                    $q->where('status', '!=', 'tersedia')->orWhere('stock_available', '<=', 0);
                });
            }
        }

        $tools = $query->orderBy('name')->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.tools.index', compact('tools', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.tools.create', compact('categories'));
    }

    public function store(StoreToolRequest $request)
    {
        $data = $request->validated();

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('tools', 'public');
        }

        $tool = Tool::create([
            'code' => strtoupper($data['code']),
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'description' => $data['description'] ?? null,
            'stock_total' => $data['stock_total'],
            'stock_available' => $data['stock_total'],
            'condition' => $data['condition'],
            'location' => $data['location'] ?? null,
            'photo' => $photo,
            'status' => $data['status'],
        ]);

        ActivityLogger::log('create', "Menambahkan alat {$tool->name} ({$tool->code}).", $tool);

        return redirect()->route('admin.tools.index')
            ->with('success', 'Alat berhasil ditambahkan.');
    }

    public function show(Tool $tool)
    {
        $tool->load('category');
        $borrowings = $tool->borrowingDetails()
            ->with(['borrowing.user'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.tools.show', compact('tool', 'borrowings'));
    }

    public function edit(Tool $tool)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.tools.edit', compact('tool', 'categories'));
    }

    public function update(UpdateToolRequest $request, Tool $tool)
    {
        $data = $request->validated();

        $reserved = $tool->stock_total - $tool->stock_available;
        $newTotal = (int) $data['stock_total'];

        if ($newTotal < $reserved) {
            return back()->withInput()->withErrors([
                'stock_total' => "Jumlah stok tidak boleh lebih kecil dari alat yang sedang dipinjam ({$reserved} unit).",
            ]);
        }

        $payload = [
            'code' => strtoupper($data['code']),
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'description' => $data['description'] ?? null,
            'stock_total' => $newTotal,
            'stock_available' => $newTotal - $reserved,
            'condition' => $data['condition'],
            'location' => $data['location'] ?? null,
            'status' => $data['status'],
        ];

        if ($request->hasFile('photo')) {
            if ($tool->photo) {
                Storage::disk('public')->delete($tool->photo);
            }

            $payload['photo'] = $request->file('photo')->store('tools', 'public');
        }

        $tool->update($payload);

        ActivityLogger::log('update', "Memperbarui alat {$tool->name} ({$tool->code}).", $tool);

        return redirect()->route('admin.tools.index')
            ->with('success', 'Data alat berhasil diperbarui.');
    }

    public function destroy(Tool $tool)
    {
        if ($tool->borrowingDetails()->exists()) {
            return back()->with('error', 'Alat memiliki riwayat peminjaman sehingga tidak dapat dihapus. Ubah status ketersediaannya bila diperlukan.');
        }

        if ($tool->photo) {
            Storage::disk('public')->delete($tool->photo);
        }

        $name = $tool->name;
        $tool->delete();

        ActivityLogger::log('delete', "Menghapus alat {$name}.");

        return redirect()->route('admin.tools.index')
            ->with('success', 'Alat berhasil dihapus.');
    }
}