<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, NotifikasiController};
use App\Http\Controllers\Admin\{DashboardController as AdminDash, PoliController,
    JadwalController, UserController, ScanController};
use App\Http\Controllers\Dokter\{DashboardController as DokterDash, PemeriksaanController};
use App\Http\Controllers\Pasien\{DashboardController as PasienDash,
    BookingController, ResepController};

// ─── AUTH ────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/',        [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login',   [AuthController::class, 'showLogin']);
    Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── ADMIN ───────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDash::class, 'index'])->name('dashboard');

    Route::resource('poli', PoliController::class);
    Route::resource('jadwal', JadwalController::class);
    Route::resource('users', UserController::class);
    Route::put('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status');


    Route::get('/scan', [ScanController::class, 'index'])->name('scan');
    Route::post('/scan/validate', [ScanController::class, 'validate_qr'])->name('scan.validate');
});

// ─── DOKTER ──────────────────────────────────────────────────────────────────
Route::prefix('dokter')->name('dokter.')->middleware(['auth', 'role:dokter'])->group(function () {
    Route::get('/dashboard', [DokterDash::class, 'index'])->name('dashboard');

    Route::get('/periksa/{booking}', [PemeriksaanController::class, 'show'])->name('periksa.show');
    Route::post('/periksa/{booking}', [PemeriksaanController::class, 'store'])->name('periksa.store');
    Route::get('/riwayat', [PemeriksaanController::class, 'riwayat'])->name('riwayat');
    Route::get('/pasien/{user}', [PemeriksaanController::class, 'detailPasien'])->name('pasien.detail');
});

// ─── PASIEN ──────────────────────────────────────────────────────────────────
Route::prefix('pasien')->name('pasien.')->middleware(['auth', 'role:pasien'])->group(function () {
    Route::get('/dashboard', [PasienDash::class, 'index'])->name('dashboard');

    Route::get('/booking',              [BookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/jadwal',       [BookingController::class, 'jadwal'])->name('booking.jadwal');
    Route::get('/booking/form/{jadwal}',[BookingController::class, 'form'])->name('booking.form');
    Route::post('/booking',             [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}',    [BookingController::class, 'show'])->name('booking.show');

    Route::get('/resep',                        [ResepController::class, 'index'])->name('resep.index');
    Route::get('/resep/{pemeriksaan}',          [ResepController::class, 'show'])->name('resep.show');
    Route::get('/resep/{pemeriksaan}/download', [ResepController::class, 'download'])->name('resep.download');
    Route::get('/riwayat',                      [PasienDash::class, 'riwayat'])->name('riwayat');
});

// ─── NOTIFIKASI ───────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/notifikasi/{notifikasi}/read', [NotifikasiController::class, 'markRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllRead'])->name('notifikasi.read-all');
});