<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPromptController extends Controller
{
    public function index(Request $request)
    {
        $query = Prompt::latest();

        if ($request->filled('target_role')) {
            $query->where('target_role', $request->target_role);
        }

        if ($request->filled('target_tool')) {
            $query->where('target_tool', $request->target_tool);
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', (bool) $request->is_featured);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('prompt_text', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        $prompts = $query->paginate(15)->withQueryString();

        return view('admin.prompts.index', compact('prompts'));
    }

    public function create()
    {
        return view('admin.prompts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'target_role' => ['required', 'string', 'max:100'],
            'target_tool' => ['required', 'string', 'max:100'],
            'prompt_text' => ['required', 'string'],
            'instruction' => ['nullable', 'string'],
            'tags' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable'],
        ]);

        $slugBase = Str::slug($validated['title']);
        $slug = $slugBase;
        $counter = 1;
        while (Prompt::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter++;
        }

        $isFeatured = isset($validated['is_featured']) ? (bool) $validated['is_featured'] : false;

        Prompt::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'target_role' => $validated['target_role'],
            'target_tool' => $validated['target_tool'],
            'prompt_text' => $validated['prompt_text'],
            'instruction' => $validated['instruction'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'is_featured' => $isFeatured,
            'copy_count' => 0,
        ]);

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt baru berhasil ditambahkan ke perpustakaan!');
    }

    public function edit(Prompt $prompt)
    {
        return view('admin.prompts.edit', compact('prompt'));
    }

    public function update(Request $request, Prompt $prompt)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'target_role' => ['required', 'string', 'max:100'],
            'target_tool' => ['required', 'string', 'max:100'],
            'prompt_text' => ['required', 'string'],
            'instruction' => ['nullable', 'string'],
            'tags' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable'],
        ]);

        $isFeatured = isset($validated['is_featured']) ? (bool) $validated['is_featured'] : false;

        $prompt->update([
            'title' => $validated['title'],
            'target_role' => $validated['target_role'],
            'target_tool' => $validated['target_tool'],
            'prompt_text' => $validated['prompt_text'],
            'instruction' => $validated['instruction'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'is_featured' => $isFeatured,
        ]);

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt berhasil diperbarui!');
    }

    public function destroy(Prompt $prompt)
    {
        $title = $prompt->title;
        $prompt->delete();

        return redirect()->route('admin.prompts.index')->with('success', "Prompt \"{$title}\" berhasil dihapus!");
    }
}
