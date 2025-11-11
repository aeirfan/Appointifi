<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Service;
use App\Services\AvailabilityService;
use App\Services\SearchService;
use App\Events\BookingCreated;
use App\Events\BookingCancelled;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    protected $availabilityService;
    protected $searchService;

    public function __construct(AvailabilityService $availabilityService, SearchService $searchService)
    {
        $this->availabilityService = $availabilityService;
        $this->searchService = $searchService;
    }

    /**
     * Display a list of all businesses (for customers to browse).
     * With search and geo-proximity filtering.
     */
    public function index(Request $request)
    {
        $query = $request->get('query');
        $location = $request->get('location');
        $sortBy = $request->get('sort_by', 'name');

        // Geocode user's location if provided
        $userCoordinates = null;
        if ($location) {
            $userCoordinates = $this->searchService->geocodeAddress($location);
        }

        // Search businesses
        $businesses = $this->searchService->searchBusinesses(
            $query,
            $userCoordinates['latitude'] ?? null,
            $userCoordinates['longitude'] ?? null,
            $sortBy
        );

        return view('bookings.businesses.index', compact('businesses', 'query', 'location', 'sortBy'));
    }

    /**
     * Show a specific business and its services.
     */
    public function show(Business $business)
    {
        $business->load(['services', 'locations']);
        return view('bookings.businesses.show', compact('business'));
    }

    /**
     * Show available time slots for a service on a selected date.
     */
    public function showAvailability(Request $request, Business $business, Service $service)
    {
        // Validate that service belongs to business
        if ($service->business_id !== $business->id) {
            abort(404);
        }

        $selectedDate = $request->get('date', now()->addDay()->format('Y-m-d'));
        $date = Carbon::parse($selectedDate);

        // Get available slots
        $slots = $this->availabilityService->getAvailableSlots($business, $service, $date);
        
        // Get unavailability reason if no slots available
        $unavailabilityReason = null;
        if ($slots->isEmpty()) {
            $unavailabilityReason = $this->availabilityService->getUnavailabilityReason($business, $date);
        }

        return view('bookings.availability', compact('business', 'service', 'date', 'slots', 'unavailabilityReason'));
    }

    /**
     * Store a new booking (with pessimistic locking to prevent double-booking).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'service_id' => 'required|exists:services,id',
            'start_time' => 'required|date|after:now',
        ]);

        $business = Business::findOrFail($validated['business_id']);
        $service = Service::findOrFail($validated['service_id']);
        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addMinutes($service->duration);

        // *** PESSIMISTIC LOCK: Prevent double-booking ***
        DB::beginTransaction();
        try {
            // Lock the business row to prevent concurrent bookings
            $business = Business::where('id', $business->id)->lockForUpdate()->first();

            // Re-check availability within the transaction
            if (!$this->availabilityService->isSlotAvailable($business, $service, $startTime)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Sorry, this time slot is no longer available. Please choose another.');
            }

            // Create the appointment
            $appointment = Appointment::create([
                'customer_id' => Auth::id(),
                'business_id' => $business->id,
                'service_id' => $service->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'confirmed',
            ]);

            DB::commit();

            // Dispatch event to send emails
            event(new BookingCreated($appointment));

            return redirect()->route('bookings.my-bookings')->with('status', 'Booking confirmed!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    /**
     * Show customer's own bookings.
     */
    public function myBookings()
    {
        $appointments = Auth::user()
            ->appointments()
            ->with(['business', 'service'])
            ->orderBy('start_time', 'desc')
            ->get();

        return view('bookings.my-bookings', compact('appointments'));
    }

    /**
     * Cancel a booking.
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

        // Dispatch event to send emails
        event(new BookingCancelled($appointment));

        return redirect()->route('bookings.my-bookings')->with('status', 'Appointment cancelled.');
    }
}
