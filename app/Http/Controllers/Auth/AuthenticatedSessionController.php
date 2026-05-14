<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\SessionModel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        if (session()->has('otp_user_id')) {
            $user = User::find(session('otp_user_id'));
            if ($user) {
                $user->update(['otp' => null, 'otp_expires_at' => null]);
            }
            session()->forget('otp_user_id');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Log out from all other devices
        Auth::logoutOtherDevices($request->password);

        // Retrieve the user's stored session IDs from the database
        SessionModel::where('user_id', Auth::id())->pluck('session_id');

        // Get the current session ID
        $currentSessionId = $request->session()->getId();

        // Delete old session records except for the current session
        SessionModel::where('user_id', Auth::id())->whereNotIn('session_id', [$currentSessionId])->delete();

        // Update or create a new session record for the current session
        SessionModel::updateOrCreate(['user_id' => Auth::id()], ['session_id' => $currentSessionId]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Session row will be removed or invalidated by Laravel natively

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
