@extends('layouts.app')
@section('title', 'Detail Pasien — ' . $user->name)
@section('page-title', 'Detail Pasien')
@section('page-subtitle', 'Riwayat lengkap pemeriksaan & rekam medis')

@section('content')
<div class="space-y-6">

    {{-- Back Button --}}
    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 font-bold transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>

    {{-- Patient Profile Card --}}
    <div class="bg-gradient-to-br from-primary-DEFAULT to-primary-700 rounded-2xl p-6 text-white shadow-lg shadow-primary-200/40">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center text-white font-extrabold text-2xl shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white/60 text-xs font-bold uppercase tracking-widest">Data Pasien</p>
                <h2 class="font-extrabold text-xl tracking-tight mt-0.5 truncate">{{ $user->name }}</h2>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5 text-xs text-white/75 font-semibold">
                    <span>📧 {{ $user->email }}</span>
                    @if($user->no_hp)
                        <span>📱 {{ $user->no_hp }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-2 gap-4 mt-5 pt-5 border-t border-white/10">
            <div>
                <p class="text-white/50 text-[10px] uppercase font-bold tracking-wide">Total Kunjungan</p>
                <p class="font-extrabold text-2xl mt-0.5">{{ $totalKunjungan }}</p>
            </div>
            <div>
                <p class="text-white/50 text-[10px] uppercase font-bold tracking-wide">Pemeriksaan Selesai</p>
                <p class="font-extrabold text-2xl mt-0.5">{{ $totalSelesai }}</p>
            </div>
        </div>
    </div>

    {{-- Medical History --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900">Riwayat Pemeriksaan</h3>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Semua rekam medis pasien ini</p>
            </div>
            <span class="px-3 py-1.5 bg-primary-50 text-primary-DEFAULT text-xs font-bold rounded-full border border-primary-100">
                {{ $riwayatPemeriksaan->count() }} Rekam Medis
            </span>
        </div>

        @if($riwayatPemeriksaan->isEmpty())
            <div class="px-5 py-16 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.375m0 0c.621 0 1.125.504 1.125 1.125V18m0-3a2.625 2.625 0 10-2.625 2.625M20.25 6.375c0 .621-.504 1.125-1.125 1.125H14.25a1.125 1.125 0 01-1.125-1.125V3.375c0-.621.504-1.125 1.125-1.125h4.875c.621 0 1.125.504 1.125 1.125v3z"/>
                    </svg>
                </div>
                <h4 class="font-bold text-gray-700">Belum Ada Rekam Medis</h4>
                <p class="text-sm text-gray-400 mt-1 font-medium">Pasien ini belum pernah menjalani pemeriksaan.</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($riwayatPemeriksaan as $periksa)
                    <div class="p-5 space-y-4">
                        {{-- Header: Tanggal & Poli --}}
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    {{ tglID($periksa->created_at, false) }}, {{ $periksa->created_at->format('H.i') }} WIB
                                </p>
                                <p class="font-bold text-gray-900 mt-0.5">
                                    {{ $periksa->booking->jadwal->poli->nama_poli ?? '—' }}
                                </p>
                                <p class="text-xs text-gray-500 font-semibold mt-0.5">
                                    dr. {{ $periksa->booking->jadwal->dokter->user->name ?? '—' }}
                                </p>
                            </div>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-full border border-emerald-100 uppercase shrink-0">
                                Selesai
                            </span>
                        </div>

                        {{-- Keluhan --}}
                        @if($periksa->booking->keluhan)
                            <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
                                <p class="text-[10px] font-bold text-amber-700 uppercase tracking-wider mb-1">Keluhan Pasien</p>
                                <p class="text-sm text-amber-900 font-semibold">{{ $periksa->booking->keluhan }}</p>
                            </div>
                        @endif

                        {{-- Diagnosis --}}
                        <div class="bg-primary-50 border border-primary-100 rounded-xl px-4 py-3">
                            <p class="text-[10px] font-bold text-primary-DEFAULT uppercase tracking-wider mb-1">Diagnosis</p>
                            <p class="text-sm text-primary-700 font-semibold leading-relaxed whitespace-pre-line">{{ $periksa->diagnosis }}</p>
                        </div>

                        {{-- Catatan --}}
                        @if($periksa->catatan)
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Catatan Dokter</p>
                                <p class="text-sm text-gray-600 font-medium leading-relaxed whitespace-pre-line">{{ $periksa->catatan }}</p>
                            </div>
                        @endif

                        {{-- Resep --}}
                        @if($periksa->reseps->isNotEmpty())
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Resep Obat ({{ $periksa->reseps->count() }} obat)</p>
                                <div class="overflow-x-auto rounded-xl border border-gray-100">
                                    <table class="min-w-full text-xs">
                                        <thead class="bg-gray-50">
                                            <tr class="text-gray-400 font-bold uppercase text-[9px] tracking-wider">
                                                <th class="px-4 py-2.5 text-left">Nama Obat</th>
                                                <th class="px-4 py-2.5 text-center">Dosis</th>
                                                <th class="px-4 py-2.5 text-left">Aturan Pakai</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50 bg-white">
                                            @foreach($periksa->reseps as $resep)
                                                <tr>
                                                    <td class="px-4 py-3 font-extrabold text-gray-900">{{ $resep->nama_obat }}</td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span class="inline-block px-2 py-0.5 bg-primary-50 rounded-lg text-primary-DEFAULT font-bold border border-primary-100">
                                                            {{ $resep->dosis }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-600 font-semibold">{{ $resep->aturan_pakai }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-gray-400 italic">Tidak ada resep obat pada pemeriksaan ini.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
