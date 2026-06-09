@extends('layouts.patient')

@if($step === 'index')
    @section('title', 'Pilih Poliklinik')
@elseif($step === 'jadwal')
    @section('title', 'Pilih Jadwal Dokter')
@elseif($step === 'form')
    @section('title', 'Lengkapi Formulir Booking')
@elseif($step === 'show')
    @section('title', 'Detail Antrian Booking')
@endif

@section('content')
@if($step === 'index')
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
@elseif($step === 'jadwal')
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
                        <span class="text-gray-800 font-bold">{{ str_replace(':', '.', substr($jadwal->jam_mulai, 0, 5)) }} â€“ {{ str_replace(':', '.', substr($jadwal->jam_selesai, 0, 5)) }} WIB</span>
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
@elseif($step === 'form')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="space-y-3">
        <a href="{{ route('pasien.booking.jadwal', ['poli_id' => $jadwal->poli_id]) }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 font-bold uppercase tracking-wider">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali Ke Jadwal
        </a>
        <div class="space-y-1">
            <h2 class="font-extrabold text-gray-900 text-lg">Konfirmasi Booking</h2>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Langkah 3 dari 3: Pilih Slot & Lengkapi Formulir</p>
        </div>
    </div>

    <!-- Schedule Summary -->
    <div class="bg-gradient-to-br from-primary-DEFAULT to-primary-600 rounded-2xl p-5 text-white shadow-sm space-y-4">
        <div class="border-b border-white/10 pb-3">
            <span class="text-[10px] font-bold uppercase tracking-wider text-white/70">Poliklinik</span>
            <h3 class="text-base font-extrabold">{{ $jadwal->poli->nama_poli ?? '-' }}</h3>
        </div>

        <div class="space-y-2 text-xs">
            <div>
                <span class="block text-[10px] text-primary-100 uppercase font-bold">Dokter Yang Menangani</span>
                <span class="font-extrabold text-sm">dr. {{ $jadwal->dokter->user->name ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-2 gap-3 pt-2">
                <div>
                    <span class="block text-[10px] text-primary-100 uppercase font-bold">Tanggal Booking</span>
                    <span class="font-extrabold text-sm">{{ $tanggalLabel }}</span>
                </div>
                <div>
                    <span class="block text-[10px] text-primary-100 uppercase font-bold">Jam Praktik</span>
                    <span class="font-extrabold text-sm">{{ str_replace(':', '.', substr($jadwal->jam_mulai, 0, 5)) }} - {{ str_replace(':', '.', substr($jadwal->jam_selesai, 0, 5)) }} WIB</span>
                </div>
            </div>
            <div class="pt-1 flex items-center gap-1.5 text-[10px] text-white/60 font-bold border-t border-white/10">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Durasi per pasien: 30 menit Â· Istirahat: 12.30 â€“ 14.00 WIB
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="p-3.5 bg-red-50 border border-red-100 text-red-800 rounded-xl text-xs font-bold">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Booking Form -->
    <form method="POST" action="{{ route('pasien.booking.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        <input type="hidden" name="slot_waktu" id="selected-slot-input" value="{{ old('slot_waktu') }}">

        <!-- Slot Picker -->
        <div class="bg-white rounded-2xl border border-gray-200/85 p-5 shadow-sm space-y-4">
            @php
                $isBookingToday = ($tanggal === now()->toDateString());
                $nowTime = now()->format('H:i');
                
                // Hitung kuota yang benar-benar bisa dibooking
                $availableCount = 0;
                foreach ($allSlots as $slot) {
                    $isBooked = in_array($slot, $bookedSlots);
                    $isPast   = $isBookingToday && ($slot <= $nowTime);
                    if (!$isBooked && !$isPast) {
                        $availableCount++;
                    }
                }
            @endphp
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-gray-900 text-sm">Pilih Slot Waktu</h3>
                    <p class="text-[10px] text-gray-400 font-semibold mt-0.5">{{ $availableCount }} slot tersedia pada tanggal ini</p>
                </div>
                <span class="text-[10px] font-bold text-accent-DEFAULT bg-accent-50 border border-accent-100 px-2.5 py-1 rounded-full">
                    Wajib Dipilih
                </span>
            </div>

            @if(empty($allSlots))
                <p class="text-center text-xs text-gray-400 font-medium py-4">Tidak ada slot tersedia.</p>
            @else
                <div class="grid grid-cols-3 gap-2" id="slot-grid">
                    @foreach($allSlots as $slot)
                        @php
                            $isBooked = in_array($slot, $bookedSlots);
                            $isPast   = $isBookingToday && ($slot <= $nowTime);
                            $isDisabled = $isBooked || $isPast;
                        @endphp
                        
                        <button type="button"
                            data-slot="{{ $slot }}"
                            @if($isDisabled) disabled @else onclick="selectSlot(this)" @endif
                            class="slot-btn relative flex flex-col items-center justify-center gap-0.5 py-3 px-2 rounded-xl border-2 text-center transition-all
                                @if($isBooked)
                                    border-red-100 bg-red-50/50 text-red-400 cursor-not-allowed opacity-60
                                @elseif($isPast)
                                    border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed opacity-50
                                @elseif(old('slot_waktu') === $slot)
                                    border-accent-DEFAULT bg-accent-50 text-accent-DEFAULT shadow-md shadow-accent-100/50
                                @else
                                    border-gray-200 bg-white text-gray-700 hover:border-accent-DEFAULT/50 hover:bg-accent-50/40
                                @endif">
                            
                            <span class="font-extrabold text-sm leading-none">{{ str_replace(':', '.', $slot) }}</span>
                            
                            @if($isBooked)
                                <span class="text-[8px] font-bold text-red-500 uppercase tracking-wide leading-none mt-1">Booked</span>
                            @elseif($isPast)
                                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-wide leading-none mt-1">Lewat</span>
                            @else
                                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-wide leading-none mt-1">WIB</span>
                            @endif

                            <!-- Checkmark for selected state -->
                            <span class="slot-check absolute top-1.5 right-1.5 w-3.5 h-3.5 bg-accent-DEFAULT rounded-full items-center justify-center {{ old('slot_waktu') === $slot ? 'flex' : 'hidden' }}">
                                <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        </button>
                    @endforeach
                </div>

                <!-- Selected slot display -->
                <div id="slot-selected-display" class="hidden items-center gap-2 p-3 bg-accent-50 border border-accent-100 rounded-xl text-xs font-bold text-accent-DEFAULT">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="slot-selected-text">Slot terpilih: â€”</span>
                </div>
            @endif
        </div>

        <!-- Keluhan -->
        <div class="bg-white rounded-2xl border border-gray-200/85 p-5 shadow-sm space-y-4">
            <div class="space-y-1.5">
                <label for="keluhan" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Keluhan Medis / Gejala <span class="text-red-500">*</span></label>
                <textarea id="keluhan" name="keluhan" rows="4" required placeholder="Tuliskan keluhan atau gejala yang Anda rasakan secara detail agar mempermudah pemeriksaan awal dokter..." class="w-full rounded-xl border border-gray-200 px-4 py-3 text-xs focus:outline-none focus:border-primary-DEFAULT focus:ring-1 focus:ring-primary-DEFAULT transition leading-relaxed">{{ old('keluhan') }}</textarea>
            </div>
        </div>

        <button type="submit" id="submit-btn"
            class="w-full flex items-center justify-center gap-1.5 px-4 py-3 bg-gray-200 text-gray-400 rounded-2xl text-sm font-bold transition shadow-sm cursor-not-allowed"
            disabled>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Pilih slot waktu terlebih dahulu
        </button>
    </form>
</div>


@elseif($step === 'show')
<div class="space-y-6">
    <!-- Header Back to Home -->
    <div class="flex items-center justify-between">
        <a href="{{ route('pasien.dashboard') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 font-bold uppercase tracking-wider">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali Ke Dashboard
        </a>
    </div>

    <!-- Ticket Container -->
    <div id="ticket-card" class="bg-white rounded-3xl border border-gray-200/80 shadow-md overflow-hidden relative">
        <div class="p-6 text-center space-y-4">
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-primary-50 text-primary-DEFAULT border border-primary-100">
                Tiket Antrian Digital
            </span>

            <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ $booking->kode_booking }}</h3>
            <!-- QR Code Frame (rendered directly from Backend as SVG) -->
            <div class="bg-white border border-gray-100 p-4 inline-flex items-center justify-center rounded-2xl mx-auto shadow-sm">
                <div class="w-48 h-48 flex items-center justify-center">
                    {!! QrCode::size(192)->color(15, 76, 117)->generate($booking->kode_booking) !!}
                </div>
            </div>

            <p class="text-xs text-gray-400 font-semibold max-w-xs mx-auto">
                Tunjukkan QR Code ini kepada petugas klinik di meja scan untuk melakukan check-in.
            </p>
        </div>

        <!-- Ticket Separator Line -->
        <div class="relative h-4 flex items-center justify-between">
            <div class="w-3 h-6 rounded-r-full bg-gray-50 border-y border-r border-gray-200/80 -ml-[1px]"></div>
            <div class="flex-1 border-t border-dashed border-gray-200/80 mx-1"></div>
            <div class="w-3 h-6 rounded-l-full bg-gray-50 border-y border-l border-gray-200/80 -mr-[1px]"></div>
        </div>

        <!-- Bottom Half: Details -->
        <div class="p-6 space-y-4 text-xs font-semibold text-gray-500">
            <div class="grid grid-cols-2 gap-y-4 gap-x-2">
                <div>
                    <span class="block text-[10px] text-gray-400 uppercase font-bold">Poliklinik</span>
                    <span class="text-gray-900 font-extrabold text-sm">{{ $booking->jadwal->poli->nama_poli ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-[10px] text-gray-400 uppercase font-bold">Dokter</span>
                    <span class="text-gray-900 font-extrabold text-sm">dr. {{ $booking->jadwal->dokter->user->name ?? '-' }}</span>
                </div>
                @if($booking->slot_waktu)
                <div>
                    <span class="block text-[10px] text-gray-400 uppercase font-bold">Slot Waktu</span>
                    <span class="text-gray-900 font-extrabold text-sm text-accent-DEFAULT">{{ str_replace(':', '.', substr($booking->slot_waktu, 0, 5)) }} WIB</span>
                </div>
                <div>
                    <span class="block text-[10px] text-gray-400 uppercase font-bold">Status Antrian</span>
                @else
                <div>
                    <span class="block text-[10px] text-gray-400 uppercase font-bold">Jam Praktik</span>
                    <span class="text-gray-900 font-extrabold text-sm">{{ str_replace(':', '.', substr($booking->jadwal->jam_mulai, 0, 5)) }} - {{ str_replace(':', '.', substr($booking->jadwal->jam_selesai, 0, 5)) }} WIB</span>
                </div>
                <div>
                    <span class="block text-[10px] text-gray-400 uppercase font-bold">Status Antrian</span>
                @endif
                    @if($booking->status === 'pending')
                        <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 uppercase mt-0.5">Menunggu Scan</span>
                    @elseif($booking->status === 'checked_in')
                        <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-primary-50 text-primary-DEFAULT border border-primary-100 uppercase mt-0.5">Checked In</span>
                    @elseif($booking->status === 'selesai')
                        <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase mt-0.5">Selesai</span>
                    @else
                        <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-50 text-red-600 border border-red-100 uppercase mt-0.5">Expired</span>
                    @endif
                </div>
            </div>

            <div class="border-t border-gray-50 pt-4 text-center">
                <span class="block text-[10px] text-gray-400 uppercase font-bold">Batas Waktu Scan</span>
                <span class="text-red-500 font-extrabold text-sm">Hari ini s/d {{ \Carbon\Carbon::parse($booking->expired_at)->format('H.i') }} WIB</span>
            </div>
        </div>
    </div>

    <!-- Download Button -->
    <button id="download-btn" onclick="downloadTicket()" class="w-full flex items-center justify-center gap-1.5 px-4 py-3 bg-accent-DEFAULT hover:bg-accent-600 text-white rounded-2xl text-xs font-bold transition shadow-sm cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Simpan Tiket ke Perangkat
    </button>
</div>


@endif
@endsection

@push('scripts')
@if($step === 'form')
<script>
    const input   = document.getElementById('selected-slot-input');
    const display = document.getElementById('slot-selected-display');
    const dispTxt = document.getElementById('slot-selected-text');
    const submitBtn = document.getElementById('submit-btn');

    function selectSlot(btn) {
        document.querySelectorAll('.slot-btn').forEach(b => {
            b.classList.remove('border-accent-DEFAULT', 'bg-accent-50', 'text-accent-DEFAULT', 'shadow-md', 'shadow-accent-100/50');
            b.classList.add('border-gray-200', 'bg-white', 'text-gray-700');
            b.querySelector('.slot-check').classList.add('hidden');
            b.querySelector('.slot-check').classList.remove('flex');
        });

        btn.classList.add('border-accent-DEFAULT', 'bg-accent-50', 'text-accent-DEFAULT', 'shadow-md', 'shadow-accent-100/50');
        btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-700');
        btn.querySelector('.slot-check').classList.remove('hidden');
        btn.querySelector('.slot-check').classList.add('flex');

        const slot = btn.dataset.slot;
        input.value = slot;

        display.classList.remove('hidden');
        display.classList.add('flex');
        const slotDisplay = slot.replace(':', '.');
        const endDisplay = addMinutes(slot, 30).replace(':', '.');
        dispTxt.textContent = 'Slot terpilih: ' + slotDisplay + ' WIB (selesai pukul ' + endDisplay + ' WIB)';

        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
        submitBtn.classList.add('bg-accent-DEFAULT', 'hover:bg-accent-600', 'text-white', 'cursor-pointer');
        submitBtn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Booking Slot ${slotDisplay} WIB`;
    }

    function addMinutes(time, mins) {
        const [h, m] = time.split(':').map(Number);
        const total  = h * 60 + m + mins;
        return String(Math.floor(total / 60)).padStart(2,'0') + ':' + String(total % 60).padStart(2,'0');
    }

    const preselected = "{{ old('slot_waktu') }}";
    if (preselected) {
        const target = document.querySelector(`.slot-btn[data-slot="${preselected}"]`);
        if (target) selectSlot(target);
    }
</script>
@elseif($step === 'show')
<script src="https://cdn.jsdelivr.net/npm/html-to-image@1.11.11/dist/html-to-image.min.js"></script>
<script>
    const kodeBooking = "{{ $booking->kode_booking ?? '' }}";

    function downloadTicket() {
        const btn = document.getElementById('download-btn');
        const originalHTML = btn.innerHTML;

        btn.innerHTML = `
            <svg class="animate-spin w-4 h-4 text-white inline-block" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menyiapkan Tiket...
        `;
        btn.disabled = true;

        htmlToImage.toPng(document.getElementById('ticket-card'), {
            quality: 1.0,
            pixelRatio: 3,
            style: { transform: 'scale(1)', transformOrigin: 'top left' }
        })
        .then(function (dataUrl) {
            const link = document.createElement('a');
            link.download = 'tiket-' + kodeBooking + '.png';
            link.href = dataUrl;
            link.click();
        })
        .catch(function (error) {
            console.error('Gagal membuat gambar tiket:', error);
            alert('Gagal menyimpan tiket. Silakan coba lagi.');
        })
        .finally(function () {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        });
    }
</script>
@endif
@endpush

