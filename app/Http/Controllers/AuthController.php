<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return (Auth::user()->isAdmin() || Auth::user()->isStaff())
                ? redirect()->route('admin.dashboard')
                : redirect()->route('member.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek apakah akun subscriber masih pending atau ditolak
        $existingUser = User::where('email', $credentials['email'])->first();
        if ($existingUser) {
            if ($existingUser->isPending()) {
                return back()->withErrors([
                    'email' => 'Akun Anda masih dalam proses peninjauan oleh Pengurus IKMAS AI. Harap tunggu konfirmasi melalui WhatsApp.',
                ])->onlyInput('email');
            }
            if ($existingUser->isRejected()) {
                return back()->withErrors([
                    'email' => 'Pendaftaran Anda tidak dapat disetujui. Silakan hubungi kami untuk informasi lebih lanjut.',
                ])->onlyInput('email');
            }
        }

        // Cek apakah akun dinonaktifkan (soft-deleted)
        if (User::onlyTrashed()->where('email', $credentials['email'])->exists()) {
            return back()->withErrors([
                'email' => 'Akun ini telah dinonaktifkan oleh Pengurus IKMAS AI. Silakan hubungi Administrator untuk informasi lebih lanjut.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin() || Auth::user()->isStaff()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('member.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang kamu masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showAlumniRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }

        return view('auth.register-alumni');
    }

    public function registerAlumni(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
            'whatsapp_number' => ['required', 'string', 'max:25'],
            'alumni_year'     => ['required', 'string', 'max:10'],
        ]);

        $user = User::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'password'        => Hash::make($validated['password']),
            'whatsapp_number' => $validated['whatsapp_number'],
            'alumni_year'     => $validated['alumni_year'],
            'role'            => 'member',
            'status'          => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('member.dashboard')->with('success', 'Selamat datang di IKMAS AI! Akun alumni berhasil dibuat.');
    }

    public function showSubscriberRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }

        return view('auth.register-subscriber');
    }

    public function registerSubscriber(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
            'whatsapp_number' => ['required', 'string', 'max:25'],
        ]);

        User::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'password'        => Hash::make($validated['password']),
            'whatsapp_number' => $validated['whatsapp_number'],
            'role'            => 'subscriber',
            'status'          => 'pending',
        ]);

        // Tidak login otomatis — tunggu approval admin
        return redirect()->route('register.subscriber.pending');
    }

    public function subscriberPending()
    {
        return view('auth.subscriber-pending');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
