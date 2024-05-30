<?php

namespace App\Http\Services;

use App\Http\Requests\UserAuthRequest;
use App\Http\Requests\UserRegisterStoreKeeper;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function login(UserAuthRequest $request)
    {
        $loginUserData = $request;

        $user = User::where('email', $loginUserData['email'])->first();

        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas.'
            ], 401);
        }

        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        return [
            'access_token' => $token,
        ];
    }

    public function logout()
    {
        return 'logout';
    }

    public function registerStoreKeeper(UserRegisterStoreKeeper $request)
    {
        if (User::where('cpf', $request['cpf'])->exists()) {
            throw new Exception('Já existe um usuário cadastrado com esse CPF');
        }

        if (User::where('cnpj', $request['cnpj'])->exists()) {
            throw new Exception('Já existe um usuário cadastrado com esse CNPJ');
        }

        User::create([
            'name'     => $request['name'],
            'cpf'      => $request['cpf'],
            'cnpj'     => $request['cnpj'],
            'email'    => $request['email'],
            'password' => $request['password'],
        ])->assignRole('store-keeper');

        return [
            'Usuário cadastrado com sucesso!'
        ];
    }
}
