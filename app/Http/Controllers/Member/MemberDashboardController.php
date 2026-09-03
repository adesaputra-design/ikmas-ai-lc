<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Prompt;
use App\Models\Showcase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $myShowcases = Showcase::where('user_id', $user->id)
            ->latest()
            ->get();

        $bookmarkedPromptIds = Bookmark::where('user_id', $user->id)
            ->where('bookmarkable_type', 'prompt')
            ->pluck('bookmarkable_id');

        $bookmarkedPrompts = Prompt::whereIn('id', $bookmarkedPromptIds)->get();

        return view('member.dashboard', compact('user', 'myShowcases', 'bookmarkedPrompts'));
    }

    public function createShowcase()
    {
        return view('member.showcase-create');
    }

    public function storeShowcase(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'tools_used' => ['required', 'string', 'max:255'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'impact_story' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB max
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('showcase', 'public');
            $imageUrl = '/storage/' . $path;
        }

        $slugBase = Str::slug($validated['title']);
        $slug = $slugBase;
        $counter = 1;
        while (Showcase::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter++;
        }

        Showcase::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'tools_used' => $validated['tools_used'],
            'project_url' => $validated['project_url'] ?? null,
            'impact_story' => $validated['impact_story'] ?? null,
            'image_url' => $imageUrl,
            'status' => 'pending',
        ]);

        return redirect()->route('member.dashboard')->with('success', 'Karya kamu berhasil diajukan! Pengurus akan meninjau dan mengurasi karyamu sebelum ditampilkan di galeri publik.');
    }

    public function toggleBookmark(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'type' => ['required', 'string', 'in:prompt,material'],
        ]);

        $user = Auth::user();
        $existing = Bookmark::where('user_id', $user->id)
            ->where('bookmarkable_id', $validated['id'])
            ->where('bookmarkable_type', $validated['type'])
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['bookmarked' => false]);
        }

        Bookmark::create([
            'user_id' => $user->id,
            'bookmarkable_id' => $validated['id'],
            'bookmarkable_type' => $validated['type'],
        ]);

        return response()->json(['bookmarked' => true]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (! \Illuminate\Support\Facades\Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Kata sandi saat ini yang Anda masukkan tidak sesuai.',
            ]);
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Kata sandi akun Anda berhasil diperbarui!');
    }
}
