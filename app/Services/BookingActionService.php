<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Service;
use App\Models\Appointment;
use App\Events\BookingCreated;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service handling booking creation with pessimistic locking.
 * Encapsulates transaction logic and double-booking prevention.
 */
class BookingActionService
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    /**
     * Create a new booking with pessimistic locking to prevent double-booking.
     * 
     * @param int $customerId The customer user ID
     * @param int $businessId The business ID
     * @param int $serviceId The service ID
     * @param string $startTime The appointment start time (datetime string)
     * @return array ['success' => bool, 'appointment' => Appointment|null, 'error' => string|null]
     */
    public function createBooking(int $customerId, int $businessId, int $serviceId, string $startTime): array
    {
        $business = Business::findOrFail($businessId);
        $service = Service::findOrFail($serviceId);
        $startTime = Carbon::parse($startTime);
        $endTime = $startTime->copy()->addMinutes($service->duration);

        DB::beginTransaction();
        try {
            // Lock the business row to serialize concurrent booking attempts for this business
            $business = Business::where('id', $business->id)->lockForUpdate()->first();

            // Re-check availability within the transaction to ensure no race conditions
            if (!$this->availabilityService->isSlotAvailable($business, $service, $startTime)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'appointment' => null,
                    'error' => 'Sorry, this time slot is no longer available. Please choose another.',
                ];
            }

            // Create the appointment
            $appointment = Appointment::create([
                'customer_id' => $customerId,
                'business_id' => $business->id,
                'service_id' => $service->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'confirmed',
            ]);

            DB::commit();

            // Notify via domain event (listeners handle email delivery)
            event(new BookingCreated($appointment));

            return [
                'success' => true,
                'appointment' => $appointment,
                'error' => null,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'appointment' => null,
                'error' => 'An error occurred. Please try again.',
            ];
        }
    }
}
