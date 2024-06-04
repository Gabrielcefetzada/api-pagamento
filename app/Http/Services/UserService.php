<?php

namespace App\Http\Services;

use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UniqueConstraintExistsException;
use App\Http\Requests\UserAuthRequest;
use App\Http\Requests\UserRegister;
use App\Http\Requests\UserRegisterStoreKeeper;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function login(UserAuthRequest $request): \Illuminate\Http\JsonResponse|array
    {
        $loginUserData = $request;

        $user = User::where('email', $loginUserData['email'])->first();

        if (! $user || ! Hash::check($loginUserData['password'], $user->password)) {
            throw new InvalidCredentialsException([]);
        }

        $token = $user->createToken($user->name.'-AuthToken')->plainTextToken;

        return [
            'access_token' => $token,
        ];
    }

    private function commonUserParamsExistenceChecks(UserRegister $request): void
    {
        if (User::where('cpf', $request['cpf'])->exists()) {
            throw new UniqueConstraintExistsException('Já existe um usuário cadastrado com esse CPF', []);
        }

        if (User::where('email', $request['email'])->exists()) {
            throw new UniqueConstraintExistsException('Já existe um usuário cadastrado com esse E-mail', []);
        }
    }

    /**
     * @throws Exception
     */
    public function registerCommonUser(UserRegister $request): array
    {
        $this->commonUserParamsExistenceChecks($request);

        User::create([
            'name'     => $request['name'],
            'cpf'      => $request['cpf'],
            'email'    => $request['email'],
            'password' => $request['password'],
        ])->assignRole('common-user');

        return [
            'Usuário cadastrado com sucesso!',
        ];
    }

    public function registerStoreKeeper(UserRegisterStoreKeeper $request): array
    {
        $this->commonUserParamsExistenceChecks($request);

        if (User::where('cnpj', $request['cnpj'])->exists()) {
            throw new UniqueConstraintExistsException('Já existe um usuário cadastrado com esse CNPJ', []);
        }

        User::create([
            'name'     => $request['name'],
            'cpf'      => $request['cpf'],
            'cnpj'     => $request['cnpj'],
            'email'    => $request['email'],
            'password' => $request['password'],
        ])->assignRole('store-keeper');

        return [
            'Usuário cadastrado com sucesso!',
        ];
    }
}
