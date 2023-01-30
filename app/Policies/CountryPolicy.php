<?php

namespace App\Policies;

use App\Models\Country;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CountryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User|null $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User|null $user
     * @param Country $country
     * @return bool
     */
    public function view(?User $user, Country $country): bool
    {
        return true;
    }

    /**
     * Determine whether the user can export any models.
     *
     * @param User $user
     * @return bool
     */
    public function exportAny(User $user): bool
    {
        return $user->isAdministratorSiteRole();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isAdministratorSiteRole();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Country $country
     * @return bool
     */
    public function update(User $user, Country $country): bool
    {
        return $user->isAdministratorSiteRole();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Country $country
     * @return bool
     */
    public function delete(User $user, Country $country): bool
    {
        return $user->isAdministratorSiteRole();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Country $country
     * @return bool
     */
    public function restore(User $user, Country $country): bool
    {
        return $user->isAdministratorSiteRole();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Country $country
     * @return bool
     */
    public function forceDelete(User $user, Country $country): bool
    {
        return $user->isAdministratorSiteRole();
    }
}
