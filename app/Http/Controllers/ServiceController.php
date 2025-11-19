<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for CRUD operations on services offered by a business.
 * All actions are constrained to the authenticated owner's business.
 * Includes integrity protection to prevent deletion of services with future appointments.
 */
class ServiceController extends Controller
{
    // This helper method gets the user's business
    private function getBusiness()
    {
        $business = Auth::user()->business;
        if (!$business) {
            abort(403, 'You must create a business profile first.');
        }
        return $business;
    }

    /**
     * Display a listing of the resource.
     * Returns only services belonging to the authenticated owner's business.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $business = $this->getBusiness();
        $services = $business->services()->get(); // Get only this business's services
        return view('business.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('business.services.create');
    }

    /**
     * Store a newly created resource in storage.
     * Validates inputs and associates the service with the current business.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $business = $this->getBusiness();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:5', // 5 minute minimum
            'price' => 'nullable|numeric|min:0',
        ]);

        $business->services()->create($validated);

        return redirect()->route('business.services.index')->with('status', 'Service created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        // Not needed for our MVP
        return redirect()->route('business.services.index');
    }

    /**
     * Show the form for editing the specified resource.
     * Ensures the service belongs to the authenticated owner's business.
     *
     * @param Service $service
     * @return \Illuminate\View\View
     */
    public function edit(Service $service)
    {
        // *** SECURITY CHECK ***
        // Ensure the service they are editing belongs to their business
        if ($service->business_id !== Auth::user()->business->id) {
            abort(403, 'Unauthorized action.');
        }

    return view('business.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     * Validates input and applies updates. Ownership enforced.
     *
     * @param Request $request
     * @param Service $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Service $service)
    {
        // *** SECURITY CHECK ***
        if ($service->business_id !== Auth::user()->business->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:5',
            'price' => 'nullable|numeric|min:0',
        ]);

        $service->update($validated);
        
        return redirect()->route('business.services.index')->with('status', 'Service updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage after integrity checks.
     * Prevents deletion if future appointments reference the service.
     *
     * @param Service $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Service $service)
    {
        // *** SECURITY CHECK ***
        if ($service->business_id !== Auth::user()->business->id) {
            abort(403, 'Unauthorized action.');
        }

        // *** DATA INTEGRITY LOGIC ***
        if ($service->appointments()->where('start_time', '>=', now())->exists()) {
            return redirect()->route('business.services.index')
                   ->with('error', 'Cannot delete service! It has upcoming appointments.');
        }

        $service->delete();

        return redirect()->route('business.services.index')->with('status', 'Service deleted!');
    }
}