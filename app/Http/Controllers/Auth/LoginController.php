<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login user
     */
    public function login(Request $request)
    {
        // Validasi input user
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Coba autentikasi
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Arahkan berdasarkan role - FIXED
            $userRole = Auth::user()->role;
            
            if (str_contains($userRole, 'Kasir')) {
                return redirect()->route('kasir.dashboard');
            } elseif (str_contains($userRole, 'Admin')) {
                return redirect()->route('admin.dashboard');
            } elseif (str_contains($userRole, 'Kepala')) {
                return redirect()->route('kepala.dashboard');
            } elseif (str_contains($userRole, 'Supplier')) {
                return redirect()->route('supplier.dashboard');
            } else {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'role' => 'Role tidak dikenali: ' . $userRole,
                ]);
            }
        }

        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}