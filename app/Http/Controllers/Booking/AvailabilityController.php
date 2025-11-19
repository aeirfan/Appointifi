<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Service;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Controller for viewing available time slots for a service.
 * Customer-facing availability display.
 */
class AvailabilityController extends Controller
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    /**
     * Show available time slots for a service on a selected date.
     * Validates that the service belongs to the given business.
     *
     * @param Request $request expects 'date' (Y-m-d). Defaults to tomorrow when absent
     * @param Business $business
     * @param Service $service
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Business $business, Service $service)
    {
        // Validate that service belongs to business
        if ($service->business_id !== $business->id) {
            abort(404);
        }

        $selectedDate = $request->get('date', now()->addDay()->format('Y-m-d'));
        $date = Carbon::parse($selectedDate);

        // Compute available slots for the given business/service/date
        $slots = $this->availabilityService->getAvailableSlots($business, $service, $date);
        
        // If no slots are available, ask the service for a human-friendly reason (holiday/closed/etc.)
        $unavailabilityReason = null;
        if ($slots->isEmpty()) {
            $unavailabilityReason = $this->availabilityService->getUnavailabilityReason($business, $date);
        }

        return view('bookings.availability', compact('business', 'service', 'date', 'slots', 'unavailabilityReason'));
    }
}