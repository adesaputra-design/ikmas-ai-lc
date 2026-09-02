<?php

namespace App\Http\Controllers;

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

        return view('home', compact('latestMaterials', 'featuredPrompts'));
    }
}
