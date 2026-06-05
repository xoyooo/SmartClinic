@extends('layouts.patient')

@section('title', 'Pilih Poliklinik')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="space-y-1">
        <h2 class="font-extrabold text-gray-900 text-lg">Pilih Poliklinik</h2>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Langkah 1 dari 3: Tentukan Poliklinik Tujuan</p>
    </div>

    <!-- Polyclinic Grid List -->
    <div class="grid grid-cols-1 gap-4">
        @forelse($polis as $poli)
            <div class="bg-white rounded-2xl border border-gray-200/85 p-5 shadow-sm hover:border-accent-DEFAULT/30 transition flex flex-col justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center text-primary-DEFAULT shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <h3 class="font-extrabold text-gray-900 text-base leading-tight">{{ $poli->nama_poli }}</h3>
                        </div>
                        <p class="text-xs text-gray-500 font-medium leading-relaxed mt-0.5">
                            {{ $poli->deskripsi ?? 'Poliklinik umum klinik SmartClinic, menyediakan penanganan medis terpercaya.' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-50 pt-3 text-xs font-semibold">
                    @if($poli->jadwal_aktif_count > 0)
                        <span class="text-gray-400">
                            Jadwal tersedia: <span class="text-primary-DEFAULT font-bold">{{ $poli->jadwal_aktif_count }} Dokter</span>
                        </span>
                        <a href="{{ route('pasien.booking.jadwal', ['poli_id' => $poli->id]) }}" class="px-4 py-2 bg-accent-DEFAULT hover:bg-accent-600 text-white rounded-xl font-bold transition shadow-sm flex items-center gap-1">
                            Pilih Poli
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <span class="text-gray-400 italic">Tidak ada jadwal tersedia minggu ini</span>
                        <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-xl font-bold cursor-not-allowed">
                            Tidak Tersedia
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-200/85 p-8 text-center text-xs text-gray-400">
                Poliklinik tidak tersedia untuk sementara waktu.
            </div>
        @endforelse
    </div>
</div>
@endsection
