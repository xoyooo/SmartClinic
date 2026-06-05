@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang kembali, ' . auth()->user()->name . '! Berikut ringkasan hari ini.')

@section('content')
<div class="space-y-6">

    {{-- Pending Approval Alert --}}
    @if($pending_users_count > 0)
        <div class="flex items-center justify-between gap-4 p-4 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-amber-900 text-sm">{{ $pending_users_count }} Akun Menunggu Persetujuan</p>
                    <p class="text-amber-700 text-xs mt-0.5 font-medium">Ada pasien baru yang perlu disetujui sebelum bisa login.</p>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}"
               class="shrink-0 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                Setujui &rarr;
            </a>
        </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        @php
            $stats = [
                ['label' => 'Pasien Hari Ini',  'value' => $total_pasien_hari_ini, 'color' => 'primary', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m10 0H7m5-12a4 4 0 110 8 4 4 0 010-8z"/>'],
                ['label' => 'Total Booking',    'value' => $total_booking,         'color' => 'accent',  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/>'],
                ['label' => 'Total Poli',        'value' => $total_poli,            'color' => 'emerald', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
                ['label' => 'Sudah Check-in',   'value' => $checked_in_count,      'color' => 'sky',     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
            ];
            $colorMap = [
                'primary' => ['bg' => 'bg-primary-50',  'text' => 'text-primary-DEFAULT', 'val' => 'text-primary-DEFAULT'],
                'accent'  => ['bg' => 'bg-accent-50',   'text' => 'text-accent-DEFAULT',  'val' => 'text-accent-DEFAULT'],
                'emerald' => ['bg' => 'bg-emerald-50',  'text' => 'text-emerald-600',     'val' => 'text-emerald-600'],
                'sky'     => ['bg' => 'bg-sky-50',      'text' => 'text-sky-600',         'val' => 'text-sky-600'],
            ];
        @endphp
        @foreach($stats as $stat)
            @php $c = $colorMap[$stat['color']]; @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold text-gray-500">{{ $stat['label'] }}</p>
                    <p class="text-3xl font-extrabold {{ $c['val'] }} mt-1.5">{{ $stat['value'] }}</p>
                </div>
                <div class="w-12 h-12 {{ $c['bg'] }} rounded-2xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">{!! $stat['icon'] !!}</svg>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Table + Quick Actions --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Booking Table --}}
        <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-gray-900">Booking Hari Ini</h2>
                    <p class="text-xs text-gray-500 mt-0.5 font-medium">Daftar booking terbaru hari ini</p>
                </div>
                <span class="px-2.5 py-1 bg-primary-50 text-primary-DEFAULT text-xs font-bold rounded-full">
                    {{ $booking_hari_ini->count() }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 border-b border-gray-100">
                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide">Pasien</th>
                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide">Poli / Dokter</th>
                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($booking_hari_ini as $b)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-5 py-3.5">
                                    <p class="font-bold text-gray-900 text-sm">{{ $b->pasien->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-400 font-semibold">{{ $b->kode_booking }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-gray-700 text-sm">{{ $b->jadwal->poli->nama_poli ?? '-' }}</p>
                                    <p class="text-xs text-gray-400 font-medium">{{ $b->jadwal->dokter->user->name ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($b->status === 'pending')
                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">Pending</span>
                                    @elseif($b->status === 'checked_in')
                                        <span class="px-2.5 py-1 bg-sky-100 text-sky-700 rounded-full text-xs font-bold">Check-in</span>
                                    @elseif($b->status === 'selesai')
                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">Selesai</span>
                                    @else
                                        <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Expired</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-gray-500 text-sm font-medium">{{ $b->created_at?->format('H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm font-semibold">Belum ada booking hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="font-bold text-gray-900">Aksi Cepat</h2>
            <p class="text-xs text-gray-500 mt-0.5 mb-5 font-medium">Navigasi ke menu utama</p>
            <div class="space-y-2.5">
                @php
                    $actions = [
                        ['route' => 'admin.poli.index',   'label' => 'Kelola Poliklinik',  'desc' => 'Tambah & edit data poli',      'color' => 'primary'],
                        ['route' => 'admin.jadwal.index',  'label' => 'Jadwal Praktik',     'desc' => 'Atur jadwal dokter',           'color' => 'accent'],
                        ['route' => 'admin.users.index',   'label' => 'Manajemen Akun',     'desc' => 'Kelola pengguna & pasien',     'color' => 'primary'],
                        ['route' => 'admin.scan',          'label' => 'Scan QR Check-in',   'desc' => 'Validasi kehadiran pasien',    'color' => 'accent'],
                    ];
                @endphp
                @foreach($actions as $a)
                    <a href="{{ route($a['route']) }}"
                       class="flex items-center justify-between p-3.5 rounded-xl border border-gray-100 hover:border-{{ $a['color'] === 'accent' ? 'accent' : 'primary' }}-DEFAULT/30 hover:bg-{{ $a['color'] === 'accent' ? 'accent' : 'primary' }}-50/50 transition group">
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $a['label'] }}</p>
                            <p class="text-xs text-gray-400 font-medium mt-0.5">{{ $a['desc'] }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-{{ $a['color'] === 'accent' ? 'accent-DEFAULT' : 'primary-DEFAULT' }} group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection