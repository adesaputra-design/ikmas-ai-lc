<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\LearningMaterial;
use App\Models\Prompt;
use App\Models\Showcase;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_members' => User::where('role', 'member')->count(),
            'total_materials' => LearningMaterial::count(),
            'total_prompts' => Prompt::count(),
            'total_events' => Event::count(),
            'pending_curation' => Showcase::where('status', 'pending')->count(),
            'approved_showcases' => Showcase::where('status', 'approved')->count(),
        ];

        $pendingShowcases = Showcase::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $recentShowcases = Showcase::with('user')
            ->where('status', '!=', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $topPrompts = Prompt::orderBy('copy_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('metrics', 'pendingShowcases', 'recentShowcases', 'topPrompts'));
    }

    public function approveShowcase(Showcase $showcase)
    {
        $showcase->update([
            'status' => 'approved',
            'admin_notes' => null,
        ]);

        return redirect()->route('admin.dashboard')->with('success', "Karya \"{$showcase->title}\" berhasil disetujui dan kini tayang di etalase publik!");
    }

    public function rejectShowcase(Request $request, Showcase $showcase)
    {
        $showcase->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('admin_notes', 'Belum memenuhi kriteria kurasi.'),
        ]);

        return redirect()->route('admin.dashboard')->with('info', "Karya \"{$showcase->title}\" telah ditandai perlu revisi dengan catatan yang dikirimkan.");
    }
}
