<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Services\GeocodingService;
use App\Services\OpeningHoursBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for managing business location and opening hours.
 * Separate from ProfileController for clearer separation of concerns.
 */
class LocationController extends Controller
{
    protected $geocodingService;
    protected $hoursBuilder;

    public function __construct(GeocodingService $geocodingService, OpeningHoursBuilder $hoursBuilder)
    {
        $this->geocodingService = $geocodingService;
        $this->hoursBuilder = $hoursBuilder;
    }

    /**
     * Show the edit form for the business location (address, city, opening hours).
     * If the business or location doesn't exist, redirect to creation flow.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editLocation()
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $location = $business->locations()->first();

        if (!$location) {
            // No location yet — send them to the create profile form
            return redirect()->route('business.profile.create');
        }

        return view('business.location-edit', compact('location'));
    }

    /**
     * Update the business location and opening hours; re-geocode address.
     * Preserves existing opening_hours when a day's inputs are left blank.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLocation(Request $request)
    {
        // Validate address/city and hours format, but allow hours to be partially empty
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'hours' => ['sometimes', 'array'],
            'hours.*.open' => ['nullable', 'date_format:H:i'],
            'hours.*.close' => ['nullable', 'date_format:H:i'],
        ]);

        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $location = $business->locations()->first();
        if (!$location) {
            // Create new location if missing
            $location = $business->locations()->create([
                'address' => $validated['address'],
                'city' => $validated['city'],
            ]);
        } else {
            $location->address = $validated['address'];
            $location->city = $validated['city'];
        }

        // Build opening_hours JSON while PRESERVING existing values when inputs are left blank
        $newOpeningHours = $this->hoursBuilder->buildFromRequest(
            $request->input('hours', []),
            $request->input('closed', []),
            $location->opening_hours ?? []
        );
        $location->opening_hours = $newOpeningHours;

        // Attempt geocoding (best-effort)
        $coordinates = $this->geocodingService->geocodeAddress(
            $validated['address'],
            $validated['city']
        );

        $location->latitude = $coordinates['latitude'] ?? null;
        $location->longitude = $coordinates['longitude'] ?? null;
        $location->save();

        return redirect()->route('business.dashboard')->with('status', 'Location and opening hours updated successfully!');
    }
}
