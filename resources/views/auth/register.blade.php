@extends('layouts.auth')
@section('title', 'Daftar Akun')
@section('content')
<div class="text-center mb-7">
    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Buat Akun Baru</h1>
    <p class="text-sm text-gray-500 mt-1 font-medium">Daftar sebagai pasien SmartClinic</p>
</div>

<form method="POST" action="{{ route('register.post') }}" class="space-y-4">
    @csrf

    <div class="space-y-1.5">
        <label class="block text-sm font-bold text-gray-700">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name') }}" required
               class="w-full px-4 py-3 rounded-2xl border @error('name') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror text-sm font-medium focus:outline-none focus:border-primary-DEFAULT focus:bg-white transition"
               placeholder="Nama lengkap Anda">
        @error('name')<p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="space-y-1.5">
        <label class="block text-sm font-bold text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="w-full px-4 py-3 rounded-2xl border @error('email') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror text-sm font-medium focus:outline-none focus:border-primary-DEFAULT focus:bg-white transition"
               placeholder="nama@email.com">
        @error('email')<p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="space-y-1.5">
        <label class="block text-sm font-bold text-gray-700">No. HP <span class="text-gray-400 font-medium">(opsional)</span></label>
        <input type="text" name="no_hp" value="{{ old('no_hp') }}"
               class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-medium focus:outline-none focus:border-primary-DEFAULT focus:bg-white transition"
               placeholder="08xxxxxxxxxx">
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div class="space-y-1.5">
            <label class="block text-sm font-bold text-gray-700">Password</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-3 rounded-2xl border @error('password') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror text-sm font-medium focus:outline-none focus:border-primary-DEFAULT focus:bg-white transition"
                   placeholder="Min. 8 karakter">
            @error('password')<p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="space-y-1.5">
            <label class="block text-sm font-bold text-gray-700">Konfirmasi</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-sm font-medium focus:outline-none focus:border-primary-DEFAULT focus:bg-white transition"
                   placeholder="Ulangi password">
        </div>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-3.5 text-xs text-amber-800 font-medium flex items-start gap-2.5 mt-2">
        <svg class="w-4 h-4 shrink-0 text-amber-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span>Akun Anda perlu <strong>disetujui oleh admin</strong> sebelum bisa login ke sistem.</span>
    </div>

    <button type="submit"
            style="background: linear-gradient(135deg, #0F4C75 0%, #0d4268 100%);"
            class="w-full py-3.5 px-4 text-white font-bold rounded-2xl text-sm mt-1 shadow-lg transition-all hover:opacity-90 active:scale-[0.98]">
        Daftar Sekarang
    </button>
</form>

<div class="relative mt-6 mb-5">
    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
    <div class="relative flex justify-center"><span class="bg-white px-3 text-xs text-gray-400 font-semibold">Sudah punya akun?</span></div>
</div>

<a href="{{ route('login') }}"
   class="w-full flex items-center justify-center gap-2 py-3 px-4 border-2 border-gray-200 hover:border-primary-DEFAULT hover:bg-primary-50 text-gray-700 hover:text-primary-DEFAULT font-bold rounded-2xl transition-all text-sm">
    Masuk di sini
</a>
@endsection