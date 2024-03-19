<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Responses\ResponseHandler;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function __construct(private readonly AuthService $authService)
    {

    }


    /**
     * Login a user
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            $user = $this->authService->login($credentials);
            return ResponseHandler::success($user, 'Login successful');

        } catch (\Exception $e) {
            return ResponseHandler::error($e->getMessage(), 400);
        }

    }


    /**
     * Register a new user
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            $user = $this->authService->register($data);
            return ResponseHandler::success($user, 'User registered successfully');

        } catch (\Exception $e) {
            return ResponseHandler::error($e->getMessage(), 400);
        }
    }
}
