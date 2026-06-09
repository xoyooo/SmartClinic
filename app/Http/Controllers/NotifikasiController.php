<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function markRead(Notifikasi $notifikasi)
    {
        if ($notifikasi->user_id !== Auth::id()) {
            abort(403);
        }

        $notifikasi->delete();

        return back();
    }

    public function markAllRead()
    {
        Auth::user()->notifikasis()->delete();

        return back()->with('success', 'Semua notifikasi berhasil dibersihkan.');
    }
}