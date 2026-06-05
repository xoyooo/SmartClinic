<?php

namespace App\Http\Controllers;

use App\Models\{User, Notifikasi};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Auth::check()
            ? redirect()->route(Auth::user()->role . '.dashboard')
            : view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah.'
            ])->withInput();
        }

        $request->session()->regenerate();

        if (Auth::user()->role === 'pasien' && Auth::user()->status !== 'active') {
            $status = Auth::user()->status;
            Auth::logout();

            return back()->withErrors([
                'email' => $status === 'rejected'
                    ? 'Akun Anda ditolak admin.'
                    : 'Akun Anda masih menunggu persetujuan admin.'
            ])->withInput();
        }

        $role = Auth::user()->role;

        return redirect()
            ->route($role . '.dashboard')
            ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'no_hp'    => ['nullable', 'string', 'max:20'],
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'pasien',
            'status'   => 'pending',
            'no_hp'    => $request->no_hp,
        ]);

        Notifikasi::create([
            'user_id' => $user->id,
            'pesan'   => 'Registrasi berhasil. Akun Anda sedang menunggu persetujuan admin.',
        ]);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notifikasi::create([
                'user_id' => $admin->id,
                'pesan'   => 'Pendaftaran pasien baru: ' . $user->name . ' memerlukan persetujuan.',
            ]);
        }

        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil. Silakan tunggu akun Anda di-ACC admin.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }
}