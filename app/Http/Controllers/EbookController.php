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
    public function chapter(string $slug, string $chapter): View
    {
        $user = Auth::user();
        $item = $this->readableEbook($slug, $user);

        $chapters = $item->chapters()->get();
        $index = $chapters->search(fn ($candidate): bool => $candidate->slug === $chapter);
        abort_if($index === false, 404);

        $chapter = $chapters[$index];
        $previous = $index > 0 ? $chapters[$index - 1] : null;
        $next = $index < $chapters->count() - 1 ? $chapters[$index + 1] : null;
        $item->increment('views_count');

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

        return view('library.ebook.chapter', compact(
            'item', 'chapter', 'previous', 'next', 'canSaveChecklist', 'checklistVersion', 'checked',
        ));
    }

    public function saveChecklist(Request $request, string $slug): RedirectResponse
    {
        $user = $request->user();
        $item = $this->readableEbook($slug, $user);
        abort_unless($this->canSaveChecklist($user), 403);

        $chapterData = $request->validate(['chapter_id' => ['required', 'integer', 'min:1']]);
        $chapter = $item->chapters()->findOrFail($chapterData['chapter_id']);
        abort_unless($chapter->action_checklist, 404);

        $version = $this->checklistVersion($chapter);
        $validated = $request->validate([
            'checklist_version' => ['required', 'string', Rule::in([$version])],
            'checked' => ['sometimes', 'array', 'list', 'max:'.count($chapter->action_checklist)],
            'checked.*' => ['required', 'integer', 'distinct', Rule::in(array_keys($chapter->action_checklist))],
        ], [
            'checklist_version.in' => 'Checklist sudah berubah. Pilih kembali langkah yang sudah dilakukan.',
        ]);

        $key = ['user_id' => $user->id, 'library_item_id' => $item->id];
        DB::transaction(function () use ($key, $chapter, $version, $validated): void {
            EbookProgress::query()->upsert([$key], array_keys($key), ['library_item_id']);
            $progress = EbookProgress::where($key)->lockForUpdate()->firstOrFail();
            $state = $progress->checklist_state ?? [];
            $state[$chapter->id] = [
                'version' => $version,
                'checked' => array_map('intval', $validated['checked'] ?? []),
            ];
            $progress->update(['checklist_state' => $state]);
        }, 3);

        return redirect()->to(route('library.ebook.chapter', [$item->slug, $chapter->slug]).'#amalkan-pekan-ini')
            ->with('ebook_checklist_saved', true);
    }

    private function readableEbook(string $slug, ?User $user): LibraryItem
    {
        $item = LibraryItem::approved()->where('type', 'ebook')->where('slug', $slug)->firstOrFail();
        abort_unless($item->isReadableBy($user), 404);

        return $item;
    }

    private function canSaveChecklist(?User $user): bool
    {
        return $user && $user->isActive()
            && ($user->isMember() || $user->isSubscriber() || $user->isAdmin() || $user->isStaff());
    }

    private function checklistVersion(LibraryChapter $chapter): string
    {
        return hash('sha256', json_encode($chapter->action_checklist ?? [], JSON_THROW_ON_ERROR));
    }
}
