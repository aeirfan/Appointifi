<?php

namespace App\Services;

/**
 * Service for building opening hours JSON structure from form inputs.
 * Centralizes the logic for constructing the opening_hours array used by Location model.
 */
class OpeningHoursBuilder
{
    /**
     * The weekdays supported by the system.
     */
    private const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    /**
     * Build opening_hours JSON from request inputs.
     * 
     * @param array $hoursInput Array of ['day' => ['open' => 'HH:MM', 'close' => 'HH:MM']]
     * @param array $closedFlags Array of ['day' => true/false] indicating explicitly closed days
     * @param array $existingHours Existing opening hours to preserve when inputs are empty
     * @return array The constructed opening_hours array suitable for JSON storage
     */
    public function buildFromRequest(array $hoursInput = [], array $closedFlags = [], array $existingHours = []): array
    {
        $openingHours = [];

        foreach (self::DAYS as $day) {
            $open = $hoursInput[$day]['open'] ?? null;
            $close = $hoursInput[$day]['close'] ?? null;
            $isClosed = isset($closedFlags[$day]) && (bool)$closedFlags[$day];

            if ($isClosed) {
                // Explicitly closed by the user
                $openingHours[$day] = null;
            } elseif (!empty($open) && !empty($close)) {
                // Both times provided -> set/update day
                $openingHours[$day] = ['open' => $open, 'close' => $close];
            } else {
                // No explicit change -> keep previous value if available
                $openingHours[$day] = $existingHours[$day] ?? null;
            }
        }

        return $openingHours;
    }

    /**
     * Build opening_hours JSON for new profile creation.
     * Treats missing times as closed (no preservation of existing values).
     * 
     * @param array $hoursInput Array of ['day' => ['open' => 'HH:MM', 'close' => 'HH:MM']]
     * @param array $closedFlags Array of ['day' => true/false] indicating explicitly closed days
     * @return array The constructed opening_hours array
     */
    public function buildForCreate(array $hoursInput = [], array $closedFlags = []): array
    {
        $openingHours = [];

        foreach (self::DAYS as $day) {
            $open = $hoursInput[$day]['open'] ?? null;
            $close = $hoursInput[$day]['close'] ?? null;
            $isClosed = isset($closedFlags[$day]) && (bool)$closedFlags[$day];

            if ($isClosed) {
                $openingHours[$day] = null;
            } elseif (!empty($open) && !empty($close)) {
                $openingHours[$day] = ['open' => $open, 'close' => $close];
            } else {
                // Treat missing times as closed on create
                $openingHours[$day] = null;
            }
        }

        return $openingHours;
    }
}
