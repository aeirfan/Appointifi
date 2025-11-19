<?php

use Illuminate\Support\Facades\Route;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingCreatedCustomer;

/*
|--------------------------------------------------------------------------
| Email Testing Routes
|--------------------------------------------------------------------------
| These routes are for testing email templates locally
| Access them at: http://localhost:8000/test-email
*/

Route::get('/test-email', function () {
    // Get the latest appointment or create a fake one for testing
    $appointment = Appointment::with(['customer', 'business', 'service'])->latest()->first();
    
    if (!$appointment) {
        return 'No appointments found. Please create an appointment first.';
    }
    
    // Preview the email in browser
    return new BookingCreatedCustomer($appointment);
});

Route::get('/test-email/send', function () {
    $appointment = Appointment::with(['customer', 'business', 'service'])->latest()->first();
    
    if (!$appointment) {
        return 'No appointments found. Please create an appointment first.';
    }
    
    // Send the email (will be logged to storage/logs/laravel.log)
    Mail::to($appointment->customer->email)->send(new BookingCreatedCustomer($appointment));
    
    return 'Email sent! Check storage/logs/laravel.log to see the email content.';
});
