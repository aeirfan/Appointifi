<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusUpdatedToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new message instance.
     *
     * @param Appointment $appointment
     * @param string $oldStatus
     * @param string $newStatus
     * @return void
     */
    public function __construct(Appointment $appointment, string $oldStatus, string $newStatus)
    {
        $this->appointment = $appointment->load(['customer', 'business', 'service']);
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Appointment Status Updated - ' . $this->appointment->business->name;
        
        return $this->subject($subject)
                    ->view('emails.appointment-status-updated-customer');
    }
}