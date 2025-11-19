<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for the business owner's main dashboard.
 * Provides overview/summary only; detailed operations split into specialized controllers.
 */
class DashboardController extends Controller
{
    /**
     * Show the business dashboard overview.
     * Redirects to profile creation if no business exists.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('business.profile.create');
        }

        // Load the primary location for convenience
        $location = $business->locations()->first();

        return view('business.dashboard', compact('business', 'location'));
    }
}
