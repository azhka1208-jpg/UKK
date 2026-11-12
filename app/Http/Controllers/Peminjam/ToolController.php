<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tool;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function index(Request $request)
    {
        $query = Tool::with('category');

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }

        if ($request->get('status') === 'tersedia') {
            $query->where('status', 'tersedia')->where('stock_available', '>', 0);
        } elseif ($request->get('status') === 'tidak_tersedia') {
            $query->where(function ($q) {
                $q->where('status', '!=', 'tersedia')->orWhere('stock_available', '<=', 0);
            });
        }

        $tools = $query->orderBy('name')->paginate(9)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('peminjam.tools.index', compact('tools', 'categories'));
    }

    public function show(Tool $tool)
    {
        $tool->load('category');

        return view('peminjam.tools.show', compact('tool'));
    }
}