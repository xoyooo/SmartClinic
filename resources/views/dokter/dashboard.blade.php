@extends('layouts.app')
@section('title', 'Dashboard Dokter')
@section('page-title', 'Dashboard Dokter')
@section('page-subtitle', 'Selamat datang kembali, ' . auth()->user()->name)

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Total Patients Today --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total Pasien Hari Ini</p>
                <p class="text-4xl font-extrabold text-primary-DEFAULT mt-2">{{ $totalPasien }}</p>
                <p class="text-xs text-gray-400 font-medium mt-1">Jumlah pendaftar hari ini</p>
            </div>
            <div class="w-14 h-14 bg-primary-50 rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-7 h-7 text-primary-DEFAULT" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        {{-- Patients Done Today --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Pasien Selesai Hari Ini</p>
                <p class="text-4xl font-extrabold text-accent-DEFAULT mt-2">{{ $totalSelesai }}</p>
                <p class="text-xs text-gray-400 font-medium mt-1">Total pemeriksaan selesai</p>
            </div>
            <div class="w-14 h-14 bg-accent-50 rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-7 h-7 text-accent-DEFAULT" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Patient Queue --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-gray-900">Antrian Pasien</h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Pasien yang terdaftar pada hari ini</p>
            </div>
            <span class="px-3 py-1.5 bg-accent-50 text-accent-DEFAULT rounded-full text-xs font-bold border border-accent-100">
                {{ $pasienHariIni->where('status', 'checked_in')->count() }} Menunggu Periksa
            </span>
        </div>

        @if($pasienHariIni->isEmpty())
            <div class="px-5 py-16 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.375m0 0c.621 0 1.125.504 1.125 1.125V18m0-3a2.625 2.625 0 10-2.625 2.625M20.25 6.375c0 .621-.504 1.125-1.125 1.125H14.25a1.125 1.125 0 01-1.125-1.125V3.375c0-.621.504-1.125 1.125-1.125h4.875c.621 0 1.125.504 1.125 1.125v3z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-700">Antrian Kosong</h3>
                <p class="text-sm text-gray-400 mt-1 font-medium">Tidak ada pasien terdaftar saat ini.</p>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">No. Antrian</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">No. Booking</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Nama Pasien</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Poli</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Keluhan</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Jam Booking</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wide w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($pasienHariIni as $b)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-5 py-4">
                                    <div class="w-8 h-8 rounded-full bg-accent-50 text-accent-DEFAULT flex items-center justify-center font-bold text-xs border border-accent-100">
                                        {{ $b->nomorAntrian() }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-bold text-primary-DEFAULT text-sm">{{ $b->kode_booking }}</span>
                                </td>
                                <td class="px-5 py-4 font-bold text-gray-900">{{ $b->pasien->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-gray-600 font-semibold">{{ $b->jadwal->poli->nama_poli ?? '-' }}</td>
                                <td class="px-5 py-4 text-gray-500 font-medium max-w-xs truncate">{{ $b->keluhan ?? '—' }}</td>
                                <td class="px-5 py-4 text-gray-900 font-bold text-sm">{{ $b->slot_waktu ? $b->slot_waktu . ' WIB' : '-' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Tombol Detail Pasien (selalu ada) --}}
                                        <a href="{{ route('dokter.pasien.detail', $b->pasien) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-bold rounded-xl transition border border-gray-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            Detail
                                        </a>
                                        {{-- Tombol Aksi Utama --}}
                                        @if($b->status === 'checked_in')
                                            <a href="{{ route('dokter.periksa.show', $b) }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-2 bg-accent-DEFAULT hover:bg-accent-600 text-white text-xs font-bold rounded-xl transition shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                                Periksa
                                            </a>
                                        @elseif($b->status === 'selesai' && $b->pemeriksaan)
                                            <a href="{{ route('dokter.riwayat') }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-2 bg-primary-50 hover:bg-primary-100 text-primary-DEFAULT text-xs font-bold rounded-xl transition border border-primary-100">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Resep
                                            </a>
                                        @else
                                            <button disabled
                                               class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-100 text-gray-300 text-xs font-bold rounded-xl border border-gray-200 cursor-not-allowed">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                                Periksa
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden p-4 space-y-3">
                @foreach($pasienHariIni as $b)
                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-accent-50 text-accent-DEFAULT flex items-center justify-center font-bold text-[10px] border border-accent-100">
                                    {{ $b->nomorAntrian() }}
                                </div>
                                <span class="font-bold text-primary-DEFAULT text-sm">{{ $b->kode_booking }}</span>
                            </div>
                            <span class="text-xs font-bold text-gray-500">{{ $b->jadwal->poli->nama_poli ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $b->pasien->name ?? '-' }}</h4>
                                <p class="text-xs text-primary-DEFAULT font-bold mt-1 mb-0.5"><span class="text-gray-400 font-medium">Jam:</span> {{ $b->slot_waktu ? $b->slot_waktu . ' WIB' : '-' }}</p>
                                <p class="text-xs text-gray-500 font-medium">{{ $b->keluhan ?? 'Tidak ada keluhan' }}</p>
                            </div>
                            <a href="{{ route('dokter.pasien.detail', $b->pasien) }}"
                               class="shrink-0 px-3 py-1.5 bg-primary-50 hover:bg-primary-100 text-primary-DEFAULT text-xs font-bold rounded-xl border border-primary-100 transition">
                                Detail
                            </a>
                        </div>
                        <div class="flex items-center gap-2 border-t border-gray-100 pt-2.5 mt-1.5">
                            {{-- Detail Pasien --}}
                            <a href="{{ route('dokter.pasien.detail', $b->pasien) }}"
                               class="flex-1 flex items-center justify-center gap-1 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-bold rounded-xl border border-gray-200 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Detail Pasien
                            </a>
                            {{-- Aksi Utama --}}
                            @if($b->status === 'checked_in')
                                <a href="{{ route('dokter.periksa.show', $b) }}"
                                   class="flex-1 flex items-center justify-center gap-1 py-2 bg-accent-DEFAULT hover:bg-accent-600 text-white text-xs font-bold rounded-xl transition">
                                    Periksa
                                </a>
                            @elseif($b->status === 'selesai' && $b->pemeriksaan)
                                <a href="{{ route('dokter.riwayat') }}"
                                   class="flex-1 flex items-center justify-center gap-1 py-2 bg-primary-50 hover:bg-primary-100 text-primary-DEFAULT text-xs font-bold rounded-xl border border-primary-100 transition">
                                    Lihat Resep
                                </a>
                            @else
                                <button disabled
                                   class="flex-1 py-2 bg-gray-100 text-gray-300 text-xs font-bold rounded-xl border border-gray-200 cursor-not-allowed">
                                    Periksa
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
