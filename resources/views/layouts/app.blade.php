<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartClinic') — Sistem Klinik Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#0F4C75', 50: '#EEF5FB', 100: '#D5E8F5', 600: '#0d4268', 700: '#0a3a5c' },
                        'primary-DEFAULT': '#0F4C75',
                        'primary-50':  '#EEF5FB',
                        'primary-100': '#D5E8F5',
                        'primary-600': '#0d4268',
                        'primary-700': '#0a3a5c',
                        accent:  { DEFAULT: '#00B4A6', 50: '#E0F7F5', 100: '#B3EDE9', 600: '#009e91' },
                        'accent-DEFAULT': '#00B4A6',
                        'accent-50':  '#E0F7F5',
                        'accent-100': '#B3EDE9',
                        'accent-600': '#009e91',
                    },
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        * { -webkit-font-smoothing: antialiased; }
        .glass { background: rgba(255,255,255,0.9); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        #mobile-menu { transition: all 0.3s cubic-bezier(0.4,0,0.2,1); }
        .nav-link { transition: all 0.15s ease; }
        .burger-open .b1 { transform: translateY(7px) rotate(45deg); }
        .burger-open .b2 { transform: scaleX(0); opacity: 0; }
        .burger-open .b3 { transform: translateY(-7px) rotate(-45deg); }
        .burger-line { transition: all 0.25s cubic-bezier(0.4,0,0.2,1); transform-origin: center; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        @keyframes fadeSlideIn { from { opacity:0; transform: translateY(-6px) scale(0.97); } to { opacity:1; transform: translateY(0) scale(1); } }
        .dropdown-anim { animation: fadeSlideIn 0.18s ease-out; }
        @keyframes slideDown { from { opacity:0; transform: translateY(-8px); } to { opacity:1; transform: translateY(0); } }
        .slide-down { animation: slideDown 0.2s ease-out; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 font-sans antialiased min-h-screen flex flex-col">

@php
    $isAdmin   = auth()->user()->isAdmin();
    $isDokter  = auth()->user()->isDokter();
    $isDash    = request()->routeIs($isAdmin ? 'admin.dashboard*' : 'dokter.dashboard*');
    $isPoli    = request()->routeIs('admin.poli*');
    $isJadwal  = request()->routeIs('admin.jadwal*');
    $isUsers   = request()->routeIs('admin.users*');
    $isScan    = request()->routeIs('admin.scan*');
    $isRiwayat = request()->routeIs('dokter.riwayat*');
    $unread    = auth()->user()->notifikasis()->where('is_read', false)->latest()->get();
@endphp

{{-- ───────────── TOP NAV ───────────── --}}
<header class="glass sticky top-0 z-50 border-b border-gray-200/70 shadow-sm">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">

        {{-- Brand + burger --}}
        <div class="flex items-center gap-3">
            <button id="burger-btn" onclick="toggleMobile()" aria-label="Menu"
                class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 focus:outline-none transition">
                <div id="burger-icon" class="w-5 flex flex-col gap-[5px]">
                    <span class="burger-line b1 h-0.5 w-full bg-gray-700 rounded-full"></span>
                    <span class="burger-line b2 h-0.5 w-full bg-gray-700 rounded-full"></span>
                    <span class="burger-line b3 h-0.5 w-full bg-gray-700 rounded-full"></span>
                </div>
            </button>

            <a href="{{ $isAdmin ? route('admin.dashboard') : route('dokter.dashboard') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-DEFAULT to-primary-700 flex items-center justify-center shadow-md group-hover:shadow-primary-100 transition-shadow">
                    <svg width="18" height="18" viewBox="0 0 32 32" fill="none">
                        <rect x="13" y="4" width="6" height="24" rx="3" fill="white"/>
                        <rect x="4" y="13" width="24" height="6" rx="3" fill="#00B4A6"/>
                    </svg>
                </div>
                <div class="hidden sm:block leading-none">
                    <p class="font-bold text-[15px] text-gray-900 tracking-tight">SmartClinic</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mt-0.5">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
            </a>
        </div>

        {{-- Desktop nav --}}
        <nav class="hidden lg:flex items-center gap-0.5">
            @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}"   class="nav-link px-3.5 py-2 rounded-xl text-sm font-semibold {{ $isDash   ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">Dashboard</a>
                <a href="{{ route('admin.poli.index') }}"  class="nav-link px-3.5 py-2 rounded-xl text-sm font-semibold {{ $isPoli   ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">Poliklinik</a>
                <a href="{{ route('admin.jadwal.index') }}" class="nav-link px-3.5 py-2 rounded-xl text-sm font-semibold {{ $isJadwal ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">Jadwal</a>
                <a href="{{ route('admin.users.index') }}"  class="nav-link px-3.5 py-2 rounded-xl text-sm font-semibold {{ $isUsers  ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">Akun</a>
                <a href="{{ route('admin.scan') }}"         class="nav-link px-3.5 py-2 rounded-xl text-sm font-semibold {{ $isScan   ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">Scan QR</a>
            @elseif($isDokter)
                <a href="{{ route('dokter.dashboard') }}"  class="nav-link px-3.5 py-2 rounded-xl text-sm font-semibold {{ $isDash    ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">Dashboard</a>
                <a href="{{ route('dokter.riwayat') }}"    class="nav-link px-3.5 py-2 rounded-xl text-sm font-semibold {{ $isRiwayat ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">Riwayat Pemeriksaan</a>
            @endif
        </nav>

        {{-- Right actions --}}
        <div class="flex items-center gap-1.5">
            {{-- Notif --}}
            <div class="relative" id="notif-wrap">
                <button onclick="toggleNotif()" class="relative w-10 h-10 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unread->count() > 0)
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    @endif
                </button>
                <div id="notif-drop" class="hidden dropdown-anim absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-100">
                        <span class="font-bold text-sm text-gray-900">Notifikasi</span>
                        @if($unread->count() > 0)
                            <form method="POST" action="{{ route('notifikasi.read-all') }}">@csrf
                                <button class="text-xs text-red-500 hover:text-red-700 font-bold">Hapus semua</button>
                            </form>
                        @endif
                    </div>
                    <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                        @forelse($unread as $notif)
                            <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition group">
                                <span class="mt-1.5 w-2 h-2 rounded-full bg-accent-DEFAULT shrink-0"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-800 font-medium leading-relaxed">{{ $notif->pesan }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5 font-semibold">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                                <form method="POST" action="{{ route('notifikasi.read', $notif) }}">@csrf
                                    <button class="opacity-0 group-hover:opacity-100 p-1 rounded-lg text-gray-300 hover:text-gray-500 hover:bg-gray-100 transition-all">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="py-10 text-center">
                                <p class="text-sm font-semibold text-gray-400">Tidak ada notifikasi baru</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Profile --}}
            <div class="relative" id="profile-wrap">
                <button onclick="toggleProfile()" class="flex items-center gap-2 pl-1.5 pr-3 py-1 rounded-xl hover:bg-gray-100 transition focus:outline-none">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-DEFAULT to-accent-DEFAULT flex items-center justify-center text-white font-bold text-sm shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-bold text-gray-800 truncate max-w-[110px] leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">{{ auth()->user()->role }}</p>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="profile-drop" class="hidden dropdown-anim absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                        <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="p-1.5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-xl transition font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- ───────────── MOBILE MENU OVERLAY ───────────── --}}
<div id="mobile-overlay" onclick="toggleMobile()" class="hidden fixed inset-0 bg-black/40 z-40 lg:hidden"></div>
<div id="mobile-menu" class="hidden fixed top-16 left-0 right-0 bg-white border-b border-gray-200 shadow-xl z-40 lg:hidden slide-down">
    <div class="max-w-screen-xl mx-auto px-4 py-3 space-y-1">
        @if($isAdmin)
            <a href="{{ route('admin.dashboard') }}"    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ $isDash   ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-600 hover:bg-gray-50' }}">Dashboard</a>
            <a href="{{ route('admin.poli.index') }}"   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ $isPoli   ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-600 hover:bg-gray-50' }}">Poliklinik</a>
            <a href="{{ route('admin.jadwal.index') }}"  class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ $isJadwal ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-600 hover:bg-gray-50' }}">Jadwal Praktik</a>
            <a href="{{ route('admin.users.index') }}"   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ $isUsers  ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-600 hover:bg-gray-50' }}">Manajemen Akun</a>
            <a href="{{ route('admin.scan') }}"          class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ $isScan   ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-600 hover:bg-gray-50' }}">Scan QR Check-in</a>
        @elseif($isDokter)
            <a href="{{ route('dokter.dashboard') }}"   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ $isDash    ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-600 hover:bg-gray-50' }}">Dashboard</a>
            <a href="{{ route('dokter.riwayat') }}"     class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ $isRiwayat ? 'bg-primary-50 text-primary-DEFAULT' : 'text-gray-600 hover:bg-gray-50' }}">Riwayat Pemeriksaan</a>
        @endif
        <div class="border-t border-gray-100 pt-2 pb-1">
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50">Keluar / Logout</button>
            </form>
        </div>
    </div>
</div>

{{-- ───────────── MAIN CONTENT ───────────── --}}
<main class="flex-1 w-full max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-7">
    @hasSection('page-title')
        <div class="mb-6">
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">@yield('page-title')</h1>
            @hasSection('page-subtitle')
                <p class="text-sm text-gray-500 mt-0.5 font-medium">@yield('page-subtitle')</p>
            @endif
        </div>
    @endif

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold shadow-sm">
            <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm font-semibold shadow-sm">
            <svg class="w-4 h-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>

{{-- ───────────── FOOTER ───────────── --}}
<footer class="mt-auto bg-white border-t border-gray-100">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs font-semibold text-gray-400">
        <div class="flex items-center gap-2">
            <span class="font-bold text-primary-DEFAULT text-sm">SmartClinic</span>
            <span class="text-gray-200">|</span>
            <span>Sistem Antrian &amp; Rekam Medis Digital</span>
        </div>
        <span>&copy; {{ date('Y') }} SmartClinic. All rights reserved.</span>
    </div>
</footer>

<script>
    let mobileOpen = false;
    function toggleMobile() {
        mobileOpen = !mobileOpen;
        const menu    = document.getElementById('mobile-menu');
        const overlay = document.getElementById('mobile-overlay');
        const icon    = document.getElementById('burger-icon');
        menu.classList.toggle('hidden', !mobileOpen);
        overlay.classList.toggle('hidden', !mobileOpen);
        icon.classList.toggle('burger-open', mobileOpen);
    }

    function toggleNotif() {
        const drop = document.getElementById('notif-drop');
        const prof  = document.getElementById('profile-drop');
        prof.classList.add('hidden');
        drop.classList.toggle('hidden');
        if (!drop.classList.contains('hidden')) {
            drop.classList.remove('dropdown-anim');
            void drop.offsetWidth;
            drop.classList.add('dropdown-anim');
        }
    }

    function toggleProfile() {
        const drop = document.getElementById('profile-drop');
        const notif = document.getElementById('notif-drop');
        notif.classList.add('hidden');
        drop.classList.toggle('hidden');
        if (!drop.classList.contains('hidden')) {
            drop.classList.remove('dropdown-anim');
            void drop.offsetWidth;
            drop.classList.add('dropdown-anim');
        }
    }

    document.addEventListener('click', (e) => {
        const notifWrap   = document.getElementById('notif-wrap');
        const profileWrap = document.getElementById('profile-wrap');
        if (!notifWrap?.contains(e.target))   document.getElementById('notif-drop')?.classList.add('hidden');
        if (!profileWrap?.contains(e.target)) document.getElementById('profile-drop')?.classList.add('hidden');
    });
</script>
@stack('scripts')
</body>
</html>