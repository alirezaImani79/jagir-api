<?php

namespace App\Services;

use App\Enums\OTPStatus;
use Illuminate\Support\Facades\Cache;

class OTPService
{
    public static function send(string $phone): void
    {
        $ttl = 60 * 2;
        $code = Cache::remember(sprintf('otp:phone-%s', $phone), $ttl, fn() => random_int(10000, 99999));

        //send code
        logger('OPT code : ' . $code);
    }

    public static function verify(string $phone, string $attemptCode): OTPStatus
    {
        $code = Cache::get(sprintf('otp:phone-%s', $phone));

        if(! $code) return OTPStatus::Expired;

        if($attemptCode != $code) return OTPStatus::Invalid;

        Cache::delete(sprintf('otp:phone-%s', $phone));
        return OTPStatus::Valid;
    }
}
