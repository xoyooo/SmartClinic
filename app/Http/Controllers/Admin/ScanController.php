<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Notifikasi};
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index() { return view('admin.scan'); }

    public function validate_qr(Request $request)
    {
        $request->validate(['kode_booking' => 'required|string']);
        $kode = $request->kode_booking;

        $booking = Booking::with(['pasien', 'jadwal.dokter.user', 'jadwal.poli'])
            ->where('kode_booking', $kode)->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid / booking tidak ditemukan.'], 404);
        }

        // Cek expired
        if (now()->gt($booking->expired_at) || $booking->status === 'expired') {
            $booking->update(['status' => 'expired']);
            return response()->json(['success' => false, 'message' => 'QR Code sudah expired. Jadwal telah lewat.'], 400);
        }

        if ($booking->status === 'checked_in') {
            return response()->json(['success' => false, 'message' => 'Pasien sudah melakukan check-in sebelumnya.'], 400);
        }

        if ($booking->status === 'selesai') {
            return response()->json(['success' => false, 'message' => 'Pemeriksaan sudah selesai.'], 400);
        }

        $booking->update(['status' => 'checked_in']);

        // Notifikasi ke dokter
        if ($booking->jadwal->dokter && $booking->jadwal->dokter->user) {
            Notifikasi::create([
                'user_id' => $booking->jadwal->dokter->user_id,
                'pesan'   => 'Pasien ' . $booking->pasien->name . ' telah check-in untuk poli ' . $booking->jadwal->poli->nama_poli . '.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil!',
            'data'    => [
                'nama_pasien' => $booking->pasien->name,
                'poli'        => $booking->jadwal->poli->nama_poli,
                'dokter'      => $booking->jadwal->dokter->user->name,
                'no_antrian'  => $booking->nomorAntrian(),
                'kode'        => $booking->kode_booking,
            ],
        ]);
    }
}