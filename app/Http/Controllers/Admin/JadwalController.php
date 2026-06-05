<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JadwalRequest;
use App\Models\{JadwalPraktik, Dokter, Poli};
use Carbon\Carbon;

class JadwalController extends Controller
{
    // Nama hari Indonesia dari dayOfWeek
    private function hariFromTanggal(string $tanggal): string
    {
        $map = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        return $map[Carbon::parse($tanggal)->dayOfWeek];
    }

    public function index()
    {
        $jadwals = JadwalPraktik::with(['dokter.user', 'poli'])->latest()->get();
        $dokters = Dokter::with('user')->get();
        $polis   = Poli::all();
        return view('admin.jadwal', compact('jadwals', 'dokters', 'polis'));
    }

    public function create()
    {
        return redirect()->route('admin.jadwal.index');
    }

    public function store(JadwalRequest $request)
    {
        $data = $request->validated();
        $data['hari'] = $this->hariFromTanggal($data['tanggal']);
        JadwalPraktik::create($data);
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(JadwalPraktik $jadwal)
    {
        return redirect()->route('admin.jadwal.index');
    }

    public function update(JadwalRequest $request, JadwalPraktik $jadwal)
    {
        $data = $request->validated();
        $data['hari'] = $this->hariFromTanggal($data['tanggal']);
        $jadwal->update($data);
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(JadwalPraktik $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}