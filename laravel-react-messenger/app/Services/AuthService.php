<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function signup($request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password)
            ]);

            $token = $user->createToken('User')->accessToken;
            return ['user' => $user, 'token' => $token];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function login($request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password))
                throw new Exception('The provided credentials are incorrect.', 400);

            $token = $user->createToken('User')->accessToken;
            return ['user' => $user, 'token' => $token];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function logout($request)
    {
        try {
            $request->user()->token()->revoke();
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
