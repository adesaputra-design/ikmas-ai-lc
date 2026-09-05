<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = LibraryItem::approved()->with('user')->latest();

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
            'total' => LibraryItem::approved()->count(),
            'books' => LibraryItem::approved()->where('type', 'book')->count(),
            'podcasts' => LibraryItem::approved()->where('type', 'podcast')->count(),
            'academics' => LibraryItem::approved()->where('type', 'academic')->count(),
        ];

        $categories = LibraryItem::approved()
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return view('library.index', compact('items', 'stats', 'categories'));
    }

    public function show(string $slug)
    {
        $item = LibraryItem::approved()->where('slug', $slug)->firstOrFail();

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
            $isBookmarked = $user->bookmarks()->where('library_item_id', $item->id)->exists();
        }

        $relatedItems = LibraryItem::approved()
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
