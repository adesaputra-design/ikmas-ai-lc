<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\LearningMaterial;
use App\Models\Prompt;

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

        return view('home', compact('latestMaterials', 'featuredPrompts', 'nextEvent'));
    }
}
