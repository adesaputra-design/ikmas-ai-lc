<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminLibraryController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $q = $request->get('q');

        $query = LibraryItem::with('user');

        if ($tab === 'curation') {
            $query->where('type', 'academic')->where('status', 'pending');
        } elseif (in_array($tab, ['book', 'podcast', 'academic'])) {
            $query->where('type', $tab);
        }

        if ($q) {
            $query->where(function ($sq) use ($q) {
                $sq->where('title', 'like', "%{$q}%")
                   ->orWhere('author_name', 'like', "%{$q}%")
                   ->orWhere('podcast_source', 'like', "%{$q}%")
                   ->orWhere('institution', 'like', "%{$q}%")
                   ->orWhere('category', 'like', "%{$q}%");
            });
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        $pendingCount = LibraryItem::where('type', 'academic')->where('status', 'pending')->count();
        $bookCount = LibraryItem::where('type', 'book')->count();
        $podcastCount = LibraryItem::where('type', 'podcast')->count();
        $academicCount = LibraryItem::where('type', 'academic')->where('status', 'approved')->count();

        return view('admin.library.index', compact(
            'items',
            'tab',
            'q',
            'pendingCount',
            'bookCount',
            'podcastCount',
            'academicCount'
        ));
    }

    public function create()
    {
        return view('admin.library.form', [
            'item' => new LibraryItem(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:book,podcast,academic',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:60',
            'summary_preview' => 'required|string',
            'content' => 'nullable|string',
            'cover_image' => 'nullable|image|max:3072',
            'is_featured' => 'nullable|boolean',

            // Book fields
            'author_name' => 'nullable|string|max:255',
            'reading_time' => 'nullable|string|max:50',

            // Podcast fields
            'podcast_source' => 'nullable|string|max:255',
            'media_embed_url' => 'nullable|url|max:500',
            'duration' => 'nullable|string|max:50',

            // Academic fields
            'academic_degree' => 'nullable|in:skripsi,tesis,disertasi,jurnal',
            'institution' => 'nullable|string|max:255',
            'co_authors' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'external_url' => 'nullable|url|max:500',
            'pdf_file' => 'nullable|mimes:pdf|max:15360',
        ]);

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (LibraryItem::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('library_covers', 'public');
        }

        $filePath = null;
        if ($request->hasFile('pdf_file')) {
            $filePath = $request->file('pdf_file')->store('academic_papers', 'public');
        }

        $item = new LibraryItem();
        $item->user_id = auth()->id();
        $item->type = $validated['type'];
        $item->title = $validated['title'];
        $item->slug = $slug;
        $item->category = $validated['category'];
        $item->summary_preview = $validated['summary_preview'];
        $item->content = $validated['content'] ?? null;
        $item->cover_image = $coverPath;
        $item->file_path = $filePath;
        $item->is_featured = $request->boolean('is_featured');
        $item->status = 'approved';

        if ($item->type === 'book') {
            $item->author_name = $validated['author_name'] ?? null;
            $item->reading_time = $validated['reading_time'] ?? null;
        } elseif ($item->type === 'podcast') {
            $item->podcast_source = $validated['podcast_source'] ?? null;
            $item->media_embed_url = $validated['media_embed_url'] ?? null;
            $item->duration = $validated['duration'] ?? null;
        } elseif ($item->type === 'academic') {
            $item->academic_degree = $validated['academic_degree'] ?? 'jurnal';
            $item->institution = $validated['institution'] ?? null;
            $item->co_authors = $validated['co_authors'] ?? null;
            $item->publication_year = $validated['publication_year'] ?? date('Y');
            $item->external_url = $validated['external_url'] ?? null;
        }

        $item->save();

        $tab = $item->type;
        return redirect()->route('admin.library.index', ['tab' => $tab])
            ->with('success', "Konten '{$item->title}' berhasil ditambahkan ke Pustaka AI!");
    }

    public function edit(LibraryItem $item)
    {
        return view('admin.library.form', [
            'item' => $item,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, LibraryItem $item)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:60',
            'summary_preview' => 'required|string',
            'content' => 'nullable|string',
            'cover_image' => 'nullable|image|max:3072',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:pending,approved,rejected',

            // Book fields
            'author_name' => 'nullable|string|max:255',
            'reading_time' => 'nullable|string|max:50',

            // Podcast fields
            'podcast_source' => 'nullable|string|max:255',
            'media_embed_url' => 'nullable|url|max:500',
            'duration' => 'nullable|string|max:50',

            // Academic fields
            'academic_degree' => 'nullable|in:skripsi,tesis,disertasi,jurnal',
            'institution' => 'nullable|string|max:255',
            'co_authors' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'external_url' => 'nullable|url|max:500',
            'pdf_file' => 'nullable|mimes:pdf|max:15360',
            'rejection_note' => 'nullable|string',
        ]);

        $item->title = $validated['title'];
        $item->category = $validated['category'];
        $item->summary_preview = $validated['summary_preview'];
        $item->content = $validated['content'] ?? null;
        $item->is_featured = $request->boolean('is_featured');
        $item->status = $validated['status'];
        $item->rejection_note = $validated['rejection_note'] ?? null;

        if ($request->hasFile('cover_image')) {
            if ($item->cover_image && Storage::disk('public')->exists($item->cover_image)) {
                Storage::disk('public')->delete($item->cover_image);
            }
            $item->cover_image = $request->file('cover_image')->store('library_covers', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                Storage::disk('public')->delete($item->file_path);
            }
            $item->file_path = $request->file('pdf_file')->store('academic_papers', 'public');
        }

        if ($item->type === 'book') {
            $item->author_name = $validated['author_name'] ?? null;
            $item->reading_time = $validated['reading_time'] ?? null;
        } elseif ($item->type === 'podcast') {
            $item->podcast_source = $validated['podcast_source'] ?? null;
            $item->media_embed_url = $validated['media_embed_url'] ?? null;
            $item->duration = $validated['duration'] ?? null;
        } elseif ($item->type === 'academic') {
            $item->academic_degree = $validated['academic_degree'] ?? $item->academic_degree;
            $item->institution = $validated['institution'] ?? null;
            $item->co_authors = $validated['co_authors'] ?? null;
            $item->publication_year = $validated['publication_year'] ?? null;
            $item->external_url = $validated['external_url'] ?? null;
        }

        $item->save();

        return redirect()->route('admin.library.index', ['tab' => $item->type])
            ->with('success', "Item '{$item->title}' berhasil diperbarui!");
    }

    public function approve(LibraryItem $item)
    {
        $item->update([
            'status' => 'approved',
            'rejection_note' => null,
        ]);

        return back()->with('success', "Karya ilmiah '{$item->title}' telah disetujui dan dipublikasikan!");
    }

    public function reject(Request $request, LibraryItem $item)
    {
        $request->validate([
            'rejection_note' => 'required|string|max:1000',
        ]);

        $item->update([
            'status' => 'rejected',
            'rejection_note' => $request->rejection_note,
        ]);

        return back()->with('success', "Karya ilmiah '{$item->title}' telah ditolak dengan catatan evaluasi.");
    }

    public function destroy(LibraryItem $item)
    {
        if ($item->cover_image && Storage::disk('public')->exists($item->cover_image)) {
            Storage::disk('public')->delete($item->cover_image);
        }
        if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
            Storage::disk('public')->delete($item->file_path);
        }

        $title = $item->title;
        $item->delete();

        return back()->with('success', "Item '{$title}' berhasil dihapus dari pustaka.");
    }
}
