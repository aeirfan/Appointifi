<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Handles user registration flow:
 * - Presents registration form
 * - Validates and creates user record (with role assignment)
 * - Fires Registered event (email verification dispatch, etc.)
 * - Logs user in and redirects based on role (owner/customer)
 */
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    /**
     * Show the registration form.
     *
     * @return View
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * Adds role validation and selects post-registration destination.
     *
     * @param Request $request
     * @return RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:customer,owner'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role (owners sent to business dashboard; customers to their dashboard)
        if ($user->role === 'owner') {
            return redirect()->route('business.dashboard');
        } else {
            return redirect()->route('customer.dashboard');
        }
    }
}
