@extends('layouts.app')
@section('title', 'Manajemen Poli')
@section('page-title', 'Manajemen Poliklinik')
@section('page-subtitle', 'Kelola data poliklinik SmartClinic')

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
            <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Cari poli..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-primary-DEFAULT focus:ring-1 transition">
        </div>
        <button onclick="toggleModal('addModal', true)"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-DEFAULT to-primary-700 hover:from-primary-700 hover:to-primary-DEFAULT text-white text-sm font-bold rounded-xl transition shadow-md shadow-primary-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Poli
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm" id="poliTable">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide w-12">No</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Nama Poliklinik</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Deskripsi</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Jadwal</th>
                        <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wide w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($polis as $index => $poli)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-5 py-4 text-gray-400 font-semibold text-xs">{{ $index + 1 }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
                                        <svg class="w-4.5 h-4.5 text-primary-DEFAULT" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <span class="font-bold text-gray-900">{{ $poli->nama_poli }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-500 font-medium max-w-xs truncate">{{ $poli->deskripsi ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 bg-primary-50 text-primary-DEFAULT rounded-full text-xs font-bold">
                                    {{ $poli->jadwal_praktiks_count ?? 0 }} Jadwal
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick="editPoli({{ json_encode($poli) }})" class="p-2 text-gray-400 hover:text-primary-DEFAULT hover:bg-primary-50 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.poli.destroy', $poli) }}" method="POST" onsubmit="return confirm('Hapus poli ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-16 text-center text-gray-400 font-semibold">Belum ada data poli.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@php $iCls = 'w-full bg-gray-50 border border-gray-200 text-sm rounded-xl px-4 py-2.5 focus:border-primary-DEFAULT focus:ring-1 focus:bg-white outline-none transition'; @endphp

{{-- Modal Tambah --}}
<div id="addModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-200 scale-95 opacity-0 modal-content">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Tambah Poliklinik</h3>
            <button onclick="toggleModal('addModal', false)" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('admin.poli.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Nama Poli</label>
                <input type="text" name="nama_poli" required placeholder="Contoh: Poli Umum" class="{{ $iCls }}"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Deskripsi</label>
                <textarea name="deskripsi" rows="3" placeholder="Deskripsi poli..." class="{{ $iCls }}"></textarea></div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="toggleModal('addModal', false)" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700 rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-primary-DEFAULT hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-200 scale-95 opacity-0 modal-content">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Edit Poliklinik</h3>
            <button onclick="toggleModal('editModal', false)" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Nama Poli</label>
                <input type="text" id="edit_nama_poli" name="nama_poli" required class="{{ $iCls }}"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Deskripsi</label>
                <textarea id="edit_deskripsi" name="deskripsi" rows="3" class="{{ $iCls }}"></textarea></div>
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
    function toggleModal(id, show) {
        const m = document.getElementById(id), c = m.querySelector('.modal-content');
        if (show) { m.classList.remove('hidden'); setTimeout(() => { c.classList.remove('scale-95','opacity-0'); c.classList.add('scale-100','opacity-100'); }, 10); }
        else { c.classList.remove('scale-100','opacity-100'); c.classList.add('scale-95','opacity-0'); setTimeout(() => m.classList.add('hidden'), 200); }
    }
    function editPoli(p) {
        document.getElementById('editForm').action = `/admin/poli/${p.id}`;
        document.getElementById('edit_nama_poli').value = p.nama_poli;
        document.getElementById('edit_deskripsi').value = p.deskripsi || '';
        toggleModal('editModal', true);
    }
    function searchTable() {
        const f = document.getElementById('searchInput').value.toUpperCase();
        document.querySelectorAll('#poliTable tbody tr').forEach(tr => {
            tr.style.display = (tr.textContent || tr.innerText).toUpperCase().includes(f) ? '' : 'none';
        });
    }
</script>
@endpush
