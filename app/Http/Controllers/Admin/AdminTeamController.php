<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTeamController extends Controller
{
    public const AVAILABLE_PERMISSIONS = [
        'materials' => [
            'label' => 'Kelola Materi Belajar',
            'desc' => 'Menambah, mengubah, dan menghapus materi serta kategori edukasi AI.',
        ],
        'prompts' => [
            'label' => 'Prompt Library',
            'desc' => 'Mengelola koleksi prompt siap pakai & kurasi prompt unggulan.',
        ],
        'events' => [
            'label' => 'Agenda Kegiatan',
            'desc' => 'Membuat jadwal kegiatan/meetup dan generator broadcast WhatsApp.',
        ],
        'curation' => [
            'label' => 'Kurasi Karya Showcase',
            'desc' => 'Menyetujui, menolak revisi, dan menandai karya showcase unggulan.',
        ],
        'alumni' => [
            'label' => 'Direktori Alumni',
            'desc' => 'Melihat daftar direktori alumni dan mengunduh berkas laporan CSV.',
        ],
        'pages' => [
            'label' => 'Konten Halaman Tentang',
            'desc' => 'Mengedit informasi profil, visi-misi, dan FAQ landing page.',
        ],
    ];

    public function index(Request $request)
    {
        $status = $request->get('status', 'active');
        $query = $status === 'trashed' 
            ? User::onlyTrashed()->withCount('showcases') 
            : User::withCount('showcases');

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

        $users = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_admin' => User::where('role', 'admin')->count(),
            'total_staff' => User::where('role', 'staff')->count(),
            'total_member' => User::where('role', 'member')->count(),
            'total_trashed' => User::onlyTrashed()->count(),
        ];

        $availablePermissions = self::AVAILABLE_PERMISSIONS;

        return view('admin.team.index', compact('users', 'stats', 'status', 'availablePermissions'));
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,staff,member'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', array_keys(self::AVAILABLE_PERMISSIONS))],
        ]);

        // Proteksi Anti-Lockout: Admin tidak boleh menurunkan perannya sendiri jika satu-satunya admin aktif
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            $otherAdmins = User::where('role', 'admin')->where('id', '!=', $user->id)->count();
            if ($otherAdmins === 0) {
                return back()->with('error', 'Pencegahan Keamanan: Anda tidak dapat menurunkan peran Anda sendiri karena Anda adalah satu-satunya Administrator aktif di sistem.');
            }
        }

        $user->role = $validated['role'];

        if ($validated['role'] === 'staff') {
            $user->permissions = $validated['permissions'] ?? [];
        } else {
            $user->permissions = null;
        }

        $user->save();

        $roleName = match ($user->role) {
            'admin' => 'Administrator',
            'staff' => 'Staf Pengurus',
            default => 'Member Alumni',
        };

        return back()->with('success', "Peran dan tugas untuk {$user->name} berhasil diperbarui menjadi {$roleName}.");
    }

    public function destroy(User $user)
    {
        // Proteksi Anti-Lockout: Tidak boleh menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Pencegahan Keamanan: Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif.');
        }

        // Proteksi Admin: Tidak boleh menonaktifkan admin lain secara langsung
        if ($user->isAdmin()) {
            return back()->with('error', 'Akun Administrator tidak dapat dinonaktifkan langsung. Silakan ubah perannya menjadi Member terlebih dahulu.');
        }

        $name = $user->name;
        $showcaseCount = $user->showcases()->count();
        $user->delete();

        return back()->with('success', "Akun {$name} berhasil dinonaktifkan. Seluruh {$showcaseCount} karya showcase miliknya otomatis disembunyikan sementara.");
    }

    public function restore(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return back()->with('success', "Akun {$user->name} berhasil dipulihkan kembali dan dapat login seperti semula.");
    }
}
