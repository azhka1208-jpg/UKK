<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Tool;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $stats = $this->statistics();

        $tools = Tool::with('category')
            ->orderByRaw("FIELD(status, 'tersedia', 'perbaikan', 'tidak_tersedia')")
            ->orderByDesc('stock_available')
            ->take(6)
            ->get();

        $categories = Category::withCount('tools')->orderBy('name')->get();

        $announcement = [
            'active' => (bool) Setting::get('announcement_active', false),
            'title' => Setting::get('announcement_title'),
            'content' => Setting::get('announcement_content'),
        ];

        $contact = [
            'email' => Setting::get('contact_email'),
            'phone' => Setting::get('contact_phone'),
            'address' => Setting::get('contact_address'),
        ];

        return view('landing.index', compact('stats', 'tools', 'categories', 'announcement', 'contact'));
    }

    public function tools(Request $request)
    {
        $query = Tool::with('category');

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
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

        return view('landing.tools', compact('tools', 'categories'));
    }

    public function showTool(Tool $tool)
    {
        $tool->load('category');

        $related = Tool::with('category')
            ->where('category_id', $tool->category_id)
            ->whereKeyNot($tool->id)
            ->take(3)
            ->get();

        return view('landing.tool-detail', compact('tool', 'related'));
    }

    public function guide()
    {
        return view('landing.guide');
    }

    public function about()
    {
        $stats = $this->statistics();
        $contact = [
            'email' => Setting::get('contact_email'),
            'phone' => Setting::get('contact_phone'),
            'address' => Setting::get('contact_address'),
        ];

        return view('landing.about', compact('stats', 'contact'));
    }

    private function statistics(): array
    {
        return [
            'total_tools' => Tool::count(),
            'total_categories' => Category::count(),
            'available_tools' => Tool::where('status', 'tersedia')->where('stock_available', '>', 0)->count(),
            'total_borrowings' => Borrowing::count(),
        ];
    }
}