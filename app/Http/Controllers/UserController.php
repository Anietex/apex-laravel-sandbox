<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Responses\ResponseHandler;
use App\Models\User;
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


    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {

        $userData = collect($request->validated())->filter(fn($value) => $value !== null && $value !== '')->toArray();

        $user = $this->userService->update($user, $userData);
        return ResponseHandler::success($user, 'User updated successfully');
    }

}
