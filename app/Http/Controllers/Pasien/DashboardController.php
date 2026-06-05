<?php
namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Auto-expire past bookings
        Booking::whereIn('status', ['pending', 'checked_in'])
            ->where('expired_at', '<', now())
            ->update(['status' => 'expired']);

        $bookingAktif = Booking::with(['jadwal.dokter.user', 'jadwal.poli'])
            ->where('pasien_id', $user->id)
            ->whereIn('status', ['pending', 'checked_in'])
            ->latest()->first();

        $bookingTerbaru = Booking::with(['jadwal.dokter.user', 'jadwal.poli'])
            ->where('pasien_id', $user->id)
            ->latest()->take(5)->get();

        $pemeriksaanTerbaru = \App\Models\Pemeriksaan::with(['booking', 'reseps'])
            ->whereHas('booking', fn($q) => $q->where('pasien_id', $user->id))
            ->latest()
            ->first();

        return view('pasien.dashboard', compact('bookingAktif', 'bookingTerbaru', 'pemeriksaanTerbaru'));
    }

    public function riwayat()
    {
        // Auto-expire past bookings
        Booking::whereIn('status', ['pending', 'checked_in'])
            ->where('expired_at', '<', now())
            ->update(['status' => 'expired']);

        $bookings = Booking::with(['jadwal.dokter.user', 'jadwal.poli', 'pemeriksaan'])
            ->where('pasien_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pasien.riwayat', compact('bookings'));
    }
}