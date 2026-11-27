<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryChapter;
use App\Models\LibraryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $chapter = $item->chapters()->create($this->validatedChapter($request));

        return redirect()->route('admin.library.chapters.index', $item)
            ->with('success', "Bab '{$chapter->title}' ditambahkan sebagai bab ke-{$chapter->position}.");
    }

    public function edit(LibraryItem $item, LibraryChapter $chapter): View
    {
        abort_unless($item->isEbook(), 404);

        return view('admin.library.chapter-edit', compact('item', 'chapter'));
    }

    public function update(Request $request, LibraryItem $item, LibraryChapter $chapter): RedirectResponse
    {
        abort_unless($item->isEbook(), 404);

        // Slug stays fixed so chapter links already shared (e.g. on WhatsApp) keep working.
        $chapter->update($this->validatedChapter($request));

        return redirect()->route('admin.library.chapters.index', $item)
            ->with('success', "Bab '{$chapter->title}' diperbarui.");
    }

    public function move(Request $request, LibraryItem $item, LibraryChapter $chapter): RedirectResponse
    {
        abort_unless($item->isEbook(), 404);

        $validated = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ]);

        $chapter->move($validated['direction']);

        return redirect()->route('admin.library.chapters.index', $item);
    }

    public function destroy(LibraryItem $item, LibraryChapter $chapter): RedirectResponse
    {
        abort_unless($item->isEbook(), 404);

        DB::transaction(fn () => $chapter->delete());

        return redirect()->route('admin.library.chapters.index', $item)
            ->with('success', "Bab '{$chapter->title}' dihapus. Urutan bab lain sudah dirapikan.");
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

    /**
     * @return array{title: string, subtitle: ?string, summary_points: list<string>, body: string, research_box: ?string, action_title: ?string, action_checklist: list<string>}
     */
    private function validatedChapter(Request $request): array
    {
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

        $validated['subtitle'] ??= null;
        $validated['research_box'] ??= null;
        $validated['action_title'] ??= null;
        $validated['summary_points'] = $this->cleanLines($validated['summary_points'] ?? []);
        $validated['action_checklist'] = $this->cleanLines($validated['action_checklist'] ?? []);

        return $validated;
    }

    /**
     * @param  array<mixed>  $lines
     * @return list<string>
     */
    private function cleanLines(array $lines): array
    {
        return array_values(array_filter(array_map('trim', array_filter($lines, 'is_string')), 'strlen'));
    }
}
