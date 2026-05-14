<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendSmsJob;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class ForgotPasswordController extends Controller
{
    public function showVerifyForm()
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request');
        }

        $user = User::where('email', session('reset_email'))->first();

        if (!$user) {
            return redirect()->route('password.request');
        }

        $secondsPassed = now()->diffInSeconds($user->updated_at);
        $secondsRemaining = (int) max(0, 300 - $secondsPassed);

        return view('auth.verify-email', compact('secondsRemaining'));
    }

    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $user = User::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->where('otp_expires_at', '>', now())
                    ->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP code.']);
        }

        session(['otp_verified' => true]);

        return redirect()->route('password.reset', [
                'token' => $request->otp,
                'email' => $request->email
            ])->with('status', 'Verification code sent to your email.');
    }

    public function resendOtp(Request $request)
    {
        $email = session('reset_email');

        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Session expired.'], 403);
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $otp = rand(100000, 999999);
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5)
            ]);

            SendSmsJob::dispatch(
                $user->contact_number,
                "We received a request to verify your account. Please use the following One-Time Password (OTP) to complete your verification: " . $otp
            );

            Mail::to($user->email)->send(new OtpMail($otp));

            return back()->with('status', 'A new verification code has been sent to your email.');
        }

        return back()->withErrors(['email' => 'User not found.']);
    }

    public function resetWithOtp(Request $request)
    {
        
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::where('email', $request->email)
                    ->first();
        
        if (!$user) {
            return back()->withErrors(['otp' => 'The verification code is invalid or has expired.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        session()->forget('reset_email');

        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }
}
