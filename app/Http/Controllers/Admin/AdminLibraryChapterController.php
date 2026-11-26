<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminLibraryChapterController extends Controller
{
    public function index(LibraryItem $item)
    {
        abort_unless($item->isEbook(), 404);

        return view('admin.library.chapters', [
            'item' => $item,
            'chapters' => $item->chapters()->get(),
        ]);
    }

    public function store(Request $request, LibraryItem $item)
    {
        abort_unless($item->isEbook(), 404);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'summary_points' => 'nullable|array|max:10',
            'summary_points.*' => 'nullable|string|max:500',
            'body' => 'required|string',
            'research_box' => 'nullable|string',
            'action_title' => 'nullable|string|max:1000',
            'action_checklist' => 'nullable|array|max:20',
            'action_checklist.*' => 'nullable|string|max:500',
        ]);

        $validated['summary_points'] = $this->cleanLines($validated['summary_points'] ?? []);
        $validated['action_checklist'] = $this->cleanLines($validated['action_checklist'] ?? []);

        $chapter = $item->chapters()->create($validated);

        return redirect()->route('admin.library.chapters.index', $item)
            ->with('success', "Bab '{$chapter->title}' ditambahkan sebagai bab ke-{$chapter->position}.");
    }

    public function preview(LibraryItem $item)
    {
        abort_unless($item->isEbook(), 404);

        return view('library.ebook.cover', [
            'item' => $item,
            'chapters' => $item->chapters()->get(),
            'isPreview' => true,
        ]);
    }

    public function previewChapter(LibraryItem $item, string $chapterSlug)
    {
        abort_unless($item->isEbook(), 404);

        $chapters = $item->chapters()->get();
        $index = $chapters->search(fn ($chapter) => $chapter->slug === $chapterSlug);
        abort_if($index === false, 404);

        return view('library.ebook.chapter', [
            'item' => $item,
            'chapter' => $chapters[$index],
            'previous' => $chapters->get($index - 1),
            'next' => $chapters->get($index + 1),
            'isPreview' => true,
        ]);
    }

    public function publish(Request $request, LibraryItem $item): RedirectResponse
    {
        abort_unless($item->isEbook(), 404);

        $request->validate([
            'editorial_approved' => ['accepted'],
        ], [
            'editorial_approved.accepted' => 'Centang konfirmasi bahwa naskah dan kutipan sudah disetujui narasumber.',
        ]);

        if (! $item->chapters()->exists()) {
            return back()->withErrors(['chapters' => 'Tambahkan setidaknya satu bab sebelum menerbitkan ebook.']);
        }

        $item->update([
            'status' => 'approved',
            'is_public' => true,
            'rejection_note' => null,
        ]);

        return redirect()->route('admin.library.chapters.index', $item)
            ->with('success', "Ebook '{$item->title}' sudah terbit dan dapat dibaca publik.");
    }

    private function cleanLines(array $lines): array
    {
        return array_values(array_filter(array_map('trim', array_filter($lines, 'is_string')), 'strlen'));
    }
}
