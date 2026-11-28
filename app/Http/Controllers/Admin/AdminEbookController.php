<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryChapter;
use App\Models\LibraryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

    public function editAssessment(LibraryItem $item): View
    {
        abort_unless($item->isEbook(), 404);

        return view('admin.library.ebook.assessment-form', compact('item'));
    }

    public function updateAssessment(Request $request, LibraryItem $item): RedirectResponse
    {
        abort_unless($item->isEbook(), 404);

        $request->validate(['assessment' => ['required', 'string', 'max:50000']]);
        $definition = json_decode($request->string('assessment')->toString(), true);

        if (! is_array($definition) || json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['assessment' => 'Format asesmen harus berupa JSON yang valid.'])->withInput();
        }

        $validator = Validator::make($definition, [
            'scale.min' => ['required', 'integer', 'min:0', 'max:10'],
            'scale.max' => ['required', 'integer', 'min:1', 'max:10', 'gt:scale.min'],
            'scale.labels' => ['required', 'array'],
            'scale.labels.*' => ['required', 'string', 'max:100'],
            'principles' => ['required', 'array', 'min:1'],
            'principles.*.id' => ['required', 'string', 'max:80', 'regex:/\A[a-zA-Z0-9_-]+\z/', 'distinct'],
            'principles.*.name' => ['required', 'string', 'max:120'],
            'principles.*.questions' => ['required', 'array', 'min:1'],
            'principles.*.questions.*.id' => ['required', 'string', 'max:80', 'regex:/\A[a-zA-Z0-9_-]+\z/', 'distinct'],
            'principles.*.questions.*.text' => ['required', 'string', 'max:500'],
            'ranges' => ['required', 'array', 'min:1'],
            'ranges.*.min' => ['required', 'integer', 'min:0'],
            'ranges.*.max' => ['required', 'integer', 'min:0', 'gte:ranges.*.min'],
            'ranges.*.label' => ['required', 'string', 'max:120'],
            'ranges.*.description' => ['required', 'string', 'max:1000'],
            'ranges.*.recommendation_chapters' => ['required', 'array', 'min:1'],
            'ranges.*.recommendation_chapters.*' => [
                'required',
                'string',
                'distinct',
                Rule::exists('library_chapters', 'slug')->where('library_item_id', $item->id),
            ],
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['assessment' => 'Struktur asesmen belum lengkap atau berisi referensi bab yang tidak valid.'])->withInput();
        }

        $questionIds = collect($definition['principles'])
            ->flatMap(fn (array $principle): array => array_column($principle['questions'], 'id'));
        if ($questionIds->count() !== $questionIds->unique()->count()) {
            return back()->withErrors(['assessment' => 'Setiap pertanyaan harus memiliki ID unik.'])->withInput();
        }

        $questionCount = collect($definition['principles'])->sum(fn (array $principle): int => count($principle['questions']));
        $scaleMin = (int) $definition['scale']['min'];
        $scaleMax = (int) $definition['scale']['max'];
        $expectedMin = $questionCount * $scaleMin;
        $expectedMax = $questionCount * $scaleMax;
        $ranges = collect($definition['ranges'])->sortBy('min')->values();
        $nextExpectedMin = $expectedMin;

        foreach ($ranges as $range) {
            if ((int) $range['min'] !== $nextExpectedMin || (int) $range['max'] < (int) $range['min']) {
                return back()->withErrors(['assessment' => 'Rentang hasil harus berurutan tanpa celah atau tumpang tindih.'])->withInput();
            }

            $nextExpectedMin = (int) $range['max'] + 1;
        }

        if ($nextExpectedMin !== $expectedMax + 1) {
            return back()->withErrors(['assessment' => 'Rentang hasil harus mencakup semua kemungkinan skor.'])->withInput();
        }

        for ($value = $scaleMin; $value <= $scaleMax; $value++) {
            if (! array_key_exists((string) $value, $definition['scale']['labels'])) {
                return back()->withErrors(['assessment' => 'Setiap nilai dalam skala harus memiliki label.'])->withInput();
            }
        }

        $item->update(['assessment' => $definition]);

        return redirect()->route('admin.library.ebook.preview', $item)
            ->with('success', 'Asesmen ebook berhasil diperbarui.');
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
