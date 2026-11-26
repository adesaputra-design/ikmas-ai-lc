<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = LibraryItem::visibleToPublic()->with('user')->latest();

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('summary_preview', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%")
                    ->orWhere('podcast_source', 'like', "%{$search}%")
                    ->orWhere('institution', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => LibraryItem::visibleToPublic()->count(),
            'books' => LibraryItem::approved()->where('type', 'book')->count(),
            'podcasts' => LibraryItem::approved()->where('type', 'podcast')->count(),
            'academics' => LibraryItem::approved()->where('type', 'academic')->count(),
            'ebooks' => LibraryItem::visibleToPublic()->where('type', 'ebook')->count(),
        ];

        $categories = LibraryItem::visibleToPublic()
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return view('library.index', compact('items', 'stats', 'categories'));
    }

    public function show(string $slug)
    {
        $item = LibraryItem::visibleToPublic()->where('slug', $slug)->firstOrFail();

        // Increment view count
        $item->increment('views_count');

        if ($item->isEbook()) {
            return view('library.ebook.cover', [
                'item' => $item,
                'chapters' => $item->chapters()->get(),
                'isPreview' => false,
                'isBookmarked' => Auth::check() && $item->bookmarks()->where('user_id', Auth::id())->exists(),
            ]);
        }

        $user = Auth::user();
        $isUnlocked = false;

        if ($user) {
            if ($user->isAdmin() || $user->isStaff() || $user->isMember() || ($user->isSubscriber() && $user->isActive())) {
                $isUnlocked = true;
            }
        }

        $isBookmarked = false;
        if ($user) {
            $isBookmarked = $item->bookmarks()->where('user_id', $user->id)->exists();
        }

        $relatedItems = LibraryItem::visibleToPublic()
            ->where('id', '!=', $item->id)
            ->where(function ($q) use ($item) {
                $q->where('type', $item->type)
                    ->orWhere('category', $item->category);
            })
            ->latest()
            ->take(3)
            ->get();

        return view('library.show', compact('item', 'isUnlocked', 'isBookmarked', 'relatedItems'));
    }

    public function chapter(string $slug, string $chapterSlug): View
    {
        $item = LibraryItem::visibleToPublic()->where('type', 'ebook')->where('slug', $slug)->firstOrFail();

        $chapters = $item->chapters()->get();
        $index = $chapters->search(fn ($chapter) => $chapter->slug === $chapterSlug);
        abort_if($index === false, 404);

        return view('library.ebook.chapter', [
            'item' => $item,
            'chapter' => $chapters[$index],
            'previous' => $chapters->get($index - 1),
            'next' => $chapters->get($index + 1),
            'isPreview' => false,
        ]);
    }
}
