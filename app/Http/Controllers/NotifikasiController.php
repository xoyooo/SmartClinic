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

        $notifikasi->update([
            'is_read' => true
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function markAllRead()
    {
        Auth::user()
            ->notifikasis()
            ->where('is_read', false)
            ->update([
                'is_read' => true
            ]);

        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }
}