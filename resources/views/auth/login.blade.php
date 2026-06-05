@extends('layouts.auth')
@section('title', 'Masuk')
@section('content')
<div class="text-center mb-7">
    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Selamat Datang</h1>
    <p class="text-sm text-gray-500 mt-1 font-medium">Masuk ke akun SmartClinic Anda</p>
</div>

<form method="POST" action="{{ route('login.post') }}" class="space-y-4">
    @csrf
    <div class="space-y-1.5">
        <label class="block text-sm font-bold text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               class="input-field w-full px-4 py-3 rounded-2xl border @error('email') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror text-sm font-medium focus:outline-none focus:border-primary-DEFAULT focus:bg-white transition"
               placeholder="nama@email.com">
        @error('email')<p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="space-y-1.5">
        <label class="block text-sm font-bold text-gray-700">Password</label>
        <div class="relative">
            <input type="password" name="password" id="pass-field" required
                   class="input-field w-full px-4 py-3 pr-12 rounded-2xl border @error('password') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror text-sm font-medium focus:outline-none focus:border-primary-DEFAULT focus:bg-white transition"
                   placeholder="Minimal 6 karakter">
            <button type="button" onclick="togglePass()" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition">
                <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
        @error('password')<p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-2.5">
        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded-lg border-gray-300 text-primary-DEFAULT focus:ring-primary-50 cursor-pointer">
        <label for="remember" class="text-sm text-gray-600 font-medium cursor-pointer">Ingat saya</label>
    </div>

    <button type="submit"
            style="background: linear-gradient(135deg, #0F4C75 0%, #0d4268 100%);"
            class="w-full py-3.5 px-4 text-white font-bold rounded-2xl text-sm mt-2 shadow-lg transition-all hover:opacity-90 hover:shadow-xl active:scale-[0.98]">
        Masuk ke Akun
    </button>
</form>

<div class="relative mt-6 mb-5">
    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
    <div class="relative flex justify-center"><span class="bg-white px-3 text-xs text-gray-400 font-semibold">Belum punya akun?</span></div>
</div>

<a href="{{ route('register') }}"
   class="w-full flex items-center justify-center gap-2 py-3 px-4 border-2 border-gray-200 hover:border-accent-DEFAULT hover:bg-accent-50 text-gray-700 hover:text-accent-DEFAULT font-bold rounded-2xl transition-all text-sm">
    Daftar sebagai Pasien
</a>

<script>
    function togglePass() {
        const f = document.getElementById('pass-field');
        const e = document.getElementById('eye-icon');
        if (f.type === 'password') {
            f.type = 'text';
            e.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
        } else {
            f.type = 'password';
            e.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    }
</script>
@endsection