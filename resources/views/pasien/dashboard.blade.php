@extends('layouts.patient')
@section('title', 'Beranda')

@section('content')
<div class="space-y-5">

    {{-- Greeting --}}
    <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary-DEFAULT to-accent-DEFAULT flex items-center justify-center text-white font-extrabold text-base shadow-md shadow-primary-100">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Selamat Datang</p>
            <h2 class="font-extrabold text-gray-900 text-base leading-tight">{{ auth()->user()->name }}</h2>
        </div>
    </div>

    {{-- Resep Banner (muncul hanya jika ada pemeriksaan selesai) --}}
    @if($pemeriksaanTerbaru)
        <a href="{{ route('pasien.resep.show', $pemeriksaanTerbaru) }}"
           class="flex items-center gap-3.5 bg-gradient-to-r from-accent-DEFAULT to-accent-600 rounded-2xl p-4 text-white shadow-md shadow-accent-100/50 hover:opacity-95 transition">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-extrabold text-sm leading-tight">Resep Dokter Tersedia!</p>
                <p class="text-white/80 text-xs font-semibold mt-0.5 truncate">
                    {{ $pemeriksaanTerbaru->reseps->count() > 0 ? $pemeriksaanTerbaru->reseps->count() . ' obat terdaftar — Tap untuk lihat & cetak PDF' : 'Tap untuk lihat hasil pemeriksaan' }}
                </p>
            </div>
            <svg class="w-4 h-4 text-white/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    @endif

    {{-- Active Booking Card --}}
    @if($bookingAktif)
        <div class="relative bg-gradient-to-br from-primary-DEFAULT via-primary-600 to-primary-700 rounded-2xl p-5 text-white overflow-hidden shadow-lg shadow-primary-200/50">
            {{-- Background decorations --}}
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/5 rounded-full"></div>
            <div class="absolute -right-2 -bottom-8 w-32 h-32 bg-white/5 rounded-full"></div>

            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-white/70 text-[10px] font-bold uppercase tracking-widest">Nomor Antrian Aktif</span>
                    <span class="px-2.5 py-1 bg-accent-DEFAULT/90 text-white text-[10px] font-extrabold rounded-full uppercase tracking-wide">
                        {{ strtoupper($bookingAktif->status) }}
                    </span>
                </div>

                <h3 class="font-extrabold text-2xl tracking-tight">{{ $bookingAktif->kode_booking }}</h3>
                <p class="text-white/70 text-xs font-bold mt-0.5">{{ $bookingAktif->jadwal->poli->nama_poli ?? '-' }}</p>

                <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-white/10">
                    <div>
                        <p class="text-white/50 text-[10px] uppercase font-bold tracking-wide">Dokter</p>
                        <p class="text-white font-bold text-sm mt-0.5">{{ $bookingAktif->jadwal->dokter->user->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-white/50 text-[10px] uppercase font-bold tracking-wide">Jam Praktik</p>
                        <p class="text-white font-bold text-sm mt-0.5">
                            {{ substr($bookingAktif->jadwal->jam_mulai, 0, 5) }}–{{ substr($bookingAktif->jadwal->jam_selesai, 0, 5) }} WIB
                        </p>
                    </div>
                </div>

                <a href="{{ route('pasien.booking.show', $bookingAktif) }}"
                   class="mt-4 flex items-center justify-center gap-2 w-full py-2.5 bg-white/20 hover:bg-white/30 text-white font-bold text-sm rounded-xl transition border border-white/20 backdrop-blur-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m-6 8h12M4 9h16M4 13h16M4 17h16M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/></svg>
                    Lihat Tiket &amp; QR Code
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-5 text-center">
            <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            </div>
            <p class="font-bold text-gray-700 text-sm">Tidak Ada Antrian Aktif</p>
            <p class="text-xs text-gray-400 font-medium mt-1">Buat booking baru untuk mendapatkan nomor antrian</p>
            <a href="{{ route('pasien.booking.index') }}"
               class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 bg-accent-DEFAULT hover:bg-accent-600 text-white text-xs font-bold rounded-xl transition shadow-sm">
                Buat Booking
            </a>
        </div>
    @endif

    {{-- Quick Links --}}
    <div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Menu Cepat</p>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('pasien.booking.index') }}"
               class="bg-white border border-gray-100 rounded-2xl p-4 flex flex-col items-center gap-2 hover:border-accent-DEFAULT/30 hover:bg-accent-50/50 transition shadow-sm group">
                <div class="w-10 h-10 bg-accent-50 group-hover:bg-accent-100 rounded-xl flex items-center justify-center transition">
                    <svg class="w-5 h-5 text-accent-DEFAULT" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-xs font-bold text-gray-700 group-hover:text-accent-DEFAULT transition text-center">Buat Booking</span>
            </a>
            <a href="{{ route('pasien.riwayat') }}"
               class="bg-white border border-gray-100 rounded-2xl p-4 flex flex-col items-center gap-2 hover:border-primary-DEFAULT/30 hover:bg-primary-50/50 transition shadow-sm group">
                <div class="w-10 h-10 bg-primary-50 group-hover:bg-primary-100 rounded-xl flex items-center justify-center transition">
                    <svg class="w-5 h-5 text-primary-DEFAULT" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-gray-700 group-hover:text-primary-DEFAULT transition text-center">Riwayat</span>
            </a>
        </div>
    </div>

    {{-- Recent Bookings --}}
    @if($bookingTerbaru && $bookingTerbaru->count() > 0)
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Booking Terbaru</p>
            <div class="space-y-2.5">
                @foreach($bookingTerbaru->take(3) as $b)
                    <div class="bg-white border border-gray-100 rounded-xl p-3.5 flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-primary-50 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-4.5 h-4.5 text-primary-DEFAULT" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.375m0 0c.621 0 1.125.504 1.125 1.125V18m0-3a2.625 2.625 0 10-2.625 2.625M6.375 3H3.375c-.621 0-1.125.504-1.125 1.125v3c0 .621.504 1.125 1.125 1.125h3c.621 0 1.125-.504 1.125-1.125v-3C7.5 3.504 6.996 3 6.375 3z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">{{ $b->kode_booking }}</p>
                                <p class="text-xs text-gray-400 font-medium">{{ $b->jadwal->poli->nama_poli ?? '-' }} · {{ $b->created_at?->format('d M') }}</p>
                            </div>
                        </div>
                        @if($b->status === 'pending')
                            <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold">Pending</span>
                        @elseif($b->status === 'checked_in')
                            <span class="px-2 py-1 bg-sky-100 text-sky-700 rounded-full text-[10px] font-bold">Check-in</span>
                        @elseif($b->status === 'selesai')
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold">Selesai</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-bold">Expired</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
