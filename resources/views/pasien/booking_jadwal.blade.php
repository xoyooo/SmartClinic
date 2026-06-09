@extends('layouts.patient')

@section('title', 'Pilih Jadwal Dokter')

@section('content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="space-y-3">
        <a href="{{ route('pasien.booking.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 font-bold uppercase tracking-wider">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali Ke Poliklinik
        </a>
        <div class="space-y-1">
            <h2 class="font-extrabold text-gray-900 text-lg">Jadwal Praktik Dokter</h2>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Langkah 2 dari 3: Pilih Jadwal Dokter di {{ $poli->nama_poli }}</p>
        </div>
    </div>

    <!-- Schedules List -->
    <div class="space-y-4">
        @forelse($jadwals as $jadwal)
            @php
                $tanggalJadwal = $jadwal->tanggal_jadwal; // Carbon instance
                $tanggalStr    = $tanggalJadwal->toDateString(); // Y-m-d
                $sisaSlot      = $jadwal->sisaKuota($tanggalStr);
                $isToday       = $tanggalStr === now()->toDateString();

                // Cek alasan kuota 0 (apakah karena Tutup atau Penuh)
                $isTutup = false;
                if ($isToday && $sisaSlot === 0) {
                    $allSlots = $jadwal->generateSlots();
                    $nowTime  = now()->format('H:i');
                    // Filter slot yang masih di depan/bisa di-booking hari ini
                    $futureSlots = array_filter($allSlots, fn($s) => $s > $nowTime);
                    if (empty($futureSlots)) {
                        $isTutup = true;
                    }
                }
            @endphp

            <div class="bg-white rounded-2xl border border-gray-200/85 p-5 shadow-sm space-y-4">
                <!-- Date Badge -->
                <div class="flex items-center gap-2">
                    @if($isToday)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-accent-50 text-accent-DEFAULT text-[10px] font-extrabold rounded-full border border-accent-100 uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 bg-accent-DEFAULT rounded-full animate-pulse inline-block"></span>
                            Hari Ini
                        </span>
                    @else
                        <span class="inline-flex px-2.5 py-1 bg-primary-50 text-primary-DEFAULT text-[10px] font-extrabold rounded-full border border-primary-100 uppercase tracking-wide">
                            {{ $tanggalJadwal->diffForHumans() }}
                        </span>
                    @endif
                </div>

                <div class="flex gap-4">
                    <div class="w-12 h-12 rounded-full bg-accent-50 border border-accent-100 flex items-center justify-center text-accent-DEFAULT font-extrabold shrink-0">
                        {{ strtoupper(substr($jadwal->dokter->user->name ?? 'D', 0, 1)) }}
                    </div>
                    <div class="space-y-0.5">
                        <h3 class="font-extrabold text-gray-900 text-base leading-snug">dr. {{ $jadwal->dokter->user->name ?? '-' }}</h3>
                        <p class="text-xs text-accent-DEFAULT font-bold uppercase tracking-wide">{{ $jadwal->dokter->spesialis ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3.5 rounded-xl text-xs font-semibold text-gray-500 border border-gray-100">
                    <div>
                        <span class="block text-[10px] text-gray-400 uppercase font-bold">Hari & Tanggal</span>
                        <span class="text-gray-800 font-bold">{{ $jadwal->hari }}, {{ tglID($tanggalJadwal, false) }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] text-gray-400 uppercase font-bold">Jam Kerja</span>
                        <span class="text-gray-800 font-bold">{{ str_replace(':', '.', substr($jadwal->jam_mulai, 0, 5)) }} – {{ str_replace(':', '.', substr($jadwal->jam_selesai, 0, 5)) }} WIB</span>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-50 pt-3.5">
                    <div>
                        <span class="block text-[10px] text-gray-400 uppercase font-bold">Slot Tersedia</span>
                        @if($sisaSlot > 0)
                            <span class="text-xs text-emerald-600 font-extrabold bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                {{ $sisaSlot }} slot kosong
                            </span>
                        @elseif($isTutup)
                            <span class="text-xs text-amber-700 font-extrabold bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-100">
                                Tutup
                            </span>
                        @else
                            <span class="text-xs text-red-600 font-extrabold bg-red-50 px-2.5 py-1 rounded-lg border border-red-100">
                                Penuh
                            </span>
                        @endif
                    </div>

                    @if($sisaSlot > 0)
                        <a href="{{ route('pasien.booking.form', $jadwal) }}?tanggal={{ $tanggalStr }}"
                           class="px-4 py-2 bg-primary-DEFAULT hover:bg-primary-600 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1">
                            Pilih
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @elseif($isTutup)
                        <button disabled class="px-4 py-2 bg-amber-50 text-amber-600 border border-amber-200 rounded-xl text-xs font-bold cursor-not-allowed">
                            Sudah Tutup
                        </button>
                    @else
                        <button disabled class="px-4 py-2 bg-gray-100 text-gray-400 border border-gray-200 rounded-xl text-xs font-bold cursor-not-allowed">
                            Penuh
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-200/85 p-8 text-center space-y-3">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-gray-700 text-sm">Belum Ada Jadwal</p>
                    <p class="text-xs text-gray-400 font-medium mt-1">Tidak ada jadwal dokter aktif untuk poliklinik {{ $poli->nama_poli }}.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
