@extends('layouts.app')
@section('title', 'Manajemen Akun')
@section('page-title', 'Manajemen Akun')
@section('page-subtitle', 'Kelola akun pengguna SmartClinic')

@section('content')
<div class="space-y-5">
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm font-semibold">
            <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Search + Add --}}
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Cari nama, email, atau role..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-primary-DEFAULT focus:ring-1 focus:ring-primary-50 transition">
        </div>
        <button onclick="toggleModal('addModal', true)"
                style="background: linear-gradient(135deg, #0F4C75 0%, #0a3a5c 100%);"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-white text-sm font-bold rounded-xl transition shadow-lg hover:opacity-90 active:scale-[0.98] whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Akun
        </button>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm" id="userTable">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide w-10">No</th>
                        <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Pengguna</th>
                        <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Email</th>
                        <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">No. HP</th>
                        <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Role</th>
                        <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wide w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <td class="px-4 py-3.5 text-gray-400 font-semibold text-xs">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-extrabold text-sm shrink-0"
                                         style="background: linear-gradient(135deg, #0F4C75, #00B4A6);">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 text-sm leading-tight truncate max-w-[160px]">{{ $user->name }}</p>
                                        <p class="text-[11px] text-gray-400 font-medium mt-0.5">Bergabung {{ $user->created_at?->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-gray-700 font-medium text-sm">{{ $user->email }}</td>
                            <td class="px-4 py-3.5 text-gray-500 font-medium text-sm">{{ $user->no_hp ?? '—' }}</td>
                            <td class="px-4 py-3.5">
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">Admin</span>
                                @elseif($user->role === 'dokter')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-primary-100 text-primary-DEFAULT rounded-lg text-xs font-bold">
                                        Dokter{{ $user->dokter ? ' — ' . $user->dokter->spesialis : '' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold">Pasien</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($user->role !== 'pasien')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold">Aktif</span>
                                @elseif($user->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold">
                                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full inline-block animate-pulse"></span>
                                        Menunggu
                                    </span>
                                @elseif($user->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full inline-block"></span>
                                        Aktif
                                    </span>
                                @elseif($user->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-700 rounded-lg text-xs font-bold">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full inline-block"></span>
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($user->role === 'pasien' && $user->status === 'pending')
                                        <form action="{{ route('admin.users.status', $user) }}" method="POST" class="inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-bold transition">✓ Setujui</button>
                                        </form>
                                        <form action="{{ route('admin.users.status', $user) }}" method="POST" class="inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs font-bold transition">✕ Tolak</button>
                                        </form>
                                    @else
                                        <button onclick="editUser({{ json_encode($user) }})"
                                                class="p-2 text-gray-400 hover:text-primary-DEFAULT hover:bg-primary-50 rounded-lg transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                    @endif
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus akun ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                </div>
                                <p class="font-bold text-gray-500">Belum ada data akun.</p>
                                <p class="text-xs text-gray-400 mt-1 font-medium">Klik "Tambah Akun" untuk menambahkan pengguna baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">{{ $users->links() }}</div>
        @endif
    </div>

</div>

{{-- Modal: Tambah --}}
<div id="addModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-200 scale-95 opacity-0 modal-content">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Tambah Akun Baru</h3>
            <button onclick="toggleModal('addModal', false)" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            @php $iCls = 'w-full bg-gray-50 border border-gray-200 text-sm rounded-xl px-4 py-2.5 focus:border-primary-DEFAULT focus:ring-1 focus:ring-primary-50 focus:bg-white outline-none transition'; @endphp
            <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="dr. John Doe" class="{{ $iCls }}"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Email</label>
                <input type="email" name="email" required placeholder="john@smartclinic.com" class="{{ $iCls }}"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Role</label>
                    <select name="role" required onchange="toggleAddSpesialis(this.value)" class="{{ $iCls }}">
                        <option value="admin">Admin</option>
                        <option value="dokter">Dokter</option>
                    </select></div>
                <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">No. HP</label>
                    <input type="text" name="no_hp" placeholder="0812..." class="{{ $iCls }}"></div>
            </div>
            <div id="add_spesialis_container" class="hidden">
                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Spesialisasi</label>
                <input type="text" id="add_spesialis" name="spesialis" placeholder="Contoh: Dokter Gigi" class="{{ $iCls }}">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Password</label>
                    <input type="password" name="password" required minlength="8" class="{{ $iCls }}"></div>
                <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Konfirmasi</label>
                    <input type="password" name="password_confirmation" required class="{{ $iCls }}"></div>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="toggleModal('addModal', false)" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700 rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-primary-DEFAULT hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Edit --}}
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-200 scale-95 opacity-0 modal-content">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Edit Akun</h3>
            <button onclick="toggleModal('editModal', false)" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            @php $iCls = 'w-full bg-gray-50 border border-gray-200 text-sm rounded-xl px-4 py-2.5 focus:border-primary-DEFAULT focus:ring-1 focus:ring-primary-50 focus:bg-white outline-none transition'; @endphp
            <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Nama Lengkap</label>
                <input type="text" id="edit_name" name="name" required class="{{ $iCls }}"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Email</label>
                <input type="email" id="edit_email" name="email" required class="{{ $iCls }}"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Role</label>
                    <input type="text" id="edit_role_display" disabled class="w-full bg-gray-100 border border-gray-200 text-sm rounded-xl px-4 py-2.5 text-gray-500 cursor-not-allowed outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">No. HP</label>
                    <input type="text" id="edit_no_hp" name="no_hp" class="{{ $iCls }}"></div>
            </div>
            <div id="edit_spesialis_container" class="hidden">
                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Spesialisasi</label>
                <input type="text" id="edit_spesialis" name="spesialis" class="{{ $iCls }}">
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
    function toggleModal(id, show) {
        const m = document.getElementById(id);
        const c = m.querySelector('.modal-content');
        if (show) {
            m.classList.remove('hidden');
            setTimeout(() => { c.classList.remove('scale-95','opacity-0'); c.classList.add('scale-100','opacity-100'); }, 10);
        } else {
            c.classList.remove('scale-100','opacity-100'); c.classList.add('scale-95','opacity-0');
            setTimeout(() => m.classList.add('hidden'), 200);
        }
    }
    function toggleAddSpesialis(role) {
        const el = document.getElementById('add_spesialis_container');
        const inp = document.getElementById('add_spesialis');
        el.classList.toggle('hidden', role !== 'dokter');
        role === 'dokter' ? inp.setAttribute('required','') : inp.removeAttribute('required');
    }
    function editUser(user) {
        document.getElementById('editForm').action = `/admin/users/${user.id}`;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_no_hp').value = user.no_hp || '';
        document.getElementById('edit_role_display').value = user.role.toUpperCase();
        const el = document.getElementById('edit_spesialis_container');
        const inp = document.getElementById('edit_spesialis');
        el.classList.toggle('hidden', user.role !== 'dokter');
        inp.value = user.role === 'dokter' && user.dokter ? user.dokter.spesialis : '';
        toggleModal('editModal', true);
    }
    function searchTable() {
        const f = document.getElementById('searchInput').value.toUpperCase();
        document.querySelectorAll('#userTable tbody tr').forEach(tr => {
            const txt = tr.textContent || tr.innerText;
            tr.style.display = txt.toUpperCase().includes(f) ? '' : 'none';
        });
    }
</script>
@endpush
