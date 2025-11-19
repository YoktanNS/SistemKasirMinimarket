<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek jika user belum login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek jika role user tidak sesuai
        $user = auth()->user();
        if ($user->role !== $role) {
            abort(403, 'Akses ditolak. Hanya untuk role: ' . $role . '. Role Anda: ' . $user->role);
        }

        return $next($request);
    }
}