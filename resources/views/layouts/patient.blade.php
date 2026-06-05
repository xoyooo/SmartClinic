<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartClinic')</title>
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255,255,255,0.92); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); }
        .nav-glass { background: rgba(255,255,255,0.96); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        @keyframes fadeSlideIn { from { opacity:0; transform: translateY(-6px) scale(0.97); } to { opacity:1; transform: translateY(0) scale(1); } }
        .drop-anim { animation: fadeSlideIn 0.18s ease-out; }
        @keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .page-enter { animation: fadeUp 0.25s ease-out; }
        .tab-active-indicator {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 3px;
            background: #00B4A6;
            border-radius: 2px 2px 0 0;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 font-sans antialiased">

{{-- Mobile Shell Wrapper --}}
<div class="min-h-screen max-w-md mx-auto bg-white relative shadow-2xl shadow-slate-300/50 flex flex-col" style="min-height: 100dvh;">

    @php
        // Auto-expire past bookings
        \App\Models\Booking::whereIn('status', ['pending', 'checked_in'])
            ->where('expired_at', '<', now())
            ->update(['status' => 'expired']);

        $unreadNotifs  = auth()->user()->notifikasis()->where('is_read', false)->latest()->get();
        $isBeranda     = request()->routeIs('pasien.dashboard*');
        $isBookingNav  = request()->routeIs('pasien.booking.index*', 'pasien.booking.jadwal*', 'pasien.booking.form*');
        $activeBooking = \App\Models\Booking::where('pasien_id', auth()->id())->whereIn('status', ['pending','checked_in'])->latest()->first();
        $isAntrianNav  = request()->routeIs('pasien.booking.show*');
        $isRiwayatNav  = request()->routeIs('pasien.riwayat*', 'pasien.resep*');
    @endphp

    {{-- ───────── TOP HEADER ───────── --}}
    <header class="glass sticky top-0 z-40 border-b border-gray-100 px-4 h-14 flex items-center justify-between shrink-0">
        <a href="{{ route('pasien.dashboard') }}" class="flex items-center gap-2 group">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-DEFAULT to-primary-700 flex items-center justify-center shadow-sm">
                <svg width="14" height="14" viewBox="0 0 32 32" fill="none">
                    <rect x="13" y="4" width="6" height="24" rx="3" fill="white"/>
                    <rect x="4" y="13" width="24" height="6" rx="3" fill="#00B4A6"/>
                </svg>
            </div>
            <span class="font-bold text-sm text-gray-900">SmartClinic</span>
        </a>

        <div class="flex items-center gap-2">
            {{-- Notifications --}}
            <div class="relative" id="notif-wrap">
                <button onclick="toggleNotif()" class="relative w-9 h-9 rounded-xl hover:bg-gray-50 flex items-center justify-center text-gray-500 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unreadNotifs->isNotEmpty())
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    @endif
                </button>
                <div id="notif-drop" class="hidden drop-anim absolute right-0 top-full mt-2 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                    <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <span class="font-bold text-xs text-gray-700">Notifikasi</span>
                    </div>
                    <div class="max-h-64 overflow-y-auto divide-y divide-gray-50">
                        @forelse($unreadNotifs as $notif)
                            <div class="flex items-start gap-2.5 p-3 hover:bg-gray-50 transition">
                                <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-accent-DEFAULT shrink-0"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-700 font-medium leading-relaxed">{{ $notif->pesan }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5 font-semibold">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                                <form method="POST" action="{{ route('notifikasi.read', $notif) }}">@csrf
                                    <button class="text-gray-300 hover:text-gray-500 p-0.5 rounded">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs text-gray-400 font-semibold">Tidak ada notifikasi baru</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Profile Avatar --}}
            <div class="relative" id="profile-wrap">
                <button onclick="toggleProfile()" class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-DEFAULT to-accent-DEFAULT flex items-center justify-center text-white font-bold text-xs shadow-sm focus:outline-none">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </button>
                <div id="profile-drop" class="hidden drop-anim absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                    <div class="px-3.5 py-3 bg-gray-50 border-b border-gray-100">
                        <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-gray-400 truncate mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="p-1.5">
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 hover:bg-red-50 rounded-xl transition font-bold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ───────── MAIN CONTENT ───────── --}}
    <main class="flex-1 overflow-y-auto pb-20 page-enter">
        @if(session('success'))
            <div class="mx-4 mt-4 flex items-center gap-2 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold">
                <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-4 mt-4 flex items-center gap-2 p-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-xs font-bold">
                <svg class="w-4 h-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="p-4">
            @yield('content')
        </div>
    </main>

    {{-- ───────── BOTTOM NAV ───────── --}}
    <nav class="nav-glass fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md border-t border-gray-100 z-40" style="padding-bottom: env(safe-area-inset-bottom);">
        <div class="flex items-stretch h-16">

            {{-- Beranda --}}
            <a href="{{ route('pasien.dashboard') }}" class="flex-1 flex flex-col items-center justify-center gap-0.5 relative transition group {{ $isBeranda ? 'text-accent-DEFAULT' : 'text-gray-400' }}">
                @if($isBeranda)<span class="tab-active-indicator"></span>@endif
                <svg class="w-5 h-5" fill="{{ $isBeranda ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-[10px] font-bold">Beranda</span>
            </a>

            {{-- Booking --}}
            <a href="{{ route('pasien.booking.index') }}" class="flex-1 flex flex-col items-center justify-center gap-0.5 relative transition {{ $isBookingNav ? 'text-accent-DEFAULT' : 'text-gray-400' }}">
                @if($isBookingNav)<span class="tab-active-indicator"></span>@endif
                <svg class="w-5 h-5" fill="{{ $isBookingNav ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                </svg>
                <span class="text-[10px] font-bold">Booking</span>
            </a>

            {{-- Antrian --}}
            @if($activeBooking)
                <a href="{{ route('pasien.booking.show', $activeBooking) }}" class="flex-1 flex flex-col items-center justify-center gap-0.5 relative transition {{ $isAntrianNav ? 'text-accent-DEFAULT' : 'text-gray-400' }}">
                    @if($isAntrianNav)<span class="tab-active-indicator"></span>@endif
                    <div class="relative">
                        <svg class="w-5 h-5" fill="{{ $isAntrianNav ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 w-2 h-2 bg-accent-DEFAULT rounded-full border-2 border-white"></span>
                    </div>
                    <span class="text-[10px] font-bold">Antrian</span>
                </a>
            @else
                <button onclick="alert('Anda tidak memiliki antrian aktif. Lakukan booking terlebih dahulu.')" class="flex-1 flex flex-col items-center justify-center gap-0.5 text-gray-300 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    <span class="text-[10px] font-bold">Antrian</span>
                </button>
            @endif

            {{-- Riwayat --}}
            <a href="{{ route('pasien.riwayat') }}" class="flex-1 flex flex-col items-center justify-center gap-0.5 relative transition {{ $isRiwayatNav ? 'text-accent-DEFAULT' : 'text-gray-400' }}">
                @if($isRiwayatNav)<span class="tab-active-indicator"></span>@endif
                <svg class="w-5 h-5" fill="{{ $isRiwayatNav ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-[10px] font-bold">Riwayat</span>
            </a>

        </div>
    </nav>

</div>{{-- end shell --}}

<script>
    function toggleNotif() {
        const d = document.getElementById('notif-drop');
        document.getElementById('profile-drop').classList.add('hidden');
        d.classList.toggle('hidden');
    }
    function toggleProfile() {
        const d = document.getElementById('profile-drop');
        document.getElementById('notif-drop').classList.add('hidden');
        d.classList.toggle('hidden');
    }
    document.addEventListener('click', e => {
        if (!document.getElementById('notif-wrap')?.contains(e.target))    document.getElementById('notif-drop')?.classList.add('hidden');
        if (!document.getElementById('profile-wrap')?.contains(e.target))  document.getElementById('profile-drop')?.classList.add('hidden');
    });
</script>
@stack('scripts')
</body>
</html>
