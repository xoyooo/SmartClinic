<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .auth-gradient { background: linear-gradient(135deg, #0F4C75 0%, #1a6da8 50%, #00B4A6 100%); }
        .card-float { animation: floatIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes floatIn { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .input-field { transition: all 0.15s ease; }
        .input-field:focus { box-shadow: 0 0 0 3px rgba(15, 76, 117, 0.1); }
    </style>
</head>
<body class="min-h-screen auth-gradient flex items-center justify-center p-4">
    <div class="w-full max-w-md card-float">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-3 mb-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center border border-white/30 shadow-lg">
                    <svg width="26" height="26" viewBox="0 0 32 32" fill="none">
                        <rect x="13" y="4" width="6" height="24" rx="3" fill="white"/>
                        <rect x="4" y="13" width="24" height="6" rx="3" fill="#00B4A6"/>
                    </svg>
                </div>
                <span class="text-2xl font-extrabold text-white tracking-tight">SmartClinic</span>
            </div>
            <p class="text-white/60 text-sm font-medium">Sistem Antrian &amp; Rekam Medis Digital</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-3xl shadow-2xl shadow-primary-700/20 overflow-hidden">
            @if(session('success'))
                <div class="mx-6 mt-6 p-3 bg-accent-50 border border-accent-100 text-accent-600 rounded-xl text-sm font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mx-6 mt-6 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-0.5 font-medium">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="p-8">
                @yield('content')
            </div>
        </div>

        <p class="text-center text-white/40 text-xs mt-6 font-medium">&copy; {{ date('Y') }} SmartClinic. All rights reserved.</p>
    </div>
</body>
</html>