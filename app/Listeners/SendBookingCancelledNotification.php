<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Mail\BookingCancelledCustomer;
use App\Mail\BookingCancelledOwner;
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
        Mail::to($appointment->customer->email)
            ->send(new BookingCancelledCustomer($appointment));

        // Send email to business owner
        Mail::to($appointment->business->owner->email)
            ->send(new BookingCancelledOwner($appointment));
    }
}
