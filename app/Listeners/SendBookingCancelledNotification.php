<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingCancelledNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(BookingCancelled $event): void
    {
        $appointment = $event->appointment->load(['customer', 'business.owner', 'service']);

        // Send email to customer
        Mail::send('emails.booking-cancelled-customer', ['appointment' => $appointment], function ($message) use ($appointment) {
            $message->to($appointment->customer->email, $appointment->customer->name)
                    ->subject('Booking Cancelled - ' . $appointment->business->name);
        });

        // Send email to business owner
        Mail::send('emails.booking-cancelled-owner', ['appointment' => $appointment], function ($message) use ($appointment) {
            $message->to($appointment->business->owner->email, $appointment->business->owner->name)
                    ->subject('Booking Cancelled - ' . $appointment->service->name);
        });
    }
}
