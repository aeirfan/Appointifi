<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for managing recurring blocked times (e.g., lunch breaks every weekday).
 */
class RecurringBlockController extends Controller
{
    /**
     * Store a new recurring blocked time window (e.g., lunch break every weekday).
     *
     * @param Request $request expects title (optional), start_time/end_time (H:i), and days_of_week array
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
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
     * Delete an existing recurring blocked time by id for the current owner's business.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        $business->recurringBlockedTimes()->where('id', $id)->delete();

        return redirect()->route('business.profile.edit')->with('status', 'Recurring block time removed successfully!');
    }
}
