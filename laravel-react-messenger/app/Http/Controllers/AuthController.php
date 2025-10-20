<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponseTrait;
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function signup(SignupRequest $request)
    {
        try {
            $data = $this->authService->signup($request);
            $user = new AuthResource($data['user']);
            return $this->successResponse(['user' => $user, 'token' => $data['token']], 'Account created successfully! Welcome to our chatting application.', 201);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse($e->getMessage(), $statusCode);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request);
            $user = new AuthResource($data['user']);
            return $this->successResponse(['user' => $user, 'token' => $data['token']], 'Welcome! You’re now logged in.', 200);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse($e->getMessage(), $statusCode);
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request);
            return $this->successResponse([], 'You have been successfully logged out.', 200);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse($e->getMessage(), $statusCode);
        }
    }
}
