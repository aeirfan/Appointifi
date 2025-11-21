<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Business\DashboardController as BusinessDashboardController;
use App\Http\Controllers\Business\ProfileController as BusinessProfileController;
use App\Http\Controllers\Business\LocationController;
use App\Http\Controllers\Business\AppointmentController as BusinessAppointmentController;
use App\Http\Controllers\Business\HolidayController;
use App\Http\Controllers\Business\RecurringBlockController;
use App\Http\Controllers\Booking\BusinessSearchController;
use App\Http\Controllers\Booking\AvailabilityController;
use App\Http\Controllers\Booking\BookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Email Testing Routes (Development Only)
if (app()->environment('local')) {
    Route::get('/test-email', function () {
        $appointment = \App\Models\Appointment::with(['customer', 'business', 'service'])->latest()->first();
        if (!$appointment) return 'No appointments found. Please create an appointment first.';
        return new \App\Mail\BookingCreatedCustomer($appointment);
    });

    Route::get('/test-email/send', function () {
        $appointment = \App\Models\Appointment::with(['customer', 'business', 'service'])->latest()->first();
        if (!$appointment) return 'No appointments found. Please create an appointment first.';
        \Illuminate\Support\Facades\Mail::to($appointment->customer->email)->send(new \App\Mail\BookingCreatedCustomer($appointment));
        return 'Email sent! Check Mailpit at http://localhost:8025';
    });
}

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
    Route::get('/businesses', [BusinessSearchController::class, 'index'])->name('bookings.index');
    Route::get('/businesses/{business}', [BusinessSearchController::class, 'show'])->name('bookings.show');
    Route::get('/businesses/{business}/services/{service}/availability', [AvailabilityController::class, 'show'])->name('bookings.availability');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

    // My bookings
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('bookings.my-bookings');
    Route::patch('/bookings/{appointment}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// Business Owner Routes
Route::middleware(['auth', 'owner'])->prefix('business')->group(function () {
    // Business Dashboard
    Route::get('/dashboard', [BusinessDashboardController::class, 'index'])->name('business.dashboard');

    // Business Profile Management
    Route::get('/profile/create', [BusinessProfileController::class, 'create'])->name('business.profile.create');
    Route::post('/profile', [BusinessProfileController::class, 'store'])->name('business.profile.store');

    // Location management (edit/update)
    Route::get('/location/edit', [LocationController::class, 'editLocation'])->name('business.location.edit');
    Route::patch('/location', [LocationController::class, 'updateLocation'])->name('business.location.update');

    // Unified Profile Edit (business/location/hours only)
    Route::get('/profile/edit', [BusinessProfileController::class, 'editProfile'])->name('business.profile.edit');
    Route::patch('/profile', [BusinessProfileController::class, 'updateProfile'])->name('business.profile.update');

    // Recurring Blocked Times (no-JS: add/remove via dedicated forms)
    Route::post('/recurring-blocks', [RecurringBlockController::class, 'store'])->name('business.recurring-blocks.store');
    Route::delete('/recurring-blocks/{id}', [RecurringBlockController::class, 'destroy'])->name('business.recurring-blocks.delete');

    // Holidays (no-JS: add/remove via dedicated forms)
    Route::post('/holidays', [HolidayController::class, 'store'])->name('business.holidays.store');
    Route::delete('/holidays/{id}', [HolidayController::class, 'destroy'])->name('business.holidays.delete');

    // Appointment Management
    Route::get('/appointments', [BusinessAppointmentController::class, 'index'])->name('business.appointments');
    Route::patch('/appointments/{appointment}/status', [BusinessAppointmentController::class, 'updateStatus'])->name('business.appointments.update-status');

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