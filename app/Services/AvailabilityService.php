<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Get available time slots for a service on a specific date.
     * 
     * @param Business $business
     * @param Service $service
     * @param Carbon $date
     * @return Collection Array of available start times
     */
    public function getAvailableSlots(Business $business, Service $service, Carbon $date): Collection
    {
        $location = $business->locations()->first();
        
        if (!$location || !$location->opening_hours) {
            return collect([]);
        }

        // Check if this date is a holiday
        $isHoliday = $business->holidays()
            ->whereDate('date', $date->toDateString())
            ->exists();

        if ($isHoliday) {
            return collect([]); // No slots available on holidays
        }

        $dayOfWeek = strtolower($date->format('l')); // 'monday', 'tuesday', etc.
        $dayHours = $location->opening_hours[$dayOfWeek] ?? null;

        // Check if business is closed on this day
        if (!$dayHours || empty($dayHours['open']) || empty($dayHours['close'])) {
            return collect([]);
        }

        // Parse opening and closing times
        $openTime = Carbon::parse($date->format('Y-m-d') . ' ' . $dayHours['open']);
        $closeTime = Carbon::parse($date->format('Y-m-d') . ' ' . $dayHours['close']);

        // Get all booked slots for this business on this date
        $bookedSlots = $business->appointments()
            ->whereDate('start_time', $date)
            ->whereIn('status', ['confirmed', 'arrival'])
            ->orderBy('start_time')
            ->get();

        // Get recurring blocked times for this day of week
        $recurringBlocked = $business->recurringBlockedTimes()
            ->get()
            ->filter(function ($recurring) use ($dayOfWeek) {
                return in_array($dayOfWeek, $recurring->days_of_week);
            });

        // Generate all possible slots
        $availableSlots = collect([]);
        $currentSlot = $openTime->copy();

    while ($currentSlot->copy()->addMinutes($service->duration)->lte($closeTime)) {
            $slotEnd = $currentSlot->copy()->addMinutes($service->duration);

            // Check if this slot conflicts with any booking
            $isAvailable = true;
            foreach ($bookedSlots as $booking) {
                $bookingStart = Carbon::parse($booking->start_time);
                $bookingEnd = Carbon::parse($booking->end_time);

                // Check for overlap: slot starts before booking ends AND slot ends after booking starts
                if ($currentSlot->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            // Check if this slot conflicts with any recurring blocked time
            if ($isAvailable) {
                foreach ($recurringBlocked as $recurring) {
                    // Parse the time-only values and apply to the current date
                    $recurringStart = Carbon::parse($date->format('Y-m-d') . ' ' . $recurring->start_time);
                    $recurringEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $recurring->end_time);

                    if ($currentSlot->lt($recurringEnd) && $slotEnd->gt($recurringStart)) {
                        $isAvailable = false;
                        break;
                    }
                }
            }

            // Only add slots that are in the future (or at least 30 minutes from now)
            $minimumBookingTime = now()->addMinutes(30);
            if ($isAvailable && $currentSlot->gte($minimumBookingTime)) {
                $availableSlots->push([
                    'start_time' => $currentSlot->copy(),
                    'end_time' => $slotEnd->copy(),
                    'formatted_time' => $currentSlot->format('g:i A'),
                ]);
            }

            // Move to next slot by the service duration to prevent overlapping start times
            $currentSlot->addMinutes($service->duration);
        }

        return $availableSlots;
    }

    /**
     * Get the reason why a specific date has no availability
     */
    public function getUnavailabilityReason(Business $business, Carbon $date): ?string
    {
        $location = $business->locations()->first();
        
        if (!$location || !$location->opening_hours) {
            return 'Business hours not set';
        }

        // Check if it's a holiday
        $holiday = $business->holidays()
            ->whereDate('date', $date->toDateString())
            ->first();

        if ($holiday) {
            return $holiday->name ? "Holiday: {$holiday->name}" : 'Holiday';
        }

        // Check if business is closed on this day
        $dayOfWeek = strtolower($date->format('l'));
        $dayHours = $location->opening_hours[$dayOfWeek] ?? null;

        if (!$dayHours || empty($dayHours['open']) || empty($dayHours['close'])) {
            return 'Closed on ' . ucfirst($dayOfWeek) . 's';
        }

        // Check if all slots are blocked by recurring blocks
        $recurringBlocked = $business->recurringBlockedTimes()
            ->get()
            ->filter(function ($recurring) use ($dayOfWeek) {
                return in_array($dayOfWeek, $recurring->days_of_week);
            });

        if ($recurringBlocked->isNotEmpty()) {
            // Check if recurring blocks cover the entire business day
            $openTime = Carbon::parse($date->format('Y-m-d') . ' ' . $dayHours['open']);
            $closeTime = Carbon::parse($date->format('Y-m-d') . ' ' . $dayHours['close']);
            
            $totalBlockedMinutes = 0;
            foreach ($recurringBlocked as $block) {
                $blockStart = Carbon::parse($date->format('Y-m-d') . ' ' . $block->start_time);
                $blockEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $block->end_time);
                
                // Calculate overlap with business hours
                $overlapStart = $blockStart->max($openTime);
                $overlapEnd = $blockEnd->min($closeTime);
                
                if ($overlapStart < $overlapEnd) {
                    $totalBlockedMinutes += $overlapStart->diffInMinutes($overlapEnd);
                }
            }
            
            $totalBusinessMinutes = $openTime->diffInMinutes($closeTime);
            if ($totalBlockedMinutes >= $totalBusinessMinutes * 0.9) { // 90% or more blocked
                $blockTitle = $recurringBlocked->first()->title;
                return $blockTitle ? "Unavailable: {$blockTitle}" : 'Unavailable due to recurring schedule';
            }
        }

        return null; // No specific reason, slots might be individually booked
    }

    /**
     * Check if a specific time slot is available.
     * Used before creating a booking (within a DB transaction).
     */
    public function isSlotAvailable(Business $business, Service $service, Carbon $startTime): bool
    {
        $endTime = $startTime->copy()->addMinutes($service->duration);

        // Check for overlapping appointments
        $conflicts = $business->appointments()
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    // New booking starts before existing ends AND new booking ends after existing starts
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            })
            ->whereIn('status', ['confirmed', 'arrival'])
            ->exists();

        return !$conflicts;
    }
}
