@extends('layouts.patient')

@section('title', 'Riwayat Kunjungan')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="space-y-1">
        <h2 class="font-extrabold text-gray-900 text-lg">Riwayat Pemeriksaan</h2>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Seluruh riwayat booking & pemeriksaan Anda</p>
    </div>

    <!-- Booking History List -->
    <div class="space-y-4">
        @forelse($bookings as $booking)
            <div class="bg-white rounded-2xl border border-gray-200/85 p-5 shadow-sm space-y-3.5">
                <div class="flex items-center justify-between border-b border-gray-50 pb-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">
                        {{ $booking->created_at->format('d M Y, H:i') }} WIB
                    </span>
                    @if($booking->status === 'selesai')
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[9px] font-extrabold uppercase border border-emerald-100">Selesai</span>
                    @elseif($booking->status === 'checked_in')
                        <span class="px-2.5 py-0.5 rounded-full bg-primary-50 text-primary-DEFAULT text-[9px] font-extrabold uppercase border border-primary-100">Checked In</span>
                    @elseif($booking->status === 'pending')
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[9px] font-extrabold uppercase border border-amber-100">Pending</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full bg-gray-50 text-gray-500 text-[9px] font-extrabold uppercase border border-gray-200">Expired</span>
                    @endif
                </div>

                <div class="flex items-start justify-between gap-3 text-xs">
                    <div>
                        <h4 class="font-extrabold text-gray-950 text-sm">{{ $booking->jadwal->poli->nama_poli ?? '-' }}</h4>
                        <p class="text-gray-400 mt-1 font-semibold">Dokter: <span class="text-gray-700 font-bold">dr. {{ $booking->jadwal->dokter->user->name ?? '-' }}</span></p>
                        <p class="text-[10px] text-gray-400 mt-0.5 font-bold uppercase tracking-wider">KODE: {{ $booking->kode_booking }}</p>
                    </div>

                    <!-- Action: View Prescription if selesai -->
                    @if($booking->status === 'selesai' && $booking->pemeriksaan)
                        <a href="{{ route('pasien.resep.show', $booking->pemeriksaan) }}" class="inline-flex items-center gap-1 px-3 py-2 bg-accent-DEFAULT hover:bg-accent-600 text-white rounded-xl font-bold transition shadow-sm shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Lihat Resep
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-200/85 p-8 text-center text-xs text-gray-400">
                Belum ada riwayat booking atau kunjungan terdaftar.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
