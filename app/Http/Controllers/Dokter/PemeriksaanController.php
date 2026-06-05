<?php
namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dokter\PemeriksaanRequest;
use App\Models\{Booking, Pemeriksaan, Resep, Notifikasi, User};

class PemeriksaanController extends Controller
{
    public function show(Booking $booking)
    {
        $this->authorizeBooking($booking);
        $booking->load(['pasien', 'jadwal.poli', 'pemeriksaan.reseps']);
        return view('dokter.periksa', compact('booking'));
    }

    public function store(PemeriksaanRequest $request, Booking $booking)
    {
        $this->authorizeBooking($booking);

        $pemeriksaan = Pemeriksaan::create([
            'booking_id' => $booking->id,
            'dokter_id'  => auth()->user()->dokter->id,
            'diagnosis'  => $request->diagnosis,
            'catatan'    => $request->catatan,
        ]);

        if ($request->filled('reseps')) {
            foreach ($request->reseps as $resep) {
                if (!empty($resep['nama_obat'])) {
                    Resep::create([
                        'pemeriksaan_id' => $pemeriksaan->id,
                        'nama_obat'      => $resep['nama_obat'],
                        'dosis'          => $resep['dosis'],
                        'aturan_pakai'   => $resep['aturan_pakai'],
                    ]);
                }
            }
        }

        $booking->update(['status' => 'selesai']);

        Notifikasi::create([
            'user_id' => $booking->pasien_id,
            'pesan'   => 'Pemeriksaan Anda telah selesai. Diagnosis tersedia. Silakan cek resep Anda.',
        ]);

        return redirect()->route('dokter.dashboard')->with('success', 'Pemeriksaan berhasil disimpan.');
    }

    public function riwayat()
    {
        $dokter   = auth()->user()->dokter;
        $riwayat  = Pemeriksaan::with(['booking.pasien', 'booking.jadwal.poli', 'reseps'])
            ->where('dokter_id', $dokter->id)->latest()->paginate(15);
        return view('dokter.riwayat', compact('riwayat'));
    }

    public function detailPasien(User $user)
    {
        // Hanya bisa melihat pasien
        if ($user->role !== 'pasien') abort(404);

        // Ambil seluruh riwayat pemeriksaan pasien ini
        $riwayatPemeriksaan = Pemeriksaan::with(['booking.jadwal.poli', 'booking.jadwal.dokter.user', 'reseps'])
            ->whereHas('booking', fn($q) => $q->where('pasien_id', $user->id))
            ->latest()
            ->get();

        // Statistik ringkas
        $totalKunjungan = Booking::where('pasien_id', $user->id)
            ->whereIn('status', ['pending', 'checked_in', 'selesai'])
            ->count();

        $totalSelesai = Booking::where('pasien_id', $user->id)
            ->where('status', 'selesai')
            ->count();

        return view('dokter.detail_pasien', compact('user', 'riwayatPemeriksaan', 'totalKunjungan', 'totalSelesai'));
    }

    private function authorizeBooking(Booking $booking): void
    {
        if ($booking->jadwal->dokter_id !== auth()->user()->dokter->id) {
            abort(403);
        }
    }
}