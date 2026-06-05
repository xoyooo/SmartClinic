@extends('layouts.app')
@section('title', 'Riwayat Pemeriksaan')
@section('page-title', 'Riwayat Pemeriksaan')
@section('page-subtitle', 'Daftar seluruh pasien yang telah Anda periksa')

@section('content')
<div class="space-y-5">

    {{-- Search + Count --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="searchInput" onkeyup="filterData()" placeholder="Cari nama pasien atau diagnosis..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-primary-DEFAULT focus:ring-1 transition">
        </div>
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wide shrink-0">
            Total: {{ $riwayat->total() }} Pemeriksaan
        </span>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($riwayat->isEmpty())
            <div class="px-5 py-16 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-700">Belum Ada Riwayat</h3>
                <p class="text-sm text-gray-400 mt-1 font-medium">Anda belum menyelesaikan pemeriksaan apapun.</p>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm" id="riwayatTable">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Tanggal</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Pasien</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Poli</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Diagnosis</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wide">Resep</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wide w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($riwayat as $p)
                            <tr class="hover:bg-gray-50/80 transition riwayat-row">
                                <td class="px-5 py-4 text-gray-500 font-semibold text-xs whitespace-nowrap">{{ $p->created_at?->format('d M Y, H:i') }}</td>
                                <td class="px-5 py-4 font-bold patient-name">
                                    <a href="{{ route('dokter.pasien.detail', $p->booking->pasien) }}"
                                       class="text-primary-DEFAULT hover:text-primary-600 hover:underline underline-offset-2 transition">
                                        {{ $p->booking->pasien->name ?? '-' }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-gray-600 font-semibold">{{ $p->booking->jadwal->poli->nama_poli ?? '-' }}</td>
                                <td class="px-5 py-4 text-gray-500 font-medium max-w-xs truncate diagnosis-col">{{ $p->diagnosis }}</td>
                                <td class="px-5 py-4 text-center">
                                    @if($p->reseps->isNotEmpty())
                                        <span class="px-2.5 py-1 bg-primary-50 text-primary-DEFAULT rounded-full text-xs font-bold">{{ $p->reseps->count() }} Obat</span>
                                    @else
                                        <span class="text-gray-300 text-xs font-semibold">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button onclick="openDetail({{ json_encode($p) }})"
                                            class="px-3 py-1.5 border border-gray-200 hover:border-primary-DEFAULT hover:bg-primary-50 text-gray-600 hover:text-primary-DEFAULT rounded-xl text-xs font-bold transition">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden p-4 space-y-3" id="mobileCards">
                @foreach($riwayat as $p)
                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-4 space-y-2 mobile-card">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400 font-bold">{{ $p->created_at?->format('d M Y') }}</span>
                            <span class="text-xs font-bold text-emerald-600">{{ $p->booking->jadwal->poli->nama_poli ?? '-' }}</span>
                        </div>
                        <h4 class="font-bold text-gray-900 patient-name">{{ $p->booking->pasien->name ?? '-' }}</h4>
                        <p class="text-xs text-gray-500 font-medium diagnosis-col">{{ Str::limit($p->diagnosis, 80) }}</p>
                        <div class="flex items-center justify-between pt-1">
                            @if($p->reseps->isNotEmpty())
                                <span class="text-xs font-bold text-primary-DEFAULT">{{ $p->reseps->count() }} Resep Obat</span>
                            @else
                                <span class="text-xs text-gray-300 font-semibold">Tidak ada resep</span>
                            @endif
                            <button onclick="openDetail({{ json_encode($p) }})"
                                    class="px-3 py-1.5 border border-gray-200 hover:bg-gray-100 text-gray-600 rounded-xl text-xs font-bold transition">
                                Detail
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-5 py-4 border-t border-gray-100">{{ $riwayat->links() }}</div>
        @endif
    </div>
</div>

{{-- Detail Modal --}}
<div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Detail Pemeriksaan</h3>
            <button onclick="closeDetail()" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
            <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-xl text-sm">
                <div><p class="text-xs text-gray-400 font-bold uppercase tracking-wide">Nama Pasien</p><p class="font-bold text-gray-900 mt-1" id="d-name"></p></div>
                <div><p class="text-xs text-gray-400 font-bold uppercase tracking-wide">Poliklinik</p><p class="font-bold text-gray-900 mt-1" id="d-poli"></p></div>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wide mb-2">Diagnosis Medis</p>
                <div class="p-4 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-800 whitespace-pre-line leading-relaxed" id="d-diagnosis"></div>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wide mb-2">Catatan Tambahan</p>
                <div class="p-4 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 whitespace-pre-line leading-relaxed" id="d-catatan"></div>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wide mb-2">Resep Obat</p>
                <div id="d-resep-table" class="border border-gray-100 rounded-xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                            <tr>
                                <th class="px-4 py-2.5 text-left">Nama Obat</th>
                                <th class="px-4 py-2.5 text-left">Dosis</th>
                                <th class="px-4 py-2.5 text-left">Aturan Pakai</th>
                            </tr>
                        </thead>
                        <tbody id="d-resep-rows" class="divide-y divide-gray-50 text-gray-700"></tbody>
                    </table>
                </div>
                <p id="d-no-resep" class="hidden text-xs text-gray-400 text-center py-3 italic font-semibold">Tidak ada resep untuk pemeriksaan ini.</p>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
            <button onclick="closeDetail()" class="px-5 py-2.5 border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-bold rounded-xl transition">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterData() {
        const f = document.getElementById('searchInput').value.toUpperCase();
        // Desktop
        document.querySelectorAll('#riwayatTable .riwayat-row').forEach(r => {
            const n = r.querySelector('.patient-name')?.textContent || '';
            const d = r.querySelector('.diagnosis-col')?.textContent || '';
            r.style.display = (n + d).toUpperCase().includes(f) ? '' : 'none';
        });
        // Mobile
        document.querySelectorAll('#mobileCards .mobile-card').forEach(c => {
            const n = c.querySelector('.patient-name')?.textContent || '';
            const d = c.querySelector('.diagnosis-col')?.textContent || '';
            c.style.display = (n + d).toUpperCase().includes(f) ? '' : 'none';
        });
    }

    function openDetail(p) {
        document.getElementById('d-name').textContent     = p.booking?.pasien?.name || '-';
        document.getElementById('d-poli').textContent     = p.booking?.jadwal?.poli?.nama_poli || '-';
        document.getElementById('d-diagnosis').textContent = p.diagnosis || '—';
        document.getElementById('d-catatan').textContent  = p.catatan || '—';

        const tbody  = document.getElementById('d-resep-rows');
        const table  = document.getElementById('d-resep-table');
        const noResep = document.getElementById('d-no-resep');
        tbody.innerHTML = '';

        if (p.reseps && p.reseps.length > 0) {
            table.classList.remove('hidden'); noResep.classList.add('hidden');
            p.reseps.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td class="px-4 py-2.5 font-bold text-gray-900">${r.nama_obat||'-'}</td><td class="px-4 py-2.5 font-semibold text-gray-600">${r.dosis||'-'}</td><td class="px-4 py-2.5 text-gray-500">${r.aturan_pakai||'-'}</td>`;
                tbody.appendChild(tr);
            });
        } else {
            table.classList.add('hidden'); noResep.classList.remove('hidden');
        }

        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetail() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    window.addEventListener('click', e => {
        if (e.target === document.getElementById('detailModal')) closeDetail();
    });
</script>
@endpush
