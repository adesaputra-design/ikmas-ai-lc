<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStaffPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        // Admin utama selalu memiliki akses tanpa batas
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Staf dicek apakah memiliki izin modul terkait
        if ($user->isStaff() && $user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'Akses Dibatasi. Anda belum memiliki izin tugas untuk mengelola modul ini. Silakan hubungi Administrator.');
    }
}
