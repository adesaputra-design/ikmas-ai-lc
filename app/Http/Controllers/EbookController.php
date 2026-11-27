<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class EbookController extends Controller
{
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
        $item->increment('views_count');

        return view('library.ebook.chapter', compact('item', 'chapter', 'previous', 'next'));
    }
}
