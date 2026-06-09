<?php
namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Pemeriksaan;
use Barryvdh\DomPDF\Facade\Pdf;

class ResepController extends Controller
{
    public function index()
    {
        $pemeriksaans = Pemeriksaan::whereHas('booking', function ($q) {
                $q->where('pasien_id', auth()->id());
            })
            ->whereHas('reseps')
            ->with(['booking.jadwal.poli', 'dokter.user', 'reseps'])
            ->latest()
            ->get();

        return view('pasien.resep_index', compact('pemeriksaans'));
    }

    public function show(Pemeriksaan $pemeriksaan)
    {
        if ($pemeriksaan->booking->pasien_id !== auth()->id()) abort(403);
        $pemeriksaan->load(['reseps', 'booking.pasien', 'booking.jadwal.poli', 'dokter.user']);
        return view('pasien.resep_show', compact('pemeriksaan'));
    }

    public function download(Pemeriksaan $pemeriksaan)
    {
        if ($pemeriksaan->booking->pasien_id !== auth()->id()) abort(403);
        $pemeriksaan->load(['reseps', 'booking.pasien', 'booking.jadwal.poli', 'dokter.user']);

        $pdf = Pdf::loadView('pasien.resep_pdf', compact('pemeriksaan'));
        $pdf->setPaper('A5', 'portrait');
        return $pdf->download('resep-' . $pemeriksaan->booking->kode_booking . '.pdf');
    }
}