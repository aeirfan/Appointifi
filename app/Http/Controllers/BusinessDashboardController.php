<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Location;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class BusinessDashboardController extends Controller
{
    public function create()
    {
        return view('business.profile-create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'business_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'hours' => ['sometimes', 'array'],
            'hours.*.open' => ['nullable', 'date_format:H:i'], // e.g., "09:00"
            'hours.*.close' => ['nullable', 'date_format:H:i'],
            'closed' => ['sometimes', 'array'],
        ]);

        $user = Auth::user();

        // 2. Build the opening_hours JSON object
        $openingHours = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $inputHours = $request->input('hours', []);
        $closedFlags = $request->input('closed', []);
        foreach ($days as $day) {
            $isClosed = isset($closedFlags[$day]) && (bool)$closedFlags[$day];
            $open = $inputHours[$day]['open'] ?? null;
            $close = $inputHours[$day]['close'] ?? null;

            if ($isClosed) {
                $openingHours[$day] = null; // Explicitly closed
            } elseif (!empty($open) && !empty($close)) {
                $openingHours[$day] = ['open' => $open, 'close' => $close];
            } else {
                $openingHours[$day] = null; // Treat missing times as closed on create
            }
        }

        // 3. Create the Business
        $business = Business::create([
            'owner_id' => $user->id,
            'name' => $validatedData['business_name'],
            'description' => $validatedData['description'],
        ]);

        // 4. Geocode address to get latitude/longitude (best-effort)
        $lat = null;
        $lng = null;
        try {
            $query = trim($validatedData['address'] . ', ' . $validatedData['city']);
            $response = Http::withHeaders([
                'User-Agent' => 'appointifi-app/1.0 (contact@localhost)'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'limit' => 1,
            ]);

            if ($response->ok()) {
                $result = $response->json();
                if (is_array($result) && !empty($result)) {
                    $lat = isset($result[0]['lat']) ? (float) $result[0]['lat'] : null;
                    $lng = isset($result[0]['lon']) ? (float) $result[0]['lon'] : null;
                }
            }
        } catch (\Exception $e) {
            // swallow geocoding errors; location will be created without lat/lng
        }

        // 5. Create the Location and link it to the business
        $location = $business->locations()->create([
            'address' => $validatedData['address'],
            'city' => $validatedData['city'],
            'latitude' => $lat,
            'longitude' => $lng,
            'opening_hours' => $openingHours, // Save the JSON
        ]);

        // 6. Send them back to the dashboard with appropriate message
        $message = 'Profile created!';
        if ($lat === null || $lng === null) {
            $message .= ' Note: Unable to geocode your address automatically. Distance search may not work.';
        }
        
        return redirect()->route('business.dashboard')
            ->with('status', $message);
    }

    public function index()
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        // Load the primary location (if any)
        $location = $business->locations()->first();

        return view('business.dashboard', compact('business', 'location'));
    }

    /**
     * Show the edit form for the business location.
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
     * Update the business location and re-geocode address.
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
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $existingHours = $location->opening_hours ?? [];
        $inputHours = $request->input('hours', []);
        $closedFlags = $request->input('closed', []);

        $newOpeningHours = [];
        foreach ($days as $day) {
            $open = $inputHours[$day]['open'] ?? null;
            $close = $inputHours[$day]['close'] ?? null;
            $isClosed = isset($closedFlags[$day]) && (bool)$closedFlags[$day];

            if ($isClosed) {
                // Explicitly closed by the user
                $newOpeningHours[$day] = null;
            } elseif (!empty($open) && !empty($close)) {
                // Both times provided -> set/update day
                $newOpeningHours[$day] = ['open' => $open, 'close' => $close];
            } else {
                // No explicit change -> keep previous value
                $newOpeningHours[$day] = $existingHours[$day] ?? null;
            }
        }
        $location->opening_hours = $newOpeningHours;

        // Attempt geocoding
        $lat = null; $lng = null;
        try {
            $query = trim($validated['address'] . ', ' . $validated['city']);
            $response = Http::withHeaders([
                'User-Agent' => 'appointifi-app/1.0 (contact@localhost)'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'limit' => 1,
            ]);

            if ($response->ok()) {
                $result = $response->json();
                if (is_array($result) && !empty($result)) {
                    $lat = isset($result[0]['lat']) ? (float) $result[0]['lat'] : null;
                    $lng = isset($result[0]['lon']) ? (float) $result[0]['lon'] : null;
                }
            }
        } catch (\Exception $e) {
            // ignore
        }

        $location->latitude = $lat;
        $location->longitude = $lng;
        $location->save();

        return redirect()->route('business.dashboard')->with('status', 'Location and opening hours updated successfully!');
    }

    /**
     * Show owner's appointments (upcoming and past)
     */
    public function appointments()
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
     * Update appointment status
     */
    public function updateAppointmentStatus(Request $request, $appointmentId)
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

    /**
     * Show unified profile edit page (business details, location, opening hours)
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
     * Update unified profile (business details, location, opening hours only)
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
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                $inputHours = $request->input('hours', []);
                $closedFlags = $request->input('closed', []);

                $newOpeningHours = [];
                foreach ($days as $day) {
                    $open = $inputHours[$day]['open'] ?? null;
                    $close = $inputHours[$day]['close'] ?? null;

                    // Closed checkbox overrides any provided times
                    if (isset($closedFlags[$day]) && (bool)$closedFlags[$day]) {
                        $newOpeningHours[$day] = null;
                    } else {
                        if (!empty($open) && !empty($close)) {
                            $newOpeningHours[$day] = ['open' => $open, 'close' => $close];
                        } else {
                            $newOpeningHours[$day] = null; // Closed when times are incomplete/empty
                        }
                    }
                }
                $location->opening_hours = $newOpeningHours;

                // Re-geocode address
                $lat = null; $lng = null;
                try {
                    $query = trim($validated['address'] . ', ' . $validated['city']);
                    $response = \Http::withHeaders([
                        'User-Agent' => 'appointifi-app/1.0 (contact@localhost)'
                    ])->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $query,
                        'format' => 'json',
                        'limit' => 1,
                    ]);

                    if ($response->ok()) {
                        $result = $response->json();
                        if (is_array($result) && !empty($result)) {
                            $lat = isset($result[0]['lat']) ? (float) $result[0]['lat'] : null;
                            $lng = isset($result[0]['lon']) ? (float) $result[0]['lon'] : null;
                        }
                    }
                } catch (\Exception $e) {
                    // ignore
                }

                $location->latitude = $lat;
                $location->longitude = $lng;
                $location->save();
            }
        });

        return redirect()->route('business.profile.edit')->with('status', 'Business profile updated successfully!');
    }

    /**
     * Store a new recurring blocked time (no-JS add)
     */
    public function storeRecurringBlock(Request $request)
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        $business->recurringBlockedTimes()->create([
            'title' => $validated['title'] ?? null,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'days_of_week' => $validated['days_of_week'],
        ]);

        return redirect()->route('business.profile.edit')->with('status', 'Recurring block time added successfully!');
    }

    /**
     * Delete a recurring blocked time (no-JS delete)
     */
    public function deleteRecurringBlock($id)
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $business->recurringBlockedTimes()->where('id', $id)->delete();

        return redirect()->route('business.profile.edit')->with('status', 'Recurring block time removed successfully!');
    }

    /**
     * Store a new holiday (no-JS add)
     */
    public function storeHoliday(Request $request)
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'name' => 'nullable|string|max:255',
        ]);

        $business->holidays()->create([
            'date' => $validated['date'],
            'name' => $validated['name'] ?? null,
        ]);

        return redirect()->route('business.profile.edit')->with('status', 'Holiday added successfully!');
    }

    /**
     * Delete a holiday (no-JS delete)
     */
    public function deleteHoliday($id)
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $business->holidays()->where('id', $id)->delete();

        return redirect()->route('business.profile.edit')->with('status', 'Holiday removed successfully!');
    }

    /**
     * Show blocked times management (old method - can be removed if not used)
     */
    public function blockedTimes()
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $blockedTimes = $business->blockedTimes()
            ->where('end_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->get();

        return view('business.blocked-times', compact('blockedTimes'));
    }

    /**
     * Store a new blocked time
     */
    public function storeBlockedTime(Request $request)
    {
        $validated = $request->validate([
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'reason' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $business->blockedTimes()->create($validated);

        return redirect()->back()->with('status', 'Time slot blocked successfully!');
    }

    /**
     * Delete a blocked time
     */
    public function deleteBlockedTime($id)
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $blockedTime = $business->blockedTimes()->findOrFail($id);
        $blockedTime->delete();

        return redirect()->back()->with('status', 'Blocked time removed successfully!');
    }
}
