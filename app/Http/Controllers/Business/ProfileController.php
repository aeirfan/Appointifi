<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\GeocodingService;
use App\Services\OpeningHoursBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for business profile creation and editing (name/description only).
 * Location and opening hours managed separately via LocationController.
 */
class ProfileController extends Controller
{
    protected $geocodingService;
    protected $hoursBuilder;

    public function __construct(GeocodingService $geocodingService, OpeningHoursBuilder $hoursBuilder)
    {
        $this->geocodingService = $geocodingService;
        $this->hoursBuilder = $hoursBuilder;
    }

    /**
     * Show the business profile creation form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('business.profile-create');
    }

    /**
     * Persist a new business profile with an initial location and opening hours.
     * Performs best-effort geocoding; if it fails, the profile is still created.
     *
     * Expected inputs:
     * - business_name (string)
     * - description (nullable string)
     * - address, city (strings)
     * - hours[day][open|close] (optional H:i) for each weekday
     * - closed[day] (optional boolean) to mark a day as closed
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'business_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'hours' => ['sometimes', 'array'],
            'hours.*.open' => ['nullable', 'date_format:H:i'],
            'hours.*.close' => ['nullable', 'date_format:H:i'],
            'closed' => ['sometimes', 'array'],
        ]);

        $user = Auth::user();

        // Build the opening_hours JSON object per weekday
        $openingHours = $this->hoursBuilder->buildForCreate(
            $request->input('hours', []),
            $request->input('closed', [])
        );

        // Create the Business
        $business = Business::create([
            'owner_id' => $user->id,
            'name' => $validatedData['business_name'],
            'description' => $validatedData['description'],
        ]);

        // Geocode address to get latitude/longitude (best-effort, non-fatal on errors)
        $coordinates = $this->geocodingService->geocodeAddress(
            $validatedData['address'],
            $validatedData['city']
        );

        // Create the Location and link it to the business, storing the opening_hours JSON
        $location = $business->locations()->create([
            'address' => $validatedData['address'],
            'city' => $validatedData['city'],
            'latitude' => $coordinates['latitude'] ?? null,
            'longitude' => $coordinates['longitude'] ?? null,
            'opening_hours' => $openingHours,
        ]);

        // Provide feedback about geocoding success
        $message = 'Profile created!';
        if (!$coordinates) {
            $message .= ' Note: Unable to geocode your address automatically. Distance search may not work.';
        }
        
        return redirect()->route('business.dashboard')
            ->with('status', $message);
    }

    /**
     * Show unified profile edit page (business details, location, opening hours, holidays, recurring blocks).
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editProfile()
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $location = $business->locations()->first();

        if (!$location) {
            return redirect()->route('business.profile.create');
        }

        // Get upcoming holidays
        $holidays = $business->holidays()
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->get();

        // Get recurring blocked times
        $recurringBlockedTimes = $business->recurringBlockedTimes()
            ->orderBy('start_time', 'asc')
            ->get();

        return view('business.profile-edit', compact('business', 'location', 'holidays', 'recurringBlockedTimes'));
    }

    /**
     * Update unified profile (business details, location, opening hours only).
     * Wrapped in a DB transaction to keep business + location in sync.
     *
     * Expected inputs are similar to store(), including hours/closed arrays.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        // Validate business and location + hours only
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'hours' => ['sometimes', 'array'],
            'hours.*.open' => ['nullable', 'date_format:H:i'],
            'hours.*.close' => ['nullable', 'date_format:H:i'],
            'closed' => ['sometimes', 'array'],
        ]);

        \DB::transaction(function () use ($business, $validated, $request) {
            // Update business details
            $business->name = $validated['business_name'];
            $business->description = $validated['description'] ?? $business->description;
            $business->save();

            // Update location
            $location = $business->locations()->first();
            if ($location) {
                $location->address = $validated['address'];
                $location->city = $validated['city'];

                // Build opening_hours JSON
                $newOpeningHours = $this->hoursBuilder->buildFromRequest(
                    $request->input('hours', []),
                    $request->input('closed', []),
                    $location->opening_hours ?? []
                );
                $location->opening_hours = $newOpeningHours;

                // Re-geocode address
                $coordinates = $this->geocodingService->geocodeAddress(
                    $validated['address'],
                    $validated['city']
                );

                $location->latitude = $coordinates['latitude'] ?? null;
                $location->longitude = $coordinates['longitude'] ?? null;
                $location->save();
            }
        });

        return redirect()->route('business.profile.edit')->with('status', 'Business profile updated successfully!');
    }
}
