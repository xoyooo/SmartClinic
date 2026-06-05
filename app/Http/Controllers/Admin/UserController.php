<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Dokter};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('dokter')->latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function create() { return redirect()->route('admin.users.index'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:8|confirmed',
            'role'      => 'required|in:admin,dokter',
            'no_hp'     => 'nullable|string|max:20',
            'spesialis' => 'required_if:role,dokter|nullable|string',
        ]);

        $user = User::create([
            'name' => $data['name'], 'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'], 'no_hp' => $data['no_hp'] ?? null,
        ]);

        if ($data['role'] === 'dokter') {
            Dokter::create(['user_id' => $user->id, 'spesialis' => $data['spesialis']]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user) { return redirect()->route('admin.users.index'); }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required','email', Rule::unique('users')->ignore($user->id)],
            'no_hp' => 'nullable|string|max:20',
            'spesialis' => 'nullable|string',
        ]);

        $user->update($data);

        if ($user->isDokter() && $request->filled('spesialis')) {
            $user->dokter()->updateOrCreate(
                ['user_id' => $user->id],
                ['spesialis' => $request->spesialis]
            );
        }

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dihapus.');
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:active,rejected',
        ]);

        $user->update([
            'status' => $request->status,
        ]);

        // Create notification for the user
        \App\Models\Notifikasi::create([
            'user_id' => $user->id,
            'pesan' => $request->status === 'active'
                ? 'Pendaftaran akun Anda telah disetujui oleh admin. Silakan login.'
                : 'Pendaftaran akun Anda ditolak oleh admin.',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Status akun ' . $user->name . ' berhasil diubah menjadi ' . $request->status . '.');
    }
}