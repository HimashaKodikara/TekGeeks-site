<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendOtpEmail;
use App\Jobs\SendSmsJob;
use App\Models\SessionModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function showVerifyForm()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $secondsPassed = now()->timestamp - (int) session('otp_sent_at', 0);
        $secondsRemaining = (int) max(0, 300 - $secondsPassed);

        return view('auth.otp-verify', compact('secondsRemaining'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6'
        ]);

        $userId = session('otp_user_id');
        $user = User::find($userId);
        

        if ($user && $user->otp == $request->otp && now()->isBefore($user->otp_expires_at)) {
            
            Auth::login($user);
            
            $request->session()->regenerate();
            
            $user->update(['otp' => null, 'otp_expires_at' => null]);
            session()->forget('otp_user_id');

            $currentSessionId = $request->session()->getId();

            SessionModel::updateOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => $currentSessionId]
            );

            if ($user->can('dashboard')) {
                $redirect = route('dashboard');
            } elseif ($user->hasRole('Institute')) { 
                $redirect = route('institute.index');
            } else {
                $redirect = route('login');
            }


            return response()->json([
                'status' => 'success',
                'message' => 'Identity verified! Redirecting...',
                'redirect' => $redirect
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'The code you entered is invalid or has expired.'
        ], 422);
    }

    public function resend()
    {
        $user = User::find(session('otp_user_id'));
        if (!$user){ return redirect()->route('login');}

        $otp = rand(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $message = "Dear Declarant,\n\n" .
            "A login attempt was made to access your Declaration of Assets & Liabilities profile.\n\n" .
            "Your One-Time Password (OTP) is: " . $otp . "\n\n" .
            "Please enter this code to securely proceed with the login.\n\n" .
            "For security reasons, do not share this OTP with anyone.";

        SendSmsJob::dispatch($user->contact_number, $message);

        SendOtpEmail::dispatch(
            $user,
            $otp
        );

        session(['otp_sent_at' => now()->timestamp]);

        return back()->with('status', 'A new OTP has been sent.');
    }

    public function cancel()
    {
        if (session()->has('otp_user_id')) {
            $user = User::find(session('otp_user_id'));
            if ($user) {
                $user->update(['otp' => null, 'otp_expires_at' => null]);
            }
        }

        session()->forget('otp_user_id');

        return redirect()->route('login');
    }
}
