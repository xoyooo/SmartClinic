@extends('layouts.patient')
@section('title', 'Resep Obat')

@section('content')
<div class="space-y-4">
    <div class="bg-gradient-to-r from-accent-DEFAULT to-accent-600 rounded-2xl p-5 text-white shadow-md shadow-accent-200">
        <h2 class="font-extrabold text-lg">Daftar Resep Obat</h2>
        <p class="text-white/80 text-xs font-semibold mt-1">
            Kumpulan resep obat dari hasil pemeriksaan dokter Anda.
        </p>
    </div>

    @forelse($pemeriksaans as $p)
        <a href="{{ route('pasien.resep.show', $p) }}"
           class="block bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:border-accent-DEFAULT/30 hover:shadow-md transition group">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-accent-50 text-accent-DEFAULT rounded-xl flex items-center justify-center shrink-0 group-hover:bg-accent-DEFAULT group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-sm">{{ $p->booking->jadwal->poli->nama_poli ?? '-' }}</p>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Dokter: {{ $p->dokter->user->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">{{ $p->created_at?->format('d M Y') }}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 bg-accent-50 text-accent-700 text-[10px] font-bold rounded-full">
                        {{ $p->reseps->count() }} Obat
                    </span>
                </div>
            </div>
        </a>
    @empty
        <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-8 text-center">
            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            <p class="font-bold text-gray-700 text-sm">Belum Ada Resep Obat</p>
            <p class="text-xs text-gray-400 font-medium mt-1">Resep obat Anda akan muncul di sini setelah pemeriksaan selesai.</p>
        </div>
    @endforelse
</div>
@endsection
