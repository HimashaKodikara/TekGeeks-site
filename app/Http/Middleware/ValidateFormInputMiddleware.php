<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use voku\helper\AntiXSS;

class ValidateFormInputMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $errors = [];
        $antiXss = new AntiXSS();

        foreach ($request->except('_token') as $key => $value) {
            // Skip email, password, and arrays
            if (in_array($key, ['email', 'password']) || is_array($value)) {
                continue;
            }

            if (is_string($value)) {
                $cleaned = $antiXss->xss_clean($value);

                // Custom detection: flag scriptless function() payloads
                if (
                    $cleaned !== $value ||
                    preg_match('/\bfunction\s*\(.*\)\s*\{/', $value) ||
                    preg_match('/\(\s*function\s*\(.*\)\s*\)\s*\(\s*\)/', $value) ||
                    preg_match('/javascript\s*:/i', $value)
                ) {
                    $errors[$key] = 'Unsafe or suspicious script content detected.';
                }
            }
        }

        if (!empty($errors)) {
            return back()
                ->withInput()
                ->withErrors($errors);
        }

        return $next($request);
    }
}
