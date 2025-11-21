<?php

namespace App\Listeners;

use App\Events\AppointmentStatusUpdated;
use App\Mail\AppointmentStatusUpdatedToCustomer;
use App\Mail\AppointmentStatusUpdatedToOwner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendAppointmentStatusUpdateNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(AppointmentStatusUpdated $event): void
    {
        $appointment = $event->appointment->load(['customer', 'business.owner', 'service']);

        // Send email to customer
        Mail::to($appointment->customer->email)
            ->send(new AppointmentStatusUpdatedToCustomer($appointment, $event->oldStatus, $event->newStatus));

        // Send confirmation email to business owner
        Mail::to($appointment->business->owner->email)
            ->send(new AppointmentStatusUpdatedToOwner($appointment, $event->oldStatus, $event->newStatus));
    }
}