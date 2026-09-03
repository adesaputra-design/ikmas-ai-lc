<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'pending');

        $query = User::where('role', 'subscriber')->latest();

        if ($filter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($filter === 'active') {
            $query->where('status', 'active');
        } elseif ($filter === 'rejected') {
            $query->where('status', 'rejected');
        }

        $subscribers = $query->paginate(20)->withQueryString();

        $counts = [
            'all'      => User::where('role', 'subscriber')->count(),
            'pending'  => User::where('role', 'subscriber')->where('status', 'pending')->count(),
            'active'   => User::where('role', 'subscriber')->where('status', 'active')->count(),
            'rejected' => User::where('role', 'subscriber')->where('status', 'rejected')->count(),
        ];

        return view('admin.subscribers.index', compact('subscribers', 'counts', 'filter'));
    }

    public function approve(User $user)
    {
        if (! $user->isSubscriber()) {
            return back()->with('error', 'User ini bukan subscriber.');
        }

        $user->update(['status' => 'active']);

        return back()->with('success', "Akun {$user->name} berhasil diaktifkan.");
    }

    public function reject(User $user)
    {
        if (! $user->isSubscriber()) {
            return back()->with('error', 'User ini bukan subscriber.');
        }

        $user->update(['status' => 'rejected']);

        return back()->with('info', "Pendaftaran {$user->name} telah ditolak.");
    }

    public function destroy(User $user)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Hanya Administrator yang berwenang menghapus akun subscriber.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Akun subscriber {$name} berhasil dihapus.");
    }
}
