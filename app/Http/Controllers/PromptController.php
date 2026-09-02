<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Prompt;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    public function index(Request $request)
    {
        $query = Prompt::with('category');

        if ($request->filled('role')) {
            $role = $request->role;
            $query->where('target_role', 'like', "%{$role}%");
        }

        if ($request->filled('tool')) {
            $tool = $request->tool;
            $query->where('target_tool', 'like', "%{$tool}%");
        }

        if ($request->filled('category')) {
            $catSlug = $request->category;
            $query->whereHas('category', function ($q) use ($catSlug) {
                $q->where('slug', $catSlug);
            });
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('prompt_text', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        $prompts = $query->latest()->paginate(12)->withQueryString();

        $roles = [
            'Umum & Produktivitas',
            'Pebisnis / Marketer',
            'Penulis / Content Creator',
            'Pendidik / Guru',
            'Developer / IT',
        ];

        $tools = [
            'ChatGPT',
            'Claude',
            'Gemini',
            'Canva / Midjourney',
            'Cursor / v0',
        ];

        return view('prompts.index', compact('prompts', 'roles', 'tools'));
    }

    public function trackCopy(Prompt $prompt)
    {
        $prompt->increment('copy_count');
        return response()->json(['success' => true, 'copy_count' => $prompt->copy_count]);
    }
}
