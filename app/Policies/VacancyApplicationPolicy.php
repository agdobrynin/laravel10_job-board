<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VacancyApplication;

class VacancyApplicationPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VacancyApplication $vacancyApplication): bool
    {
        return $vacancyApplication->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VacancyApplication $vacancyApplication): bool
    {
        return $vacancyApplication->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VacancyApplication $vacancyApplication): bool
    {
        return $vacancyApplication->user_id === $user->id;
    }
}
