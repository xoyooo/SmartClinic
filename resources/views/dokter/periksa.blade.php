@extends('layouts.app')

@section('title', 'Pemeriksaan Pasien')
@section('page-title', 'Pemeriksaan Pasien')
@section('page-subtitle', 'Pemeriksaan medis untuk pasien ' . ($booking->pasien->name ?? '-'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('dokter.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 bg-white text-gray-600 hover:text-gray-900 hover:bg-gray-50 text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Patient Info Card -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
            <h3 class="font-bold text-gray-900 text-lg">Informasi Pasien</h3>
            <span class="px-3 py-1 bg-primary-50 text-primary-DEFAULT rounded-full text-xs font-bold border border-primary-DEFAULT/10">
                Booking: {{ $booking->kode_booking }}
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-sm">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Nama Pasien</p>
                <p class="font-bold text-gray-800 mt-1.5 text-base">{{ $booking->pasien->name ?? '-' }}</p>
                <p class="text-xs text-gray-500 font-semibold mt-0.5">{{ $booking->pasien->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Poliklinik Tujuan</p>
                <p class="font-bold text-gray-800 mt-1.5 text-base">{{ $booking->jadwal->poli->nama_poli ?? '-' }}</p>
                <p class="text-xs text-gray-500 font-semibold mt-0.5">Dokter: {{ \Illuminate\Support\Str::startsWith(strtolower($booking->jadwal->dokter->user->name ?? ''), 'dr') ? ($booking->jadwal->dokter->user->name ?? '-') : 'dr. ' . ($booking->jadwal->dokter->user->name ?? '-') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Keluhan Awal</p>
                <p class="font-medium text-gray-600 mt-1.5 leading-relaxed bg-gray-50 p-2.5 rounded-xl border border-gray-200/50 italic">
                    "{{ $booking->keluhan ?? 'Tidak ada keluhan tertulis' }}"
                </p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-semibold">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Examination Form -->
    <form method="POST" action="{{ route('dokter.periksa.store', $booking) }}" class="space-y-6">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-5">
            <h3 class="font-bold text-gray-900 text-lg border-b border-gray-100 pb-3">Hasil Pemeriksaan</h3>
            
            <div class="grid grid-cols-1 gap-5">
                <!-- Diagnosis -->
                <div class="space-y-1.5">
                    <label for="diagnosis" class="text-sm font-bold text-gray-700">Diagnosis Medis <span class="text-red-500">*</span></label>
                    <textarea id="diagnosis" name="diagnosis" rows="3" placeholder="Masukkan diagnosis lengkap pasien..." required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-primary-DEFAULT focus:ring-1 focus:ring-primary-DEFAULT transition">{{ old('diagnosis') }}</textarea>
                </div>

                <!-- Notes -->
                <div class="space-y-1.5">
                    <label for="catatan" class="text-sm font-bold text-gray-700">Catatan Tambahan (Tindakan/Instruksi)</label>
                    <textarea id="catatan" name="catatan" rows="3" placeholder="Masukkan instruksi khusus atau catatan resep jika ada..." class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-primary-DEFAULT focus:ring-1 focus:ring-primary-DEFAULT transition">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Prescription Card -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                <h3 class="font-bold text-gray-900 text-lg">Resep Obat (Opsional)</h3>
                <button type="button" onclick="addResepRow()" class="inline-flex items-center gap-1 px-3 py-1.5 bg-accent-DEFAULT hover:bg-accent-600 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Obat
                </button>
            </div>

            <!-- Header Row for Prescription -->
            <div class="hidden md:grid grid-cols-12 gap-3 mb-2 px-1 text-xs font-bold text-gray-400 uppercase tracking-wider">
                <div class="col-span-5">Nama Obat</div>
                <div class="col-span-3">Dosis</div>
                <div class="col-span-3">Aturan Pakai</div>
                <div class="col-span-1 text-center">Aksi</div>
            </div>

            <!-- Prescription Items Container -->
            <div id="resep-container" class="space-y-3">
                <!-- If old state exists, populate old items -->
                @if(old('reseps'))
                    @foreach(old('reseps') as $idx => $oldResep)
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-3 md:p-0 rounded-xl bg-gray-50 md:bg-transparent border border-gray-100 md:border-0 relative resep-row">
                            <div class="col-span-5">
                                <label class="block md:hidden text-xs font-bold text-gray-400 mb-1">Nama Obat</label>
                                <input type="text" name="reseps[{{ $idx }}][nama_obat]" value="{{ $oldResep['nama_obat'] }}" placeholder="Contoh: Paracetamol 500mg" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-primary-DEFAULT transition">
                            </div>
                            <div class="col-span-3">
                                <label class="block md:hidden text-xs font-bold text-gray-400 mb-1">Dosis</label>
                                <input type="text" name="reseps[{{ $idx }}][dosis]" value="{{ $oldResep['dosis'] }}" placeholder="Contoh: 3 x 1 Tablet" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-primary-DEFAULT transition">
                            </div>
                            <div class="col-span-3">
                                <label class="block md:hidden text-xs font-bold text-gray-400 mb-1">Aturan Pakai</label>
                                <input type="text" name="reseps[{{ $idx }}][aturan_pakai]" value="{{ $oldResep['aturan_pakai'] }}" placeholder="Contoh: Sesudah Makan" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-primary-DEFAULT transition">
                            </div>
                            <div class="col-span-1 flex items-center justify-center pt-2 md:pt-0">
                                <button type="button" onclick="removeResepRow(this)" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition md:absolute md:right-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Initial Empty Row -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-3 md:p-0 rounded-xl bg-gray-50 md:bg-transparent border border-gray-100 md:border-0 relative resep-row">
                        <div class="col-span-5">
                            <label class="block md:hidden text-xs font-bold text-gray-400 mb-1">Nama Obat</label>
                            <input type="text" name="reseps[0][nama_obat]" placeholder="Contoh: Paracetamol 500mg" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-primary-DEFAULT transition">
                        </div>
                        <div class="col-span-3">
                            <label class="block md:hidden text-xs font-bold text-gray-400 mb-1">Dosis</label>
                            <input type="text" name="reseps[0][dosis]" placeholder="Contoh: 3 x 1 Tablet" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-primary-DEFAULT transition">
                        </div>
                        <div class="col-span-3">
                            <label class="block md:hidden text-xs font-bold text-gray-400 mb-1">Aturan Pakai</label>
                            <input type="text" name="reseps[0][aturan_pakai]" placeholder="Contoh: Sesudah Makan" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-primary-DEFAULT transition">
                        </div>
                        <div class="col-span-1 flex items-center justify-center pt-2 md:pt-0">
                            <button type="button" onclick="removeResepRow(this)" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition md:absolute md:right-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Submission buttons -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dokter.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-sm font-semibold transition shadow-sm">
                Batalkan
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-DEFAULT hover:bg-primary-600 text-white text-sm font-bold shadow-sm transition">
                Simpan & Selesaikan Pemeriksaan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let resepIndex = {{ old('reseps') ? count(old('reseps')) : 1 }};

    function addResepRow() {
        const container = document.getElementById('resep-container');
        const row = document.createElement('div');
        row.className = 'grid grid-cols-1 md:grid-cols-12 gap-3 p-3 md:p-0 rounded-xl bg-gray-50 md:bg-transparent border border-gray-100 md:border-0 relative resep-row';
        row.innerHTML = `
            <div class="col-span-5">
                <label class="block md:hidden text-xs font-bold text-gray-400 mb-1">Nama Obat</label>
                <input type="text" name="reseps[${resepIndex}][nama_obat]" placeholder="Contoh: Paracetamol 500mg" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-primary-DEFAULT transition">
            </div>
            <div class="col-span-3">
                <label class="block md:hidden text-xs font-bold text-gray-400 mb-1">Dosis</label>
                <input type="text" name="reseps[${resepIndex}][dosis]" placeholder="Contoh: 3 x 1 Tablet" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-primary-DEFAULT transition">
            </div>
            <div class="col-span-3">
                <label class="block md:hidden text-xs font-bold text-gray-400 mb-1">Aturan Pakai</label>
                <input type="text" name="reseps[${resepIndex}][aturan_pakai]" placeholder="Contoh: Sesudah Makan" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-primary-DEFAULT transition">
            </div>
            <div class="col-span-1 flex items-center justify-center pt-2 md:pt-0">
                <button type="button" onclick="removeResepRow(this)" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition md:absolute md:right-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        `;
        container.appendChild(row);
        resepIndex++;
    }

    function removeResepRow(button) {
        const rows = document.querySelectorAll('.resep-row');
        // Do not delete the last remaining row, just clear its values instead.
        if (rows.length > 1) {
            button.closest('.resep-row').remove();
        } else {
            const inputs = button.closest('.resep-row').querySelectorAll('input');
            inputs.forEach(input => input.value = '');
        }
    }
</script>
@endpush
