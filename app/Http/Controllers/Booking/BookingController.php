<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\BookingActionService;
use App\Events\BookingCancelled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for customer booking actions:
 * - Create new bookings (using BookingActionService for pessimistic locking)
 * - View personal bookings segmented by status
 * - Cancel future bookings
 */
class BookingController extends Controller
{
    protected $bookingActionService;

    public function __construct(BookingActionService $bookingActionService)
    {
        $this->bookingActionService = $bookingActionService;
    }

    /**
     * Store a new booking (with pessimistic locking to prevent double-booking).
     *
     * Validates inputs, delegates to BookingActionService for transaction handling,
     * and returns user to their bookings page with success/error message.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'service_id' => 'required|exists:services,id',
            'start_time' => 'required|date|after:now',
        ]);

        $result = $this->bookingActionService->createBooking(
            Auth::id(),
            $validated['business_id'],
            $validated['service_id'],
            $validated['start_time']
        );

        if ($result['success']) {
            return redirect()->route('bookings.my-bookings')->with('status', 'Booking confirmed!');
        } else {
            return redirect()->back()->with('error', $result['error']);
        }
    }

    /**
     * Show customer's own bookings.
     * Segments into three collections for the UI tabs.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $now = now();
        
        $upcomingAppointments = Auth::user()
            ->appointments()
            ->with(['business', 'service'])
            ->where('start_time', '>=', $now)
            ->whereIn('status', ['confirmed', 'arrival'])
            ->orderBy('start_time', 'asc')
            ->get();

        $completedAppointments = Auth::user()
            ->appointments()
            ->with(['business', 'service'])
            ->where('status', 'completed')
            ->orderBy('start_time', 'desc')
            ->get();

        $cancelledAppointments = Auth::user()
            ->appointments()
            ->with(['business', 'service'])
            ->whereIn('status', ['cancelled', 'no_show'])
            ->orderBy('start_time', 'desc')
            ->get();

        return view('bookings.my-bookings', compact('upcomingAppointments', 'completedAppointments', 'cancelledAppointments'));
    }

    /**
     * Cancel a booking.
     *
     * Only the original customer can cancel, and only for future appointments.
     * Triggers a BookingCancelled event for email notification.
     *
     * @param Appointment $appointment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Appointment $appointment)
    {
        // Security check: only the customer who made the booking can cancel it
        if ($appointment->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow cancellation of future bookings
        if ($appointment->start_time < now()) {
            return redirect()->back()->with('error', 'Cannot cancel past appointments.');
        }

        // Update status to cancelled
        $appointment->update(['status' => 'cancelled']);

        // Notify via domain event (listeners handle email delivery)
        event(new BookingCancelled($appointment));

        return redirect()->route('bookings.my-bookings')->with('status', 'Appointment cancelled.');
    }
}
