<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAuthRequest;
use App\Http\Requests\UserRegisterStoreKeeper;
use App\Http\Services\UserService;

class UserController
{
    public function __construct(
        public UserService $userService,
    ) {
    }

    public function login(UserAuthRequest $request)
    {
        try {
            return response()->json($this->userService->login($request));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function logout()
    {
        return $this->userService->logout();
    }

    public function registerStoreKeeper(UserRegisterStoreKeeper $request)
    {
        try {
            return response()->json($this->userService->registerStoreKeeper($request));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
