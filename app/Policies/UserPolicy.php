<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {

    }

    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function delete(User $user, User $model)
    {
        return $user->id === $model->id;
    }

    public function view(User $user, User $model)
    {
        return $user->id === $model->id;
    }
}
