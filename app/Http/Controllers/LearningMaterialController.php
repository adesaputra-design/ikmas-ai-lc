<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\LearningMaterial;
use Illuminate\Http\Request;

class LearningMaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = LearningMaterial::with('category')
            ->where('is_published', true);

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('pillar')) {
            $query->where('pillar', $request->pillar);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $materials = $query->latest()->paginate(9)->withQueryString();
        $categories = Category::where('type', 'learning')->get();

        return view('learning.index', compact('materials', 'categories'));
    }

    public function show(string $slug)
    {
        $material = LearningMaterial::with('category')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $relatedMaterials = LearningMaterial::where('is_published', true)
            ->where('id', '!=', $material->id)
            ->where(function ($q) use ($material) {
                $q->where('pillar', $material->pillar)
                  ->orWhere('level', $material->level);
            })
            ->latest()
            ->take(3)
            ->get();

        return view('learning.show', compact('material', 'relatedMaterials'));
    }
}
