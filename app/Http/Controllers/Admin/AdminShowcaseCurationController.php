<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showcase;
use Illuminate\Http\Request;

class AdminShowcaseCurationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $counts = [
            'pending' => Showcase::where('status', 'pending')->count(),
            'approved' => Showcase::where('status', 'approved')->count(),
            'rejected' => Showcase::where('status', 'rejected')->count(),
            'all' => Showcase::count(),
        ];

        $query = Showcase::with('user')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('tools_used', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $showcases = $query->paginate(12)->withQueryString();

        return view('admin.curation.index', compact('showcases', 'status', 'counts'));
    }

    public function approve(Showcase $showcase)
    {
        $showcase->update([
            'status' => 'approved',
            'admin_notes' => null,
        ]);

        return redirect()->route('admin.curation.index')->with('success', "Karya \"{$showcase->title}\" berhasil disetujui dan langsung tayang di galeri publik!");
    }

    public function reject(Request $request, Showcase $showcase)
    {
        $showcase->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('admin_notes', 'Belum memenuhi kelengkapan deskripsi atau screenshot karya.'),
        ]);

        return redirect()->route('admin.curation.index')->with('info', "Karya \"{$showcase->title}\" telah ditandai perlu revisi beserta catatan untuk alumni.");
    }

    public function toggleFeatured(Showcase $showcase)
    {
        $newStatus = ! $showcase->is_featured;
        $showcase->update(['is_featured' => $newStatus]);

        $msg = $newStatus 
            ? "Karya \"{$showcase->title}\" sekarang ditandai sebagai Karya Unggulan (Featured)!" 
            : "Status unggulan karya \"{$showcase->title}\" telah dicabut.";

        return redirect()->route('admin.curation.index')->with('success', $msg);
    }

    public function destroy(Showcase $showcase)
    {
        $title = $showcase->title;
        $showcase->delete();

        return redirect()->route('admin.curation.index')->with('success', "Karya \"{$title}\" berhasil dihapus.");
    }
}
