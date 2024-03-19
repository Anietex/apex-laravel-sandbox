<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Responses\ResponseHandler;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct(private readonly UserService $userService)
    {

    }


    public function index(): JsonResponse
    {
        $users =  $this->userService->getUsers();

        return ResponseHandler::success($users);
    }



    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());
        return ResponseHandler::success($user, 'User created successfully', 201);
    }

}
