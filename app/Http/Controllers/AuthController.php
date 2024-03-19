<?php

namespace App\Http\Controllers;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required'
            ]);


            $user = $this->authService->login($credentials);
            return ResponseHandler::success($user, 'Login successful');

        } catch (\Exception $e) {
            return ResponseHandler::error($e->getMessage(), 400);
        }

    }
}
