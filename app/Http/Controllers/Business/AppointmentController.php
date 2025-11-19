<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for business owner's appointment management.
 * View upcoming/past appointments and update status.
 */
class AppointmentController extends Controller
{
    /**
     * Show owner's appointments segmented as upcoming vs past (including completed/cancelled/no_show).
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $upcomingAppointments = $business->appointments()
            ->with(['customer', 'service'])
            ->where('start_time', '>=', now())
            ->whereIn('status', ['confirmed', 'arrival'])
            ->orderBy('start_time', 'asc')
            ->get();

        $pastAppointments = $business->appointments()
            ->with(['customer', 'service'])
            ->where(function($query) {
                $query->where('start_time', '<', now())
                      ->orWhereIn('status', ['completed', 'cancelled', 'no_show']);
            })
            ->orderBy('start_time', 'desc')
            ->get();

        return view('business.appointments', compact('upcomingAppointments', 'pastAppointments'));
    }

    /**
     * Update appointment status for the current owner's business.
     * Accepted statuses: confirmed, arrival, completed, no_show, cancelled
     *
     * @param Request $request
     * @param int $appointmentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $appointmentId)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,arrival,completed,no_show,cancelled',
        ]);

        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $appointment = $business->appointments()->findOrFail($appointmentId);
        $appointment->status = $validated['status'];
        $appointment->save();

        return redirect()->back()->with('status', 'Appointment status updated to ' . ucfirst($validated['status']));
    }
}
