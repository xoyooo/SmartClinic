@extends('layouts.patient')

@section('title', 'Detail Resep Obat')

@section('content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="space-y-3">
        <a href="{{ route('pasien.riwayat') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 font-bold uppercase tracking-wider">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali Ke Riwayat
        </a>
        <div class="space-y-1">
            <h2 class="font-extrabold text-gray-900 text-lg">Hasil Pemeriksaan & Resep</h2>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Detail diagnosis & resep obat dari dokter</p>
        </div>
    </div>

    <!-- Doctor & Poli Card -->
    <div class="bg-white rounded-2xl border border-gray-200/85 p-5 shadow-sm space-y-4">
        <div class="flex gap-4">
            <div class="w-11 h-11 rounded-xl bg-primary-50 border border-primary-100 flex items-center justify-center text-primary-DEFAULT font-extrabold shrink-0">
                <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="space-y-0.5 text-xs font-semibold text-gray-500">
                <h3 class="font-extrabold text-gray-900 text-sm leading-snug">dr. {{ $pemeriksaan->dokter->user->name ?? '-' }}</h3>
                <p class="text-accent-DEFAULT font-bold uppercase tracking-wide">{{ $pemeriksaan->dokter->spesialis ?? '-' }}</p>
                <p class="text-[10px] text-gray-400 mt-1 font-bold">POLIKLINIK: {{ $pemeriksaan->booking->jadwal->poli->nama_poli ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Medical Details -->
    <div class="bg-white rounded-2xl border border-gray-200/85 p-5 shadow-sm space-y-4">
        <div class="space-y-1.5 border-b border-gray-50 pb-3">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Diagnosis</span>
            <p class="text-xs text-gray-800 font-bold leading-relaxed whitespace-pre-line">{{ $pemeriksaan->diagnosis }}</p>
        </div>

        <div class="space-y-1.5">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Catatan Tambahan / Instruksi</span>
            <p class="text-xs text-gray-600 font-semibold leading-relaxed whitespace-pre-line">{{ $pemeriksaan->catatan ?? 'Tidak ada catatan tambahan' }}</p>
        </div>
    </div>

    <!-- Prescription Box -->
    <div class="bg-white rounded-2xl border border-gray-200/85 p-5 shadow-sm space-y-4">
        <h3 class="font-extrabold text-gray-900 text-sm border-b border-gray-50 pb-2">Daftar Resep Obat</h3>

        @if($pemeriksaan->reseps->isEmpty())
            <p class="text-xs text-gray-400 italic text-center py-2">Tidak ada resep obat untuk pemeriksaan ini.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs text-left">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 font-bold uppercase text-[9px] tracking-wider">
                            <th class="py-2 pr-3">Nama Obat</th>
                            <th class="py-2 px-3 text-center">Dosis</th>
                            <th class="py-2 pl-3">Aturan Pakai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($pemeriksaan->reseps as $resep)
                            <tr class="align-middle">
                                <td class="py-3 pr-3 font-extrabold text-gray-900">{{ $resep->nama_obat }}</td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-block px-2 py-0.5 bg-primary-50 rounded-lg text-primary-DEFAULT font-bold border border-primary-100">
                                        {{ $resep->dosis }}
                                    </span>
                                </td>
                                <td class="py-3 pl-3 text-gray-600 font-semibold">{{ $resep->aturan_pakai }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Download PDF Button -->
    <a href="{{ route('pasien.resep.download', $pemeriksaan) }}" class="w-full flex items-center justify-center gap-1.5 px-4 py-3 bg-accent-DEFAULT hover:bg-accent-600 text-white rounded-2xl text-xs font-bold transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Download PDF
    </a>
</div>
@endsection
