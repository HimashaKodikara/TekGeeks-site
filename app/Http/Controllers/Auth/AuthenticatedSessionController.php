<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Jobs\SendOtpEmail;
use App\Jobs\SendSmsJob;
use App\Mail\OtpMail;
use App\Models\SessionModel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
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
        try {
            $request->authenticate();
            $request->session()->regenerate();

            Auth::logoutOtherDevices($request->password);
            $currentSessionId = $request->session()->getId();

            SessionModel::where('user_id', Auth::id())
                ->whereNotIn('session_id', [$currentSessionId])
                ->delete();

            SessionModel::updateOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => $currentSessionId]
            );

            $user = Auth::user();
            

            if ($user->email === 'superadmin@tekgeeks.net') {
                return redirect()->intended(route('dashboard'));
            }

            // if ($user->hasRole('Institute') || $user->customer_type === 'institute') {
            //     return redirect()->route('otp.verify');
            // }

            // 5. OTP Generation for All Other Users
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            $otp = rand(100000, 999999);
            
            $lockedUser->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            session(['otp_user_id' => $lockedUser->id, 'otp_sent_at' => now()->timestamp]);

            $message = "Dear Declarant,\n\n" .
                "Your One-Time Password (OTP) for the Assets Declaration Portal is: " . $otp . "\n\n" .
                "For security reasons, do not share this code.";

            SendSmsJob::dispatch($user->contact_number, $message);
            SendOtpEmail::dispatch($user, $otp);

            Auth::logout();

            return redirect()->route('otp.verify');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Login Error: " . $e->getMessage());

            return back()->withErrors([
                'email' => 'An unexpected error occurred during login. Please try again later.'
            ]);
        }
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        SessionModel::where('user_id', Auth::id())->delete();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/declarant-management-portal-login');
    }
}
