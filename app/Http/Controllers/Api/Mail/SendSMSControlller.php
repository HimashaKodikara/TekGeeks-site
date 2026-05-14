<?php

namespace App\Http\Controllers\Api\Mail;

use App\Http\Controllers\Controller;
use App\Mail\OtpLoginMail;
use App\Mail\OtpMail;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSMSControlller extends Controller
{
   public function sendEmail(Request $request)
    {
        $request->validate([
            'email'   => 'required|email',
            'otp' => 'required|string',
            'type'    => 'required|string',
            'jwt'     => 'required|string',
        ]);

        $email   = $request->input('email');
        $otp     = $request->input('otp');
        $type    = $request->input('type');
        $jwt     = $request->input('jwt');


        if ($jwt !== config('services.email.email_secret')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorised.',
            ], 400);
        }

        try {
            if ($type === 'login') {
                Mail::to($email)->queue(new OtpLoginMail($otp));
            }elseif($type === 'registration') {
                Mail::to($email)->queue(new OtpMail($otp));
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid message type provided.',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to ' . $email,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send OTP to {$email}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not send OTP. Please try again later.',
            ], 500);
        }
    }

    private function isValidJWT($jwt)
    {
        $secret = config('services.email.email_secret');

        try {
            JWT::decode($jwt, new Key($secret, 'HS256'));
            return true;
        } catch (\Exception $e) {
            Log::warning("Invalid JWT attempt: " . $e->getMessage());
            return false;
        }
    }
}
