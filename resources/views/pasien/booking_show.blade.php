@extends('layouts.patient')

@section('title', 'Detail Antrian Booking')

@section('content')
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

<!-- Scripts (html-to-image for high-precision vector & image capturing) -->
<script src="https://cdn.jsdelivr.net/npm/html-to-image@1.11.11/dist/html-to-image.min.js"></script>
<script>
    const kodeBooking = "{{ $booking->kode_booking }}";

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

        const ticket = document.getElementById('ticket-card');

        // Gunakan html-to-image yang memiliki engine rendering asli untuk SVG & CSS
        htmlToImage.toPng(ticket, {
            quality: 1.0,
            pixelRatio: 3, // Kualitas sangat tajam
            style: {
                transform: 'scale(1)',
                transformOrigin: 'top left'
            }
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
@endsection