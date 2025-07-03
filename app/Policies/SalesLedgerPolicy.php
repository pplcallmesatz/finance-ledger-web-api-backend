<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SalesLedger;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesLedgerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the salesLedger can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the salesLedger can view the model.
     */
    public function view(User $user, SalesLedger $model): bool
    {
        return true;
    }

    /**
     * Determine whether the salesLedger can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the salesLedger can update the model.
     */
    public function update(User $user, SalesLedger $model): bool
    {
        return true;
    }

    /**
     * Determine whether the salesLedger can delete the model.
     */
    public function delete(User $user, SalesLedger $model): bool
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
     * Determine whether the salesLedger can restore the model.
     */
    public function restore(User $user, SalesLedger $model): bool
    {
        return false;
    }

    /**
     * Determine whether the salesLedger can permanently delete the model.
     */
    public function forceDelete(User $user, SalesLedger $model): bool
    {
        return false;
    }
}
