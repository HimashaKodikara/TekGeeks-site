<?php

namespace App\Trait;

trait GeneratesOtp
{
    /**
     * Generates a 6-digit OTP string.
     *
     * @return string
     */
    public function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
