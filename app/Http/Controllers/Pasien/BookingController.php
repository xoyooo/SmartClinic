<?php
namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pasien\BookingRequest;
use App\Models\{Booking, JadwalPraktik, Poli, Notifikasi};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Hitung tanggal jadwal untuk pekan ini.
     * - Hari ini & belum selesai → kembalikan Carbon hari ini
     * - Hari ini tapi sudah lewat jam_selesai → null (sudah lewat)
     * - Hari ke depan dalam minggu ini → kembalikan Carbon tanggalnya
     * - Hari yang sudah lewat minggu ini → null (sudah lewat)
     */
    private function getNextDateForJadwal(JadwalPraktik $jadwal): ?Carbon
    {
        // Jadwal berbasis tanggal spesifik
        if ($jadwal->tanggal) {
            $tanggal = Carbon::parse($jadwal->tanggal)->startOfDay();
            $today   = now()->startOfDay();

            // Jadwal yang sudah lewat
            if ($tanggal->lt($today)) return null;

            // Jadwal hari ini — cek apakah jam sudah selesai
            if ($tanggal->eq($today)) {
                if (now()->format('H:i:s') >= $jadwal->jam_selesai) return null;
            }

            return $tanggal;
        }

        // Fallback: jadwal berbasis hari-berulang (data lama)
        $dayMap = [
            'Minggu' => 0, 'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
            'Kamis'  => 4, 'Jumat' => 5, 'Sabtu'  => 6,
        ];
        $today      = now();
        $targetDow  = $dayMap[$jadwal->hari] ?? 0;
        $currentDow = $today->dayOfWeek;
        $diff       = $targetDow - $currentDow;
        if ($diff < 0) return null;
        if ($diff === 0 && $today->format('H:i:s') >= $jadwal->jam_selesai) return null;
        return $today->copy()->addDays($diff)->startOfDay();
    }

    public function index()
    {
        $polis = Poli::with('jadwalPraktiks')->get();

        // Hitung hanya jadwal yang masih punya slot tersedia minggu ini
        $polis->each(function ($poli) {
            $poli->jadwal_aktif_count = $poli->jadwalPraktiks
                ->filter(fn($jadwal) => $this->getNextDateForJadwal($jadwal) !== null)
                ->count();
        });

        return view('pasien.booking_index', compact('polis'));
    }

    public function jadwal(Request $request)
    {
        $request->validate(['poli_id' => 'required|exists:polis,id']);

        // Auto-expire past bookings
        Booking::whereIn('status', ['pending', 'checked_in'])
            ->where('expired_at', '<', now())
            ->update(['status' => 'expired']);

        $rawJadwals = JadwalPraktik::with(['dokter.user', 'poli'])
            ->where('poli_id', $request->poli_id)
            ->get();

        // Hanya tampilkan jadwal hari ini dan mendatang; skip yang sudah lewat
        $jadwals = $rawJadwals->map(function ($jadwal) {
            $tanggal = $this->getNextDateForJadwal($jadwal);
            if ($tanggal === null) return null; // sudah lewat, skip
            $jadwal->tanggal_jadwal = $tanggal;
            return $jadwal;
        })->filter()->sortBy('tanggal_jadwal')->values();

        $poli = Poli::find($request->poli_id);
        return view('pasien.booking_jadwal', compact('jadwals', 'poli'));
    }

    public function form(JadwalPraktik $jadwal, Request $request)
    {
        // Gunakan tanggal dari jadwal spesifik jika ada, atau dari query string
        if ($jadwal->tanggal) {
            $tanggal = $jadwal->tanggal->toDateString();
        } else {
            $request->validate(['tanggal' => 'required|date|after_or_equal:today']);
            $tanggal = $request->tanggal;

            // Validasi hari (untuk jadwal lama berbasis hari berulang)
            $dayMap = [
                'Minggu' => 0, 'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
                'Kamis'  => 4, 'Jumat' => 5, 'Sabtu'  => 6,
            ];
            $targetDate = Carbon::parse($tanggal);
            if (($dayMap[$jadwal->hari] ?? -1) !== $targetDate->dayOfWeek) {
                return redirect()->back()->with('error', 'Tanggal tidak sesuai dengan hari jadwal.');
            }
        }

        $allSlots = $jadwal->generateSlots();

        $bookedSlots = Booking::where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal_booking', $tanggal)
            ->whereIn('status', ['pending', 'checked_in'])
            ->pluck('slot_waktu')
            ->map(fn($t) => substr($t, 0, 5))
            ->toArray();

        $jadwal->load(['dokter.user', 'poli']);
        $tanggalLabel = tglID(\Carbon\Carbon::parse($tanggal));

        return view('pasien.booking_form', compact('jadwal', 'allSlots', 'bookedSlots', 'tanggal', 'tanggalLabel'));
    }

    public function store(BookingRequest $request)
    {
        $jadwal  = JadwalPraktik::findOrFail($request->jadwal_id);
        $slot    = $request->slot_waktu;
        $tanggal = $request->tanggal;

        // Validasi slot tersedia pada tanggal tersebut
        $available = $jadwal->getAvailableSlots($tanggal);
        if (!in_array($slot, $available)) {
            return back()->with('error', 'Slot waktu yang dipilih sudah tidak tersedia. Silakan pilih slot lain.')->withInput();
        }

        // Cek duplikat: satu pasien, satu jadwal, satu tanggal
        $existing = Booking::where('pasien_id', auth()->id())
            ->where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal_booking', $tanggal)
            ->whereIn('status', ['pending', 'checked_in'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki booking aktif untuk jadwal ini pada tanggal tersebut.')->withInput();
        }

        $kode = strtoupper(Str::random(8));

        // expired_at = tanggal booking + jam akhir slot
        $expiredAt = Carbon::parse($tanggal)
            ->setTimeFromTimeString($slot)
            ->addMinutes(JadwalPraktik::SLOT_DURATION);

        $booking = Booking::create([
            'pasien_id'       => auth()->id(),
            'jadwal_id'       => $jadwal->id,
            'slot_waktu'      => $slot,
            'keluhan'         => $request->keluhan,
            'kode_booking'    => $kode,
            'qr_code_path'    => null,
            'status'          => 'pending',
            'tanggal_booking' => $tanggal,
            'expired_at'      => $expiredAt,
        ]);

        Notifikasi::create([
            'user_id' => auth()->id(),
            'pesan'   => 'Booking berhasil! Kode: ' . $kode . ' — Tanggal: ' . tglID(\Carbon\Carbon::parse($tanggal), false) . ', Slot: ' . $slot . ' WIB.',
        ]);

        return redirect()->route('pasien.booking.show', $booking)
            ->with('success', 'Booking berhasil! Slot ' . $slot . ' WIB tanggal ' . tglID(\Carbon\Carbon::parse($tanggal), false) . ' telah dikonfirmasi.');
    }

    public function show(Booking $booking)
    {
        if ($booking->pasien_id !== auth()->id()) abort(403);

        // Auto-expire past bookings
        Booking::whereIn('status', ['pending', 'checked_in'])
            ->where('expired_at', '<', now())
            ->update(['status' => 'expired']);

        $booking->refresh();
        $booking->load(['jadwal.dokter.user', 'jadwal.poli']);
        return view('pasien.booking_show', compact('booking'));
    }
}