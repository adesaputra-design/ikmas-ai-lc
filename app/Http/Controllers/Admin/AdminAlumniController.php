<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAlumniController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');
        $query = $status === 'trashed' 
            ? User::onlyTrashed()->withCount('showcases')->latest() 
            : User::withCount('showcases')->latest();

        if ($request->filled('alumni_year')) {
            $query->where('alumni_year', $request->alumni_year);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('whatsapp_number', 'like', "%{$search}%")
                  ->orWhere('alumni_year', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();

        $alumniYears = User::whereNotNull('alumni_year')
            ->where('alumni_year', '!=', '')
            ->distinct()
            ->pluck('alumni_year')
            ->sortDesc();

        $totalMembers = User::where('role', 'member')->count();
        $totalTrashed = User::onlyTrashed()->count();

        return view('admin.alumni.index', compact('users', 'alumniYears', 'totalMembers', 'totalTrashed', 'status'));
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Hanya Administrator yang berwenang menonaktifkan akun anggota.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        if ($user->isAdmin()) {
            return back()->with('error', 'Akun Administrator tidak dapat dinonaktifkan langsung.');
        }

        $name = $user->name;
        $count = $user->showcases()->count();
        $user->delete();

        return back()->with('success', "Akun {$name} berhasil dinonaktifkan dan {$count} karyanya disembunyikan sementara.");
    }

    public function restore(int $id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Hanya Administrator yang berwenang memulihkan akun anggota.');
        }

        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return back()->with('success', "Akun {$user->name} berhasil dipulihkan.");
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filename = 'data-alumni-ikmas-ai-' . date('Y-m-d') . '.csv';

        $query = User::withCount('showcases')->latest();

        if ($request->filled('alumni_year')) {
            $query->where('alumni_year', $request->alumni_year);
        }

        $users = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($users) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header
            fputcsv($handle, [
                'No',
                'Nama Lengkap',
                'Alamat Email',
                'No. WhatsApp',
                'Tahun Angkatan',
                'Peran (Role)',
                'Total Karya Showcase',
                'Tanggal Bergabung',
            ]);

            foreach ($users as $index => $user) {
                fputcsv($handle, [
                    $index + 1,
                    $user->name,
                    $user->email,
                    $user->whatsapp_number ?? '-',
                    $user->alumni_year ?? '-',
                    $user->role === 'admin' ? 'Pengurus (Admin)' : 'Member Alumni',
                    $user->showcases_count ?? 0,
                    $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
