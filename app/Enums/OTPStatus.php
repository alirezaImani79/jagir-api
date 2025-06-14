<?php

namespace App\Enums;

enum OTPStatus: string {
    case Valid = 'valid';
    case Invalid = 'invalid';
    case Expired = 'expired';
}
