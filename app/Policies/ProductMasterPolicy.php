<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProductMaster;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductMasterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the productMaster can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the productMaster can view the model.
     */
    public function view(User $user, ProductMaster $model): bool
    {
        return true;
    }

    /**
     * Determine whether the productMaster can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the productMaster can update the model.
     */
    public function update(User $user, ProductMaster $model): bool
    {
        return true;
    }

    /**
     * Determine whether the productMaster can delete the model.
     */
    public function delete(User $user, ProductMaster $model): bool
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
     * Determine whether the productMaster can restore the model.
     */
    public function restore(User $user, ProductMaster $model): bool
    {
        return false;
    }

    /**
     * Determine whether the productMaster can permanently delete the model.
     */
    public function forceDelete(User $user, ProductMaster $model): bool
    {
        return false;
    }
}
