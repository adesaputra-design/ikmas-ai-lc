<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = LibraryItem::visibleInCatalog()->with('user')->latest();

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('series') && $request->input('type') === 'ebook') {
            $query->where('series_name', (string) $request->string('series')->trim());
        }

        if ($request->filled('edition') && $request->input('type') === 'ebook') {
            $query->where('edition_number', $request->integer('edition'));
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
            'total' => LibraryItem::visibleInCatalog()->count(),
            'books' => LibraryItem::approved()->where('type', 'book')->count(),
            'podcasts' => LibraryItem::approved()->where('type', 'podcast')->count(),
            'academics' => LibraryItem::approved()->where('type', 'academic')->count(),
            'ebooks' => LibraryItem::visibleInCatalog()->where('type', 'ebook')->count(),
        ];

        $categories = LibraryItem::visibleInCatalog()
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $ebookSeries = LibraryItem::visibleInCatalog()->where('type', 'ebook')
            ->whereNotNull('series_name')->distinct()->orderBy('series_name')->pluck('series_name');
        $ebookEditions = LibraryItem::visibleInCatalog()->where('type', 'ebook')
            ->whereNotNull('edition_number')->distinct()->orderBy('edition_number')->pluck('edition_number');

        return view('library.index', compact('items', 'stats', 'categories', 'ebookSeries', 'ebookEditions'));
    }

    public function show(string $slug)
    {
        $item = LibraryItem::approved()->where('slug', $slug)->firstOrFail();
        if ($item->isEbook()) {
            abort_unless($item->isReadableBy(Auth::user()), 404);

            $item->increment('views_count');
            $chapters = $item->chapters()->get();
            $isBookmarked = Auth::check() && $item->bookmarks()->where('user_id', Auth::id())->exists();

            return view('library.ebook.cover', compact('item', 'chapters', 'isBookmarked'));
        }

        // Increment view count
        $item->increment('views_count');

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

        $relatedItems = LibraryItem::visibleInCatalog()
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
}
