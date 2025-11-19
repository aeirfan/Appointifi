<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Manages user login/logout lifecycle:
 * - Display login form
 * - Authenticate credentials (via LoginRequest::authenticate())
 * - Regenerate session to prevent fixation attacks
 * - Logout and invalidate session
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    /**
     * Show the login form.
     * @return View
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * Regenerates session on success and redirects to intended dashboard route.
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    /**
     * Log out the current user and invalidate their session.
     * Regenerates CSRF token for safety.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
