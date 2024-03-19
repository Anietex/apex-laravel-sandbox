<?php

namespace App\Http\Controllers;

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
        $users =  $this->userService->users();

        return ResponseHandler::success($users);
    }

}
