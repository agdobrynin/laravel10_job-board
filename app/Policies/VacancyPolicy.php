<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Auth\Access\Response;

class VacancyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return (bool)$user->employer;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vacancy $vacancy): bool
    {
        return $user->employer && $vacancy->employer->id === $user->employer->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return (bool)$user->employer;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vacancy $vacancy): bool
    {
        return $user->employer && $vacancy->employer_id === $user->employer->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vacancy $vacancy): bool
    {
        return $user->employer && $vacancy->employer_id === $user->employer->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Vacancy $vacancy): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Vacancy $vacancy): bool
    {
        return $user->employer && $vacancy->employer_id === $user->employer->id;
    }

    /**
     * Determine the user can apply for vacancy.
     */
    public function apply(?User $user, Vacancy $vacancy): Response
    {
        if (null === $user) {
            return Response::allow();
        }

        return $vacancy->hasUserVacancyApplication($user)
            ? Response::deny('Already applied to this vacancy')
            : Response::allow();
    }
}
