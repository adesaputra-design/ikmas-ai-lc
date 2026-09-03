<?php

namespace App\Http\Controllers;

use App\Models\Showcase;
use Illuminate\Http\Request;

class ShowcaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Showcase::with('user')
            ->whereHas('user')
            ->where('status', 'approved');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tools_used', 'like', "%{$search}%");
            });
        }

        $showcases = $query->latest()->paginate(9)->withQueryString();

        return view('showcase.index', compact('showcases'));
    }

    public function show(string $slug)
    {
        $showcase = Showcase::with('user')
            ->whereHas('user')
            ->where('slug', $slug)
            ->where('status', 'approved')
            ->firstOrFail();

        return view('showcase.show', compact('showcase'));
    }
}
