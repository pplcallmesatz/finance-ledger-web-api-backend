<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CategoryMaster;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryMasterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the categoryMaster can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the categoryMaster can view the model.
     */
    public function view(User $user, CategoryMaster $model): bool
    {
        return true;
    }

    /**
     * Determine whether the categoryMaster can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the categoryMaster can update the model.
     */
    public function update(User $user, CategoryMaster $model): bool
    {
        return true;
    }

    /**
     * Determine whether the categoryMaster can delete the model.
     */
    public function delete(User $user, CategoryMaster $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the categoryMaster can restore the model.
     */
    public function restore(User $user, CategoryMaster $model): bool
    {
        return false;
    }

    /**
     * Determine whether the categoryMaster can permanently delete the model.
     */
    public function forceDelete(User $user, CategoryMaster $model): bool
    {
        return false;
    }
}
