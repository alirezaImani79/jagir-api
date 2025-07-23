<?php

namespace App\Http\Controllers;

use App\Enums\OTPStatus;
use App\Models\Role;
use App\Models\User;
use App\Services\OTPService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'regex:/09\d{9}/', 'unique:users,id'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
            'user_type' => ['nullable', 'in:service_provider,service_consumer']
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'email_verified_at' => now(),
            'password' => Hash::make($request->input('password'))
        ]);

        switch ($request->input('user_type', null)) {
            case 'service_provider':
                $user->roles()->attach(Role::SERVICE_PROVIDER_ROLE_ID);
                break;
            default:
                $user->roles()->attach(Role::SERVICE_CONSUMER_ROLE_ID);
                break;
        }

        OTPService::send($user->phone);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'VERIFICATION_CODE_SENT',
            'user' => $user
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ]);

        $user = User::where('email', $request->input('email'))->first();
        if($user && Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'token' => $user->createToken('Auth_token')->plainTextToken,
                'user' => $user
            ]);
        }

        return response()->json([
            'status' => 'CREDENTIALS_ARE_NOT_VALID'
        ]);
    }

    public function sentOtpCode(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/09\d{9}/', 'exists:users,phone']
        ]);

        OTPService::send($request->input('phone'));

        return response()->json([
            'status' => 'VERIFICATION_CODE_SENT'
        ]);
    }

    public function verifyOTPCode(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/09\d{9}/'],
            'code' => ['required', 'min:5', 'max:5']
        ]);

        $otpStatus = OTPService::verify($request->input('phone'), $request->input('code'));


        switch ($otpStatus) {
            case OTPStatus::Invalid:
                return response()->json([
                    'status' => OTPStatus::Invalid->value
                ], 400);

            case OTPStatus::Expired:
                return response()->json([
                    'status' => OTPStatus::Expired->value
                ], 400);
        }

        $user = User::where('phone', $request->input('phone'))->first();
        if (!$user) {
            return response()->json([
                'status' => 'phone_is_not_valid'
            ], 400);
        }

        if (! $user->phone_verified_at) {
            $user->update([
                'phone_verified_at' => now()
            ]);
        }


        return response()->json([
            'status' => 'PHONE_VERIFIED',
            'token' => $user->createToken('Auth_token')->plainTextToken
        ]);
    }
}
