<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryChapter;
use App\Models\LibraryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminEbookController extends Controller
{
    public function createChapter(LibraryItem $item): View
    {
        abort_unless($item->isEbook(), 404);

        $quotes = [];

        return view('admin.library.ebook.chapter-form', compact('item', 'quotes'));
    }

    public function storeChapter(Request $request, LibraryItem $item): RedirectResponse
    {
        abort_unless($item->isEbook(), 404);

        $validated = $this->validatedChapter($request);

        DB::transaction(function () use ($item, $validated): void {
            $item = LibraryItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            $baseSlug = Str::slug($validated['title']) ?: 'bab';
            $slug = $baseSlug;
            $suffix = 2;

            while ($item->chapters()->where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$suffix}";
                $suffix++;
            }

            $item->chapters()->create([
                ...$validated,
                'slug' => $slug,
                'position' => $item->chapters()->max('position') + 1,
            ]);
        });

        return redirect()->route('admin.library.ebook.preview', $item)
            ->with('success', 'Bab tersimpan dalam draf ebook.');
    }

    public function editChapter(LibraryItem $item, LibraryChapter $chapter): View
    {
        abort_unless($item->isEbook(), 404);
        $chapter = $item->chapters()->findOrFail($chapter->id);
        $quotes = array_map(
            fn (array $quote): array => [...$quote, 'approved' => '1'],
            $chapter->approved_quotes ?? []
        );

        return view('admin.library.ebook.chapter-form', compact('item', 'chapter', 'quotes'));
    }

    public function updateChapter(Request $request, LibraryItem $item, LibraryChapter $chapter): RedirectResponse
    {
        abort_unless($item->isEbook(), 404);
        $chapter = $item->chapters()->findOrFail($chapter->id);
        $chapter->update($this->validatedChapter($request));

        return redirect()->route('admin.library.ebook.preview', $item)
            ->with('success', 'Bab berhasil diperbarui.');
    }

    public function destroyChapter(LibraryItem $item, LibraryChapter $chapter): RedirectResponse
    {
        abort_unless($item->isEbook(), 404);

        DB::transaction(function () use ($item, $chapter): void {
            $lockedItem = LibraryItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            $lockedChapter = $lockedItem->chapters()->lockForUpdate()->findOrFail($chapter->id);
            $lockedChapter->delete();

            foreach ($lockedItem->chapters()->orderBy('position')->get() as $index => $remainingChapter) {
                $remainingChapter->update(['position' => $index + 1]);
            }
        });

        return redirect()->route('admin.library.ebook.preview', $item)
            ->with('success', 'Bab berhasil dihapus.');
    }

    public function moveChapterUp(LibraryItem $item, LibraryChapter $chapter): RedirectResponse
    {
        return $this->moveChapter($item, $chapter, -1);
    }

    public function moveChapterDown(LibraryItem $item, LibraryChapter $chapter): RedirectResponse
    {
        return $this->moveChapter($item, $chapter, 1);
    }

    public function preview(LibraryItem $item): View
    {
        abort_unless($item->isEbook(), 404);

        $chapters = $item->chapters()->get();

        return view('admin.library.ebook.cover', compact('item', 'chapters'));
    }

    public function previewChapter(LibraryItem $item, string $chapter): View
    {
        abort_unless($item->isEbook(), 404);

        $chapter = $item->chapters()->where('slug', $chapter)->firstOrFail();

        return view('admin.library.ebook.chapter', compact('item', 'chapter'));
    }

    public function publish(Request $request, LibraryItem $item): RedirectResponse
    {
        abort_unless($item->isEbook(), 404);

        $request->validate([
            'editorial_approved' => ['accepted'],
        ]);

        if (! $item->chapters()->exists()) {
            return back()->withErrors(['chapters' => 'Tambahkan setidaknya satu bab sebelum menerbitkan ebook.']);
        }

        $item->update([
            'status' => 'approved',
            'is_public' => true,
        ]);

        return redirect()->route('admin.library.ebook.preview', $item)
            ->with('success', 'Ebook diterbitkan di Pustaka AI.');
    }

    private function validatedChapter(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'summary_points' => ['required', 'array', 'min:1'],
            'summary_points.*' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'approved_quotes' => ['nullable', 'array', 'max:10'],
            'approved_quotes.*.text' => ['required', 'string', 'max:1000'],
            'approved_quotes.*.attribution' => ['required', 'string', 'max:255'],
            'approved_quotes.*.approved' => ['accepted'],
            'research_box' => ['nullable', 'string'],
            'action_title' => ['nullable', 'string'],
            'action_checklist' => ['nullable', 'array'],
            'action_checklist.*' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['action_checklist'] = array_values(array_filter(
            $validated['action_checklist'] ?? [],
            fn (?string $action): bool => $action !== null && trim($action) !== ''
        ));

        $validated['approved_quotes'] = array_map(
            fn (array $quote): array => [
                'text' => $quote['text'],
                'attribution' => $quote['attribution'],
            ],
            $validated['approved_quotes'] ?? []
        );

        return $validated;
    }

    private function moveChapter(LibraryItem $item, LibraryChapter $chapter, int $direction): RedirectResponse
    {
        abort_unless($item->isEbook(), 404);

        DB::transaction(function () use ($item, $chapter, $direction): void {
            $lockedItem = LibraryItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            $chapters = $lockedItem->chapters()->lockForUpdate()->get();
            $index = $chapters->search(fn (LibraryChapter $candidate): bool => $candidate->id === $chapter->id);
            abort_if($index === false, 404);

            $neighborIndex = $index + $direction;
            if (! $chapters->has($neighborIndex)) {
                return;
            }

            $current = $chapters[$index];
            $neighbor = $chapters[$neighborIndex];
            $currentPosition = $current->position;
            $neighborPosition = $neighbor->position;
            $temporaryPosition = (int) $chapters->max('position') + 1;
            $current->update(['position' => $temporaryPosition]);
            $neighbor->update(['position' => $currentPosition]);
            $current->update(['position' => $neighborPosition]);
        });

        return redirect()->route('admin.library.ebook.preview', $item)
            ->with('success', 'Urutan bab berhasil diperbarui.');
    }
}
