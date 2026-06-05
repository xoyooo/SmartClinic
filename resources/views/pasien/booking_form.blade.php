@extends('layouts.patient')

@section('title', 'Lengkapi Formulir Booking')

@section('content')
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
                    <span class="font-extrabold text-sm">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB</span>
                </div>
            </div>
            <div class="pt-1 flex items-center gap-1.5 text-[10px] text-white/60 font-bold border-t border-white/10">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Durasi per pasien: 30 menit · Istirahat: 12.30 – 14.00 WIB
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
                            
                            <span class="font-extrabold text-sm leading-none">{{ $slot }}</span>
                            
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
                    <span id="slot-selected-text">Slot terpilih: —</span>
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

<script>
    const input   = document.getElementById('selected-slot-input');
    const display = document.getElementById('slot-selected-display');
    const dispTxt = document.getElementById('slot-selected-text');
    const submitBtn = document.getElementById('submit-btn');

    function selectSlot(btn) {
        // Reset all buttons
        document.querySelectorAll('.slot-btn').forEach(b => {
            b.classList.remove('border-accent-DEFAULT', 'bg-accent-50', 'text-accent-DEFAULT', 'shadow-md', 'shadow-accent-100/50');
            b.classList.add('border-gray-200', 'bg-white', 'text-gray-700');
            b.querySelector('.slot-check').classList.add('hidden');
            b.querySelector('.slot-check').classList.remove('flex');
        });

        // Activate clicked button
        btn.classList.add('border-accent-DEFAULT', 'bg-accent-50', 'text-accent-DEFAULT', 'shadow-md', 'shadow-accent-100/50');
        btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-700');
        btn.querySelector('.slot-check').classList.remove('hidden');
        btn.querySelector('.slot-check').classList.add('flex');

        const slot = btn.dataset.slot;
        input.value = slot;

        // Update display
        display.classList.remove('hidden');
        display.classList.add('flex');
        dispTxt.textContent = 'Slot terpilih: ' + slot + ' WIB (selesai pukul ' + addMinutes(slot, 30) + ' WIB)';

        // Enable submit button
        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
        submitBtn.classList.add('bg-accent-DEFAULT', 'hover:bg-accent-600', 'text-white', 'cursor-pointer');
        submitBtn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Booking Slot ${slot} WIB`;
    }

    function addMinutes(time, mins) {
        const [h, m] = time.split(':').map(Number);
        const total  = h * 60 + m + mins;
        return String(Math.floor(total / 60)).padStart(2,'0') + ':' + String(total % 60).padStart(2,'0');
    }

    // Restore from old() value if validation failed
    const preselected = "{{ old('slot_waktu') }}";
    if (preselected) {
        const target = document.querySelector(`.slot-btn[data-slot="${preselected}"]`);
        if (target) {
            selectSlot(target);
        }
    }
</script>
@endsection
