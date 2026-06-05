@extends('layouts.app')
@section('title', 'Jadwal Praktik')
@section('page-title', 'Jadwal Praktik Dokter')
@section('page-subtitle', 'Kelola jadwal praktek dokter SmartClinic')

@section('content')
<div class="space-y-5">
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm font-semibold">
            <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Cari dokter atau poli..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-primary-DEFAULT focus:ring-1 transition">
        </div>
        <button onclick="toggleModal('addModal', true)"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-DEFAULT to-primary-700 hover:from-primary-700 hover:to-primary-DEFAULT text-white text-sm font-bold rounded-xl transition shadow-md shadow-primary-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Jadwal
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm" id="jadwalTable">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide w-12">No</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Dokter</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Poli</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Hari</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Jam Praktik</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Kuota</th>
                        <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wide w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($jadwals as $index => $jadwal)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-5 py-4 text-gray-400 font-semibold text-xs">{{ $index + 1 }}</td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-gray-900">{{ $jadwal->dokter->user->name ?? '-' }}</p>
                                <p class="text-xs text-gray-400 font-medium">{{ $jadwal->dokter->spesialis ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 font-semibold text-gray-700">{{ $jadwal->poli->nama_poli ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-gray-900 text-sm">
                                    {{ $jadwal->tanggal ? tglID(\Carbon\Carbon::parse($jadwal->tanggal)) : $jadwal->hari }}
                                </p>
                                @if($jadwal->tanggal)
                                    @php $isPast = \Carbon\Carbon::parse($jadwal->tanggal)->isPast() && !\Carbon\Carbon::parse($jadwal->tanggal)->isToday(); @endphp
                                    <span class="text-[10px] font-bold {{ $isPast ? 'text-red-400' : 'text-accent-DEFAULT' }}">
                                        {{ $isPast ? 'Sudah lewat' : 'Akan datang' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-600 font-semibold">
                                {{ substr($jadwal->jam_mulai, 0, 5) }} – {{ substr($jadwal->jam_selesai, 0, 5) }} WIB
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">{{ $jadwal->kuota }} pasien</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick="editJadwal({{ json_encode($jadwal) }})" class="p-2 text-gray-400 hover:text-primary-DEFAULT hover:bg-primary-50 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.jadwal.destroy', $jadwal) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-16 text-center text-gray-400 font-semibold">Belum ada data jadwal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@php
    $iCls = 'w-full bg-gray-50 border border-gray-200 text-sm rounded-xl px-4 py-2.5 focus:border-primary-DEFAULT focus:ring-1 focus:bg-white outline-none transition';
    $hariOptions = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
@endphp

{{-- Modal Tambah --}}
<div id="addModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-200 scale-95 opacity-0 modal-content max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="font-bold text-gray-900">Tambah Jadwal</h3>
            <button onclick="toggleModal('addModal', false)" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('admin.jadwal.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Pilih Dokter</label>
                <select name="dokter_id" required class="{{ $iCls }}">
                    <option value="" disabled selected>-- Pilih Dokter --</option>
                    @foreach($dokters as $d)<option value="{{ $d->id }}">{{ $d->user->name ?? 'Dokter' }} ({{ $d->spesialis }})</option>@endforeach
                </select></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Pilih Poli</label>
                <select name="poli_id" required class="{{ $iCls }}">
                    <option value="" disabled selected>-- Pilih Poli --</option>
                    @foreach($polis as $p)<option value="{{ $p->id }}">{{ $p->nama_poli }}</option>@endforeach
                </select></div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Tanggal Jadwal</label>
                    <input type="date" id="add_tanggal" name="tanggal" required
                           min="{{ now()->toDateString() }}"
                           class="{{ $iCls }}"
                           onchange="updateHariPreview('add_tanggal', 'add_hari_preview')">
                    <div id="add_hari_preview" class="hidden mt-2 flex items-center gap-1.5 px-3 py-2 bg-accent-50 border border-accent-100 rounded-xl">
                        <svg class="w-3.5 h-3.5 text-accent-DEFAULT shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span id="add_hari_text" class="text-xs font-bold text-accent-DEFAULT"></span>
                    </div>
                </div>
                <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Kuota</label>
                    <input type="number" name="kuota" required min="1" value="20" class="{{ $iCls }}"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Jam Mulai</label>
                    <input type="time" name="jam_mulai" required class="{{ $iCls }}"></div>
                <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Jam Selesai</label>
                    <input type="time" name="jam_selesai" required class="{{ $iCls }}"></div>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="toggleModal('addModal', false)" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700 rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-primary-DEFAULT hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-200 scale-95 opacity-0 modal-content max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="font-bold text-gray-900">Edit Jadwal</h3>
            <button onclick="toggleModal('editModal', false)" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Pilih Dokter</label>
                <select id="edit_dokter_id" name="dokter_id" required class="{{ $iCls }}">
                    @foreach($dokters as $d)<option value="{{ $d->id }}">{{ $d->user->name ?? 'Dokter' }} ({{ $d->spesialis }})</option>@endforeach
                </select></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Pilih Poli</label>
                <select id="edit_poli_id" name="poli_id" required class="{{ $iCls }}">
                    @foreach($polis as $p)<option value="{{ $p->id }}">{{ $p->nama_poli }}</option>@endforeach
                </select></div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Tanggal Jadwal</label>
                    <input type="date" id="edit_tanggal" name="tanggal" required
                           min="{{ now()->toDateString() }}"
                           class="{{ $iCls }}"
                           onchange="updateHariPreview('edit_tanggal', 'edit_hari_preview')">
                    <div id="edit_hari_preview" class="hidden mt-2 flex items-center gap-1.5 px-3 py-2 bg-accent-50 border border-accent-100 rounded-xl">
                        <svg class="w-3.5 h-3.5 text-accent-DEFAULT shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span id="edit_hari_text" class="text-xs font-bold text-accent-DEFAULT"></span>
                    </div>
                </div>
                <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Kuota</label>
                    <input type="number" id="edit_kuota" name="kuota" required min="1" class="{{ $iCls }}"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Jam Mulai</label>
                    <input type="time" id="edit_jam_mulai" name="jam_mulai" required class="{{ $iCls }}"></div>
                <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Jam Selesai</label>
                    <input type="time" id="edit_jam_selesai" name="jam_selesai" required class="{{ $iCls }}"></div>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="toggleModal('editModal', false)" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700 rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-primary-DEFAULT hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const daysID   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const monthsID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    function updateHariPreview(inputId, previewId) {
        const val  = document.getElementById(inputId).value;
        const wrap = document.getElementById(previewId);
        const txt  = document.getElementById(previewId.replace('_preview','_text'));
        if (!val) { wrap.classList.add('hidden'); return; }
        const d = new Date(val + 'T00:00:00');
        txt.textContent = daysID[d.getDay()] + ', ' + d.getDate() + ' ' + monthsID[d.getMonth()] + ' ' + d.getFullYear();
        wrap.classList.remove('hidden');
    }

    function toggleModal(id, show) {
        const m = document.getElementById(id), c = m.querySelector('.modal-content');
        if (show) { m.classList.remove('hidden'); setTimeout(() => { c.classList.remove('scale-95','opacity-0'); c.classList.add('scale-100','opacity-100'); }, 10); }
        else { c.classList.remove('scale-100','opacity-100'); c.classList.add('scale-95','opacity-0'); setTimeout(() => m.classList.add('hidden'), 200); }
    }
    function editJadwal(j) {
        document.getElementById('editForm').action = `/admin/jadwal/${j.id}`;
        document.getElementById('edit_dokter_id').value = j.dokter_id;
        document.getElementById('edit_poli_id').value = j.poli_id;
        document.getElementById('edit_tanggal').value = j.tanggal;
        document.getElementById('edit_kuota').value = j.kuota;
        document.getElementById('edit_jam_mulai').value = j.jam_mulai.substring(0, 5);
        document.getElementById('edit_jam_selesai').value = j.jam_selesai.substring(0, 5);
        updateHariPreview('edit_tanggal', 'edit_hari_preview');
        toggleModal('editModal', true);
    }
    function searchTable() {
        const f = document.getElementById('searchInput').value.toUpperCase();
        document.querySelectorAll('#jadwalTable tbody tr').forEach(tr => {
            tr.style.display = (tr.textContent || tr.innerText).toUpperCase().includes(f) ? '' : 'none';
        });
    }
</script>
@endpush
