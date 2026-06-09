<?php
namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $dokter  = auth()->user()->dokter;
        $today   = now()->toDateString();

        // Auto-expire past bookings
        \App\Models\Booking::whereIn('status', ['pending', 'checked_in'])
            ->where('expired_at', '<', now())
            ->update(['status' => 'expired']);

        $pasienHariIni = \App\Models\Booking::with(['pasien', 'jadwal.poli', 'pemeriksaan'])
            ->whereHas('jadwal', fn($q) => $q->where('dokter_id', $dokter->id))
            ->whereDate('tanggal_booking', $today)
            ->where('status', 'checked_in')
            ->get();

        $totalPasien = \App\Models\Booking::whereHas('jadwal', fn($q) => $q->where('dokter_id', $dokter->id))
            ->whereDate('tanggal_booking', $today)
            ->count();

        $totalSelesai = \App\Models\Booking::whereHas('jadwal', fn($q) => $q->where('dokter_id', $dokter->id))
            ->whereDate('tanggal_booking', $today)
            ->where('status', 'selesai')
            ->count();

        return view('dokter.dashboard', compact('pasienHariIni', 'totalPasien', 'totalSelesai', 'dokter'));
    }
}