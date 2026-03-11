<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        // Try Admin login first (default guard: web)
        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Try Employee login
        if (Auth::guard('employee')->attempt($credentials, $request->boolean('remember'))) {

            $employee = Auth::guard('employee')->user();

            // 🚫 Block inactive employee
            if (!$employee->status) {
                Auth::guard('employee')->logout();

                throw ValidationException::withMessages([
                    'email' => 'Your account is inactive. Please contact admin.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended('/employee/dashboard');
        }

        // If both fail
        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logout both guards
        Auth::guard('web')->logout();
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}