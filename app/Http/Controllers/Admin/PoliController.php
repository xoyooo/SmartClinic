<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PoliRequest;
use App\Models\Poli;

class PoliController extends Controller
{
    public function index()    { return view('admin.poli', ['polis' => Poli::withCount('jadwalPraktiks')->latest()->get()]); }
    public function create()   { return redirect()->route('admin.poli.index'); }

    public function store(PoliRequest $request)
    {
        Poli::create($request->validated());
        return redirect()->route('admin.poli.index')->with('success', 'Poli berhasil ditambahkan.');
    }

    public function edit(Poli $poli)   { return redirect()->route('admin.poli.index'); }

    public function update(PoliRequest $request, Poli $poli)
    {
        $poli->update($request->validated());
        return redirect()->route('admin.poli.index')->with('success', 'Poli berhasil diperbarui.');
    }

    public function destroy(Poli $poli)
    {
        $poli->delete();
        return redirect()->route('admin.poli.index')->with('success', 'Poli berhasil dihapus.');
    }
}