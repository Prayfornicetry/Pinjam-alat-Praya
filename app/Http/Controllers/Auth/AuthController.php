<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Tampilkan Form Login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses Login
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        $remember = $request->filled('remember');

        // Cek apakah user ada
        $user = User::where('email', $request->email)->first();

        // Cek apakah user nonaktif
        if ($user && !$user->is_active) {
            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ])->onlyInput('email');
        }

        // Proses autentikasi
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Redirect berdasarkan role
            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('dashboard'));
            } elseif (Auth::user()->isStaff()) {
                return redirect()->intended(route('staff.dashboard'));
            } else {
                return redirect()->intended(route('user.dashboard'));
            }
        }

        // ✅ Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan Form Register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses Register - ✅ REDIRECT KE LOGIN
     */
    public function register(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Nama lengkap harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        try {
            // Buat user baru - ✅ OTOMATIS ROLE USER
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'user',  // ✅ HARD-CODE ROLE KE 'USER'
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            // ✅ HAPUS AUTO LOGIN - Redirect ke login page
            // Auth::login($user);  // ← DIKOMENTARI/DIHAPUS

            // ✅ Redirect ke login dengan success message
            return redirect()->route('login')
                ->with('success', '✅ Akun berhasil dibuat! Silakan login dengan email dan password Anda.');

        } catch (\Exception $e) {
            \Log::error('Register error: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Gagal membuat akun. Silakan coba lagi.'])
                ->withInput();
        }
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah logout.');
    }
}