@extends('layouts.app')

@section('title', 'Scan QR Check-in')
@section('page-title', 'Scan QR Check-in')
@section('page-subtitle', 'Scan QR Code tiket pasien untuk konfirmasi check-in kedatangan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- QR Camera Scanner -->
        <div class="md:col-span-7 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-gray-900">Kamera Scanner</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Izinkan kamera untuk mulai scan</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="startScanner()" id="btnStart" class="px-3 py-1.5 bg-primary-DEFAULT hover:bg-primary-600 text-white text-xs font-semibold rounded-lg transition">Mulai</button>
                    <button onclick="stopScanner()" id="btnStop" disabled class="px-3 py-1.5 bg-gray-250 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">Stop</button>
                </div>
            </div>

            <!-- Scanner Window -->
            <div class="relative bg-gray-900 flex-1 min-h-[300px] flex items-center justify-center p-4">
                <div id="reader" class="w-full max-w-sm rounded-xl overflow-hidden bg-black/40"></div>
                <div id="scanner-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-white/50 p-6 text-center space-y-3">
                    <svg class="w-16 h-16 animate-pulse text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                        <circle cx="12" cy="13" r="3"/>
                    </svg>
                    <p class="text-sm font-medium">Kamera belum aktif.<br>Klik tombol "Mulai" untuk mengaktifkan pemindaian.</p>
                </div>
            </div>
        </div>

        <!-- Manual Input & Status -->
        <div class="md:col-span-5 flex flex-col gap-6">
            <!-- Manual Form -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                <div>
                    <h2 class="font-bold text-gray-900">Check-in Manual</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Input kode booking jika kamera bermasalah</p>
                </div>
                <form id="manualForm" onsubmit="submitManual(event)" class="flex gap-2">
                    <input type="text" id="kode_booking" required placeholder="Contoh: BK-XXXXXXXX" 
                           class="flex-1 bg-gray-50 border border-gray-200 text-sm rounded-xl px-4 py-2.5 focus:border-primary-DEFAULT focus:ring-1 focus:ring-primary-DEFAULT focus:bg-white outline-none transition uppercase">
                    <button type="submit" class="bg-accent-DEFAULT hover:bg-accent-600 text-white font-semibold text-sm px-4 py-2.5 rounded-xl transition shadow-sm shrink-0">Submit</button>
                </form>
            </div>

            <!-- Scan Result Display -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex-1 flex flex-col justify-center min-h-[220px]">
                <div id="result-empty" class="text-center text-gray-400 py-6">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm">Belum ada hasil check-in.</p>
                    <p class="text-xs text-gray-400 mt-1">Lakukan scan QR Code atau input kode booking secara manual di atas.</p>
                </div>

                <!-- Result Success -->
                <div id="result-success" class="hidden space-y-4 animate-[fadeIn_0.3s_ease-out]">
                    <div class="flex items-center gap-3 p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-emerald-800">Check-in Berhasil!</p>
                            <p id="res-time" class="text-xs text-emerald-600 font-medium">Status diperbarui ke Checked In</p>
                        </div>
                    </div>

                    <div class="border border-gray-150 rounded-xl p-4 bg-gray-50/50 space-y-3">
                        <div class="text-center pb-2 border-b border-gray-200">
                            <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">Nomor Antrian</span>
                            <h1 id="res-antrian" class="text-4xl font-extrabold text-primary-DEFAULT mt-1">-</h1>
                        </div>
                        <div class="grid grid-cols-3 gap-y-2 text-xs">
                            <span class="text-gray-400 font-medium">Pasien</span>
                            <span id="res-pasien" class="col-span-2 text-gray-900 font-bold truncate">-</span>

                            <span class="text-gray-400 font-medium">Poli</span>
                            <span id="res-poli" class="col-span-2 text-gray-800 font-semibold truncate">-</span>

                            <span class="text-gray-400 font-medium">Dokter</span>
                            <span id="res-dokter" class="col-span-2 text-gray-800 font-semibold truncate">-</span>

                            <span class="text-gray-400 font-medium">Kode</span>
                            <span id="res-kode" class="col-span-2 text-primary-DEFAULT font-mono font-bold">-</span>
                        </div>
                    </div>
                </div>

                <!-- Result Error -->
                <div id="result-error" class="hidden p-4 bg-red-50 border border-red-150 text-red-800 rounded-xl space-y-2 animate-[fadeIn_0.3s_ease-out]">
                    <div class="flex items-center gap-2 font-semibold">
                        <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Gagal Check-in
                    </div>
                    <p id="res-err-msg" class="text-xs text-red-650 font-medium leading-relaxed">-</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrcodeScanner = null;

    function startScanner() {
        document.getElementById('scanner-placeholder').classList.add('hidden');
        document.getElementById('btnStart').disabled = true;
        document.getElementById('btnStop').disabled = false;

        html5QrcodeScanner = new Html5Qrcode("reader");
        html5QrcodeScanner.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.error(err);
            showError("Tidak dapat mengakses kamera: " + err);
            stopScanner();
        });
    }

    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                document.getElementById('reader').innerHTML = '';
                document.getElementById('scanner-placeholder').classList.remove('hidden');
                document.getElementById('btnStart').disabled = false;
                document.getElementById('btnStop').disabled = true;
                html5QrcodeScanner = null;
            }).catch(err => {
                console.error(err);
            });
        } else {
            document.getElementById('scanner-placeholder').classList.remove('hidden');
            document.getElementById('btnStart').disabled = false;
            document.getElementById('btnStop').disabled = true;
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanning to prevent multiple hits
        stopScanner();
        // Play success beep sound (optional)
        // Send to backend
        sendCheckin(decodedText);
    }

    function onScanFailure(error) {
        // We can ignore quiet failures of no QR code found in frame
    }

    function submitManual(e) {
        e.preventDefault();
        const codeInput = document.getElementById('kode_booking');
        const code = codeInput.value.trim().toUpperCase();
        if (code) {
            sendCheckin(code);
        }
    }

    function sendCheckin(kode) {
        // Reset results display
        document.getElementById('result-empty').classList.add('hidden');
        document.getElementById('result-success').classList.add('hidden');
        document.getElementById('result-error').classList.add('hidden');

        fetch("{{ route('admin.scan.validate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ kode_booking: kode })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            if (status === 200 && body.success) {
                showSuccess(body.data);
            } else {
                showError(body.message || 'Terjadi kesalahan sistem.');
            }
        })
        .catch(err => {
            console.error(err);
            showError('Koneksi internet bermasalah atau server error.');
        });
    }

    function showSuccess(data) {
        document.getElementById('result-empty').classList.add('hidden');
        document.getElementById('result-error').classList.add('hidden');
        
        document.getElementById('res-antrian').textContent = data.no_antrian;
        document.getElementById('res-pasien').textContent = data.nama_pasien;
        document.getElementById('res-poli').textContent = data.poli;
        document.getElementById('res-dokter').textContent = data.dokter;
        document.getElementById('res-kode').textContent = data.kode;
        
        document.getElementById('result-success').classList.remove('hidden');
        document.getElementById('kode_booking').value = '';
    }

    function showError(msg) {
        document.getElementById('result-empty').classList.add('hidden');
        document.getElementById('result-success').classList.add('hidden');
        
        document.getElementById('res-err-msg').textContent = msg;
        document.getElementById('result-error').classList.remove('hidden');
    }
</script>
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush
