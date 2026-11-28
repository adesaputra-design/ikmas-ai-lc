<?php

namespace App\Http\Controllers;

use App\Models\LibraryChapter;
use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $progressQuery = DB::table('ebook_progress')
                ->where('user_id', $userId)
                ->where('library_item_id', $item->id);
            $progress = $progressQuery->lockForUpdate()->first();

            if (! $progress) {
                DB::table('ebook_progress')->insert([
                    'user_id' => $userId,
                    'library_item_id' => $item->id,
                    'checklist_state' => null,
                    'assessment_results' => json_encode([$attempt], JSON_THROW_ON_ERROR),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return;
            }

            $attempts = $progress->assessment_results ? json_decode($progress->assessment_results, true) : [];
            $attempts[] = $attempt;
            $progressQuery->update([
                'assessment_results' => json_encode($attempts, JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);
        });

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
        if ($user?->canSaveEbookProgress()) {
            $this->saveLastReadChapter($user, $item, $chapter);
        }
        $item->increment('views_count');

        return view('library.ebook.chapter', compact('item', 'chapter', 'previous', 'next'));
    }

    private function canSaveAssessment(?User $user): bool
    {
        return $user?->canSaveEbookProgress() ?? false;
    }

    private function saveLastReadChapter(User $user, LibraryItem $item, LibraryChapter $chapter): void
    {
        DB::transaction(function () use ($user, $item, $chapter): void {
            $progressQuery = DB::table('ebook_progress')
                ->where('user_id', $user->id)
                ->where('library_item_id', $item->id);
            $progress = $progressQuery->lockForUpdate()->first();

            if ($progress) {
                $progressQuery->update([
                    'last_chapter_id' => $chapter->id,
                    'updated_at' => now(),
                ]);

                return;
            }

            DB::table('ebook_progress')->insert([
                'user_id' => $user->id,
                'library_item_id' => $item->id,
                'last_chapter_id' => $chapter->id,
                'checklist_state' => null,
                'assessment_results' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
