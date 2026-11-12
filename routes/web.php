<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Peminjam;
use App\Http\Controllers\Petugas;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/alat', [LandingController::class, 'tools'])->name('landing.tools');
Route::get('/alat/{tool}', [LandingController::class, 'showTool'])->name('landing.tools.show');
Route::get('/panduan', [LandingController::class, 'guide'])->name('landing.guide');
Route::get('/tentang', [LandingController::class, 'about'])->name('landing.about');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi/{notification}/baca', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifikasi/baca-semua', [NotificationController::class, 'markAll'])->name('notifications.read-all');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', Admin\UserController::class);

    Route::resource('tools', Admin\ToolController::class);
    Route::resource('categories', Admin\CategoryController::class)->except('show');

    Route::resource('borrowings', Admin\BorrowingController::class);
    Route::post('borrowings/{borrowing}/approve', [Admin\BorrowingController::class, 'approve'])->name('borrowings.approve');
    Route::post('borrowings/{borrowing}/reject', [Admin\BorrowingController::class, 'reject'])->name('borrowings.reject');
    Route::post('borrowings/{borrowing}/pickup', [Admin\BorrowingController::class, 'pickup'])->name('borrowings.pickup');

    Route::get('returns', [Admin\ReturnController::class, 'index'])->name('returns.index');
    Route::get('returns/create', [Admin\ReturnController::class, 'create'])->name('returns.create');
    Route::post('returns', [Admin\ReturnController::class, 'store'])->name('returns.store');
    Route::get('returns/{return}', [Admin\ReturnController::class, 'show'])->name('returns.show');
    Route::put('returns/{return}', [Admin\ReturnController::class, 'update'])->name('returns.update');
    Route::post('returns/{return}/verify', [Admin\ReturnController::class, 'verify'])->name('returns.verify');
    Route::delete('returns/{return}', [Admin\ReturnController::class, 'destroy'])->name('returns.destroy');

    Route::get('logs', [Admin\ActivityLogController::class, 'index'])->name('logs.index');

    Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| Petugas Routes
|--------------------------------------------------------------------------
*/

Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/', [Petugas\DashboardController::class, 'index'])->name('dashboard');

    Route::get('persetujuan', [Petugas\ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('persetujuan/{borrowing}', [Petugas\ApprovalController::class, 'show'])->name('approvals.show');
    Route::post('persetujuan/{borrowing}/approve', [Petugas\ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('persetujuan/{borrowing}/reject', [Petugas\ApprovalController::class, 'reject'])->name('approvals.reject');

    Route::get('monitoring', [Petugas\MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('monitoring/{borrowing}', [Petugas\MonitoringController::class, 'showBorrowing'])->name('monitoring.show');
    Route::post('monitoring/{borrowing}/pickup', [Petugas\MonitoringController::class, 'pickup'])->name('monitoring.pickup');

    Route::get('pengembalian', [Petugas\MonitoringController::class, 'returns'])->name('returns.index');
    Route::get('pengembalian/{return}', [Petugas\MonitoringController::class, 'showReturn'])->name('returns.show');
    Route::post('pengembalian/{return}/verify', [Petugas\MonitoringController::class, 'verifyReturn'])->name('returns.verify');

    Route::get('peminjaman', [Petugas\BorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('peminjaman/{borrowing}', [Petugas\BorrowingController::class, 'show'])->name('borrowings.show');

    Route::get('laporan', [Petugas\ReportController::class, 'index'])->name('reports.index');
    Route::get('laporan/cetak', [Petugas\ReportController::class, 'print'])->name('reports.print');
    Route::get('laporan/pdf', [Petugas\ReportController::class, 'pdf'])->name('reports.pdf');
    Route::get('laporan/excel', [Petugas\ReportController::class, 'excel'])->name('reports.excel');
});

/*
|--------------------------------------------------------------------------
| Peminjam Routes
|--------------------------------------------------------------------------
*/

Route::prefix('peminjam')->name('peminjam.')->middleware(['auth', 'role:peminjam'])->group(function () {
    Route::get('/', [Peminjam\DashboardController::class, 'index'])->name('dashboard');

    Route::get('alat', [Peminjam\ToolController::class, 'index'])->name('tools.index');
    Route::get('alat/{tool}', [Peminjam\ToolController::class, 'show'])->name('tools.show');

    Route::get('peminjaman', [Peminjam\BorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('peminjaman/ajukan', [Peminjam\BorrowingController::class, 'create'])->name('borrowings.create');
    Route::post('peminjaman', [Peminjam\BorrowingController::class, 'store'])->name('borrowings.store');
    Route::get('peminjaman/riwayat', [Peminjam\BorrowingController::class, 'history'])->name('borrowings.history');
    Route::get('peminjaman/{borrowing}', [Peminjam\BorrowingController::class, 'show'])->name('borrowings.show');

    Route::get('pengembalian', [Peminjam\ReturnController::class, 'index'])->name('returns.index');
    Route::get('pengembalian/ajukan/{borrowing}', [Peminjam\ReturnController::class, 'create'])->name('returns.create');
    Route::post('pengembalian/ajukan/{borrowing}', [Peminjam\ReturnController::class, 'store'])->name('returns.store');
    Route::get('pengembalian/{return}', [Peminjam\ReturnController::class, 'show'])->name('returns.show');
});