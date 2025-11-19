<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\SearchService;
use Illuminate\Http\Request;

/**
 * Controller for browsing and searching businesses (customer-facing).
 * Handles location-aware search and sorting by name or distance.
 */
class BusinessSearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Display a list of all businesses (for customers to browse).
     * With search and geo-proximity filtering.
     *
     * Request inputs:
     * - query: optional text query (business name/description)
     * - location: optional free-form address/city; will be geocoded (best-effort)
     * - sort_by: optional sort key (defaults to 'name'); may support distance when coords known
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = $request->get('query');
        $location = $request->get('location');
        $sortBy = $request->get('sort_by', 'name');

        // Geocode user's location if provided (best-effort; failures are tolerated)
        $userCoordinates = null;
        if ($location) {
            $userCoordinates = $this->searchService->geocodeAddress($location);
        }

        // Search businesses using provided query and optional coordinates (enables distance-aware sorting)
        $businesses = $this->searchService->searchBusinesses(
            $query,
            $userCoordinates['latitude'] ?? null,
            $userCoordinates['longitude'] ?? null,
            $sortBy
        );

        return view('bookings.businesses.index', compact('businesses', 'query', 'location', 'sortBy'));
    }

    /**
     * Show a specific business and its services.
     *
     * @param Business $business
     * @return \Illuminate\View\View
     */
    public function show(Business $business)
    {
        $business->load(['services', 'locations']);
        return view('bookings.businesses.show', compact('business'));
    }
}
