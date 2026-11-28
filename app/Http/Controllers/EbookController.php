<?php

namespace App\Http\Controllers;

use App\Models\EbookProgress;
use App\Models\LibraryChapter;
use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EbookController extends Controller
{
    public function assessment(string $slug): View
    {
        $item = LibraryItem::approved()->where('type', 'ebook')->where('slug', $slug)->firstOrFail();
        abort_unless($item->isReadableBy(Auth::user()), 404);
        abort_unless(is_array($item->assessment) && isset($item->assessment['principles'], $item->assessment['scale'], $item->assessment['ranges']), 404);

        $user = Auth::user();
        $canSave = $this->canSaveAssessment($user);
        $chaptersBySlug = $item->chapters()->get(['slug', 'title'])->keyBy('slug');
        $assessmentAttempts = [];
        if ($canSave) {
            $storedAttempts = DB::table('ebook_progress')
                ->where('user_id', $user->id)
                ->where('library_item_id', $item->id)
                ->value('assessment_results');
            $assessmentAttempts = $storedAttempts ? json_decode($storedAttempts, true) : [];
        }

        return view('library.ebook.assessment', compact('item', 'canSave', 'assessmentAttempts', 'chaptersBySlug'));
    }

    public function storeAssessment(Request $request, string $slug): RedirectResponse
    {
        $item = LibraryItem::approved()->where('type', 'ebook')->where('slug', $slug)->firstOrFail();
        abort_unless($item->isReadableBy(Auth::user()), 404);
        abort_unless($this->canSaveAssessment(Auth::user()), 403);
        abort_unless(is_array($item->assessment), 404);

        $validated = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*' => ['required', 'integer'],
        ]);
        $calculated = $item->calculateAssessment($validated['answers']);
        $attempt = [
            'date' => now()->toISOString(),
            'answers' => $calculated['answers'],
            'total' => $calculated['total'],
            'scores' => $calculated['principleScores'],
            'range' => $calculated['range'],
        ];

        DB::transaction(function () use ($item, $request, $attempt): void {
            $userId = (int) $request->user()->id;
            $now = now();
            DB::table('ebook_progress')->upsert([[
                'user_id' => $userId,
                'library_item_id' => $item->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]], ['user_id', 'library_item_id'], ['updated_at']);
            $progressQuery = DB::table('ebook_progress')
                ->where('user_id', $userId)
                ->where('library_item_id', $item->id);
            $progress = $progressQuery->lockForUpdate()->firstOrFail();
            $attempts = $progress->assessment_results ? json_decode($progress->assessment_results, true) : [];
            $attempts[] = $attempt;
            $progressQuery->update([
                'assessment_results' => json_encode($attempts, JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);
        }, 3);

        return redirect()->route('library.ebook.assessment', $item->slug)
            ->with('success', 'Hasil asesmen berhasil disimpan.');
    }

    public function printDocument(string $slug): View
    {
        $item = LibraryItem::approved()->where('type', 'ebook')->where('slug', $slug)->firstOrFail();
        abort_unless($item->isReadableBy(Auth::user()), 404);

        $chapters = $item->chapters()->get();

        return view('library.ebook.print', compact('item', 'chapters'));
    }

    public function chapter(string $slug, string $chapter): View
    {
        $item = LibraryItem::approved()->where('type', 'ebook')->where('slug', $slug)->firstOrFail();
        abort_unless($item->isReadableBy(Auth::user()), 404);

        $chapters = $item->chapters()->get();
        $index = $chapters->search(fn ($candidate): bool => $candidate->slug === $chapter);
        abort_if($index === false, 404);

        $chapter = $chapters[$index];
        $previous = $index > 0 ? $chapters[$index - 1] : null;
        $next = $index < $chapters->count() - 1 ? $chapters[$index + 1] : null;
        $user = Auth::user();
        $canSaveChecklist = $this->canSaveChecklist($user);
        $checked = [];
        $checklistVersion = $this->checklistVersion($chapter);
        if ($canSaveChecklist && $chapter->action_checklist) {
            $state = EbookProgress::query()
                ->where('user_id', $user->id)
                ->where('library_item_id', $item->id)
                ->first()?->checklist_state[$chapter->id] ?? [];
            if (($state['version'] ?? null) === $checklistVersion) {
                $checked = $state['checked'];
            }
        }
        if ($user?->canSaveEbookProgress()) {
            $this->saveLastReadChapter($user, $item, $chapter);
        }
        $item->increment('views_count');

        return view('library.ebook.chapter', compact('item', 'chapter', 'previous', 'next', 'canSaveChecklist', 'checked', 'checklistVersion'));
    }

    public function saveChecklist(Request $request, string $slug): RedirectResponse
    {
        $user = $request->user();
        $item = LibraryItem::approved()->where('type', 'ebook')->where('slug', $slug)->firstOrFail();
        abort_unless($item->isReadableBy($user), 404);
        abort_unless($this->canSaveChecklist($user), 403);

        $chapterData = $request->validate(['chapter_id' => ['required', 'integer', 'min:1']]);
        $chapter = $item->chapters()->findOrFail($chapterData['chapter_id']);
        abort_unless($chapter->action_checklist, 404);
        $version = $this->checklistVersion($chapter);
        $validated = $request->validate([
            'checklist_version' => ['required', 'string', Rule::in([$version])],
            'checked' => ['sometimes', 'array', 'list', 'max:'.count($chapter->action_checklist)],
            'checked.*' => ['required', 'integer', 'distinct', Rule::in(array_keys($chapter->action_checklist))],
        ], ['checklist_version.in' => 'Checklist sudah berubah. Pilih kembali langkah yang sudah dilakukan.']);

        $key = ['user_id' => $user->id, 'library_item_id' => $item->id];
        DB::transaction(function () use ($key, $chapter, $version, $validated): void {
            $now = now();
            DB::table('ebook_progress')->upsert([
                [...$key, 'created_at' => $now, 'updated_at' => $now],
            ], array_keys($key), ['updated_at']);
            $progress = EbookProgress::query()->where($key)->lockForUpdate()->firstOrFail();
            $state = $progress->checklist_state ?? [];
            $state[$chapter->id] = ['version' => $version, 'checked' => array_map('intval', $validated['checked'] ?? [])];
            $progress->update(['checklist_state' => $state]);
        }, 3);

        return redirect()->to(route('library.ebook.chapter', [$item->slug, $chapter->slug]).'#amalkan-pekan-ini')
            ->with('ebook_checklist_saved', true);
    }

    private function canSaveAssessment(?User $user): bool
    {
        return $user?->canSaveEbookProgress() ?? false;
    }

    private function canSaveChecklist(?User $user): bool
    {
        return $user?->canSaveEbookProgress() ?? false;
    }

    private function checklistVersion(LibraryChapter $chapter): string
    {
        return hash('sha256', json_encode($chapter->action_checklist ?? [], JSON_THROW_ON_ERROR));
    }

    private function saveLastReadChapter(User $user, LibraryItem $item, LibraryChapter $chapter): void
    {
        DB::transaction(function () use ($user, $item, $chapter): void {
            $now = now();
            DB::table('ebook_progress')->upsert([[
                'user_id' => $user->id,
                'library_item_id' => $item->id,
                'last_chapter_id' => $chapter->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]], ['user_id', 'library_item_id'], ['last_chapter_id', 'updated_at']);
        });
    }
}
