<?php

namespace App\Policies;

use App\Models\Transference;
use App\Models\User;

class TransferencePolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transference $transference): bool
    {
        return $user->id === $transference->payee || $user->id === $transference->payer;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('common-user');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transference $transference): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Transference $transference): bool
    {
        return false;
    }
}
