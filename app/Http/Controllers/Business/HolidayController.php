<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for managing business holidays (days when business is closed).
 */
class HolidayController extends Controller
{
    /**
     * Store a new holiday (no-JS add).
     *
     * @param Request $request expects 'date' (Y-m-d) and optional 'name'
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
     * Delete a holiday by id for the current owner's business.
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

        $business->holidays()->where('id', $id)->delete();

        return redirect()->route('business.profile.edit')->with('status', 'Holiday removed successfully!');
    }
}
