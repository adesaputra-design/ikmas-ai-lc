<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\LearningMaterial;
use App\Models\Prompt;
use App\Models\Showcase;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $latestMaterials = LearningMaterial::with('category')
            ->where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        $featuredPrompts = Prompt::with('category')
            ->where('is_featured', true)
            ->latest()
            ->take(3)
            ->get();

        $nextEvent = Event::where('status', 'upcoming')
            ->orderBy('event_date', 'asc')
            ->first();

        // 5 Aktivitas Terbaru Gabungan (Showcase, Member Join, Event)
        $recentShowcases = Showcase::where('status', 'approved')
            ->whereHas('user')
            ->with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'showcase',
                    'badge_label' => 'Karya Baru',
                    'badge_class' => 'badge-emerald',
                    'title' => $item->title,
                    'subtitle' => 'oleh ' . ($item->user->name ?? 'Alumni') . ($item->user && $item->user->alumni_year ? ' (Angkatan ' . $item->user->alumni_year . ')' : ''),
                    'url' => url('/showcase/' . $item->slug),
                    'created_at' => $item->created_at,
                ];
            });

        $recentMembers = User::latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'member',
                    'badge_label' => 'Member Baru',
                    'badge_class' => 'badge-cyan',
                    'title' => $item->name . ' bergabung',
                    'subtitle' => $item->alumni_year ? 'Alumni Angkatan ' . $item->alumni_year : 'Alumni Assalaam',
                    'url' => url('/showcase'),
                    'created_at' => $item->created_at,
                ];
            });

        $recentEvents = Event::latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'event',
                    'badge_label' => 'Event Baru',
                    'badge_class' => 'badge-amber',
                    'title' => $item->title,
                    'subtitle' => 'Jadwal: ' . ($item->event_date ? \Carbon\Carbon::parse($item->event_date)->translatedFormat('d M Y') : 'Segera'),
                    'url' => url('/agenda/' . $item->slug),
                    'created_at' => $item->created_at,
                ];
            });

        $recentActivity = collect()
            ->concat($recentShowcases)
            ->concat($recentMembers)
            ->concat($recentEvents)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        return view('home', compact('latestMaterials', 'featuredPrompts', 'nextEvent', 'recentActivity'));
    }
}
