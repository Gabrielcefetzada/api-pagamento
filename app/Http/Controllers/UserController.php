<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAuthRequest;
use App\Http\Requests\UserRegister;
use App\Http\Requests\UserRegisterStoreKeeper;
use App\Http\Services\UserService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

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
        } catch (\Exception $ex) {
            if (method_exists($ex, 'render')) {
                return $ex->render($request);
            }

            Log::error($ex);

            return response([
                'error' => [
                    'httpCode' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                    'message'  => 'Erro de servidor',
                ],
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function registerCommonUser(UserRegister $request)
    {
        try {
            return response()->json($this->userService->registerCommonUser($request));
        } catch (\Exception $ex) {
            if (method_exists($ex, 'render')) {
                return $ex->render($request);
            }

            Log::error($ex);

            return response([
                'error' => [
                    'httpCode' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                    'message'  => 'Erro de servidor',
                ],
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function registerStoreKeeper(UserRegisterStoreKeeper $request)
    {
        try {
            return response()->json($this->userService->registerStoreKeeper($request));
        } catch (\Exception $ex) {
            if (method_exists($ex, 'render')) {
                return $ex->render($request);
            }

            Log::error($ex);

            return response([
                'error' => [
                    'httpCode' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                    'message'  => 'Erro de servidor',
                ],
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
