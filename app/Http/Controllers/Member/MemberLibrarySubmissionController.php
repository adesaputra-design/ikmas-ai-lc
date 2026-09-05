<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\LibraryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MemberLibrarySubmissionController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        // Hanya alumni member (atau admin/staf) yang boleh submit karya ilmiah
        if ($user->isSubscriber()) {
            abort(403, 'Pengajuan Karya Ilmiah saat ini dikhususkan bagi anggota alumni IKMAS.');
        }

        $categories = [
            'Fundamental AI & Machine Learning',
            'LLM & Prompt Engineering',
            'Computer Vision & Citra Digital',
            'Natural Language Processing (NLP)',
            'Etika, Tata Kelola & Masa Depan AI',
            'Bisnis, Manajemen & Startup AI',
            'Pendidikan, Dakwah & Humaniora AI',
        ];

        return view('member.library.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->isSubscriber()) {
            abort(403, 'Pengajuan Karya Ilmiah saat ini dikhususkan bagi anggota alumni IKMAS.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'academic_degree' => ['required', 'in:skripsi,tesis,disertasi,jurnal'],
            'institution' => ['required', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1980', 'max:' . (date('Y') + 1)],
            'category' => ['required', 'string', 'max:60'],
            'co_authors' => ['nullable', 'string', 'max:255'],
            'summary_preview' => ['required', 'string', 'min:20'],
            'content' => ['nullable', 'string'],
            'external_url' => ['nullable', 'url', 'max:500'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // Max 10MB
            'cover_image' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ]);

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (LibraryItem::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $filePath = null;
        if ($request->hasFile('pdf_file')) {
            $filePath = $request->file('pdf_file')->store('academic_papers', 'public');
        }

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('library_covers', 'public');
        }

        LibraryItem::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'type' => 'academic',
            'academic_degree' => $validated['academic_degree'],
            'institution' => $validated['institution'],
            'publication_year' => $validated['publication_year'] ?? date('Y'),
            'category' => $validated['category'],
            'co_authors' => $validated['co_authors'] ?? null,
            'summary_preview' => $validated['summary_preview'],
            'content' => $validated['content'] ?? null,
            'external_url' => $validated['external_url'] ?? null,
            'file_path' => $filePath,
            'cover_image' => $coverPath,
            'status' => 'pending',
            'is_featured' => false,
        ]);

        return redirect()->route('member.dashboard')->with('success', 'Karya ilmiah Anda berhasil diajukan! Pengurus akan meninjau naskah Anda sebelum dipublikasikan di Pustaka AI.');
    }
}
