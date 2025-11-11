<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
// ADD THESE NEW CONTROLLERS
use App\Http\Controllers\BusinessDashboardController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('welcome');
});

// routes/web.php

// After login, this route checks role and redirects to appropriate dashboard
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'owner') {
        return redirect('/business/dashboard');
    }
    return redirect('/customer/dashboard');
})->middleware(['auth'])->name('dashboard');

// Customer Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/customer/dashboard', function () {
        return redirect()->route('bookings.index');
    })->name('customer.dashboard');

    // Browse businesses and book appointments
    Route::get('/businesses', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/businesses/{business}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/businesses/{business}/services/{service}/availability', [BookingController::class, 'showAvailability'])->name('bookings.availability');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    
    // My bookings
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('bookings.my-bookings');
    Route::patch('/bookings/{appointment}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// Business Owner Routes
Route::middleware(['auth', 'owner'])->prefix('business')->group(function () {
    // Business Dashboard
    Route::get('/dashboard', [BusinessDashboardController::class, 'index'])->name('business.dashboard');

    // Business Profile Management
    Route::get('/profile/create', [BusinessDashboardController::class, 'create'])->name('business.profile.create');
    Route::post('/profile', [BusinessDashboardController::class, 'store'])->name('business.profile.store');

    // Location management (edit/update)
    Route::get('/location/edit', [BusinessDashboardController::class, 'editLocation'])->name('business.location.edit');
    Route::patch('/location', [BusinessDashboardController::class, 'updateLocation'])->name('business.location.update');

    // Unified Profile Edit (business/location/hours only)
    Route::get('/profile/edit', [BusinessDashboardController::class, 'editProfile'])->name('business.profile.edit');
    Route::patch('/profile', [BusinessDashboardController::class, 'updateProfile'])->name('business.profile.update');

    // Recurring Blocked Times (no-JS: add/remove via dedicated forms)
    Route::post('/recurring-blocks', [BusinessDashboardController::class, 'storeRecurringBlock'])->name('business.recurring-blocks.store');
    Route::delete('/recurring-blocks/{id}', [BusinessDashboardController::class, 'deleteRecurringBlock'])->name('business.recurring-blocks.delete');

    // Holidays (no-JS: add/remove via dedicated forms)
    Route::post('/holidays', [BusinessDashboardController::class, 'storeHoliday'])->name('business.holidays.store');
    Route::delete('/holidays/{id}', [BusinessDashboardController::class, 'deleteHoliday'])->name('business.holidays.delete');

    // Appointment Management
    Route::get('/appointments', [BusinessDashboardController::class, 'appointments'])->name('business.appointments');
    Route::patch('/appointments/{appointment}/status', [BusinessDashboardController::class, 'updateAppointmentStatus'])->name('business.appointments.update-status');

    // Blocked Times Management
    Route::get('/blocked-times', [BusinessDashboardController::class, 'blockedTimes'])->name('business.blocked-times');
    Route::post('/blocked-times', [BusinessDashboardController::class, 'storeBlockedTime'])->name('business.blocked-times.store');
    Route::delete('/blocked-times/{id}', [BusinessDashboardController::class, 'deleteBlockedTime'])->name('business.blocked-times.delete');

    // Services Management
    Route::resource('services', ServiceController::class)->names([
        'index' => 'business.services.index',
        'create' => 'business.services.create',
        'store' => 'business.services.store',
        'show' => 'business.services.show',
        'edit' => 'business.services.edit',
        'update' => 'business.services.update',
        'destroy' => 'business.services.destroy'
    ]);
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';