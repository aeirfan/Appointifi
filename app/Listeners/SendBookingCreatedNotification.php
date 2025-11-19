<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(BookingCreated $event): void
    {
        $appointment = $event->appointment->load(['customer', 'business.owner', 'service']);

        // Send email to customer
        Mail::send('emails.booking-created-customer', ['appointment' => $appointment], function ($message) use ($appointment) {
            $message->to($appointment->customer->email, $appointment->customer->name)
                    ->subject('Booking Confirmation - ' . $appointment->business->name);
        });

        // Send email to business owner
        Mail::send('emails.booking-created-owner', ['appointment' => $appointment], function ($message) use ($appointment) {
            $message->to($appointment->business->owner->email, $appointment->business->owner->name)
                    ->subject('New Booking Received - ' . $appointment->service->name);
        });
    }
}
