<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\BookingCreatedCustomer;
use App\Mail\BookingCreatedOwner;
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
        Mail::to($appointment->customer->email)
            ->send(new BookingCreatedCustomer($appointment));

        // Send email to business owner
        Mail::to($appointment->business->owner->email)
            ->send(new BookingCreatedOwner($appointment));
    }
}
