<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Responses\ResponseHandler;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

    public function show(User $user): JsonResponse
    {
        if(Gate::denies('view-user', $user)){
            return ResponseHandler::error('You do not have permission to view this user', 403);
        }

        return ResponseHandler::success($user);
    }



    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            $userData = collect($request->validated())->filter(fn($value) => $value !== null && $value !== '')->toArray();

            $user = $this->userService->create($userData);
            return ResponseHandler::success($user, 'User created successfully',201);
        }catch (\Exception $exception) {
            return ResponseHandler::error('An error occurred while creating user', 500);
        }
    }


    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {

        if(Gate::denies('update-user', $user)){
            return ResponseHandler::error('You do not have permission to update this user', 403);
        }

        try {
            $userData = collect($request->validated())->filter(fn($value) => $value !== null && $value !== '')->toArray();

            $user = $this->userService->update($user, $userData);
            return ResponseHandler::success($user, 'User updated successfully');
        }catch (\Exception $exception) {
            return ResponseHandler::error('An error occurred while updating user', 500);
        }
    }


    public function destroy(User $user): JsonResponse
    {

        if(Gate::denies('delete-self', $user)){
            return ResponseHandler::error('You do not have permission to delete yourself.', 403);
        }


        if(Gate::denies('delete-user')){
            return ResponseHandler::error('You do not have permission to delete this user', 403);
        }

        try {
            if($this->userService->delete($user)){
                return ResponseHandler::success(null, 'User deleted successfully.');
            }
            return ResponseHandler::error('Unable to delete user at the moment.', 400);
        }catch (\Exception $exception) {
            return ResponseHandler::error('An error occurred while deleting user.', 500);
        }

    }

}
