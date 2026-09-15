<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KapsterController as AdminKapsterController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Customer / Public Routes
Route::get('/', function () {
    return redirect()->route('booking.index');
});

Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
Route::get('/api/available-slots', [BookingController::class, 'getAvailableSlots'])->name('booking.slots');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/{id}/success', [BookingController::class, 'success'])->name('booking.success');
Route::get('/riwayat', [BookingController::class, 'history'])->name('booking.history');

// Auth default dashboard redirect to admin if admin
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('booking.history');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Panel Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Bookings Management
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::patch('/bookings/{id}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::delete('/bookings/{id}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');

    // Kapsters Management
    Route::resource('kapsters', AdminKapsterController::class)->except(['create', 'show', 'edit']);
    Route::post('kapsters/{id}/schedule', [AdminKapsterController::class, 'updateSchedule'])->name('kapsters.schedule');

    // Services Management
    Route::resource('services', AdminServiceController::class)->except(['create', 'show', 'edit']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
