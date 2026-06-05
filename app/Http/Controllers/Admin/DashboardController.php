<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, User, Poli};

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $data = [
            'total_pasien_hari_ini' => Booking::whereDate('tanggal_booking', $today)
                ->whereIn('status', ['pending','checked_in','selesai'])->count(),
            'total_booking'         => Booking::count(),
            'total_poli'            => Poli::count(),
            'total_dokter'          => User::where('role', 'dokter')->count(),
            'booking_hari_ini'      => Booking::with(['pasien', 'jadwal.dokter.user', 'jadwal.poli'])
                ->whereDate('tanggal_booking', $today)->latest()->take(10)->get(),
            'checked_in_count'      => Booking::whereDate('tanggal_booking', $today)
                ->where('status', 'checked_in')->count(),
            'pending_users_count'   => User::where('role', 'pasien')->where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', $data);
    }
}