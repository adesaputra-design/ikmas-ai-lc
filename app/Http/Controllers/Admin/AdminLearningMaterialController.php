<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LearningMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminLearningMaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = LearningMaterial::with('category')->latest();

        if ($request->filled('pillar')) {
            $query->where('pillar', $request->pillar);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $materials = $query->paginate(15)->withQueryString();

        return view('admin.materials.index', compact('materials'));
    }

    public function create()
    {
        $categories = Category::where('type', 'learning')->get();
        return view('admin.materials.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'pillar' => ['required', 'in:basics,tools,productivity,workflow,opportunity'],
            'level' => ['required', 'in:beginner,explorer,practitioner'],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'reading_minutes' => ['nullable', 'integer', 'min:1'],
            'reading_time' => ['nullable', 'integer', 'min:1'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'slide_url' => ['nullable', 'url', 'max:255'],
            'slides_url' => ['nullable', 'url', 'max:255'],
            'is_published' => ['nullable'],
        ]);

        $slugBase = Str::slug($validated['title']);
        $slug = $slugBase;
        $count = 1;
        while (LearningMaterial::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $count++;
        }

        $readingMinutes = $validated['reading_minutes'] ?? $validated['reading_time'] ?? 5;
        $slideUrl = $validated['slide_url'] ?? $validated['slides_url'] ?? null;
        $isPublished = isset($validated['is_published']) ? (bool) $validated['is_published'] : true;

        LearningMaterial::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'category_id' => $validated['category_id'] ?? null,
            'pillar' => $validated['pillar'],
            'level' => $validated['level'],
            'summary' => $validated['summary'] ?? Str::limit(strip_tags($validated['content']), 150),
            'content' => $validated['content'],
            'reading_minutes' => $readingMinutes,
            'video_url' => $validated['video_url'] ?? null,
            'slide_url' => $slideUrl,
            'is_published' => $isPublished,
        ]);

        return redirect()->route('admin.materi.index')->with('success', 'Materi belajar berhasil ditambahkan!');
    }

    public function edit(LearningMaterial $materi)
    {
        $categories = Category::where('type', 'learning')->get();
        return view('admin.materials.edit', compact('materi', 'categories'));
    }

    public function update(Request $request, LearningMaterial $materi)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'pillar' => ['required', 'in:basics,tools,productivity,workflow,opportunity'],
            'level' => ['required', 'in:beginner,explorer,practitioner'],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'reading_minutes' => ['nullable', 'integer', 'min:1'],
            'reading_time' => ['nullable', 'integer', 'min:1'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'slide_url' => ['nullable', 'url', 'max:255'],
            'slides_url' => ['nullable', 'url', 'max:255'],
            'is_published' => ['nullable'],
        ]);

        $readingMinutes = $validated['reading_minutes'] ?? $validated['reading_time'] ?? $materi->reading_minutes;
        $slideUrl = $validated['slide_url'] ?? $validated['slides_url'] ?? $materi->slide_url;
        $isPublished = isset($validated['is_published']) ? (bool) $validated['is_published'] : false;

        $materi->update([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'] ?? $materi->category_id,
            'pillar' => $validated['pillar'],
            'level' => $validated['level'],
            'summary' => $validated['summary'] ?? Str::limit(strip_tags($validated['content']), 150),
            'content' => $validated['content'],
            'reading_minutes' => $readingMinutes,
            'video_url' => $validated['video_url'] ?? null,
            'slide_url' => $slideUrl,
            'is_published' => $isPublished,
        ]);

        return redirect()->route('admin.materi.index')->with('success', 'Materi belajar berhasil diperbarui!');
    }

    public function destroy(LearningMaterial $materi)
    {
        $title = $materi->title;
        $materi->delete();

        return redirect()->route('admin.materi.index')->with('success', "Materi \"{$title}\" berhasil dihapus!");
    }
}
