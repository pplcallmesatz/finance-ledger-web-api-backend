<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ExpenseLedger;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpenseLedgerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the expenseLedger can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the expenseLedger can view the model.
     */
    public function view(User $user, ExpenseLedger $model): bool
    {
        return true;
    }

    /**
     * Determine whether the expenseLedger can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the expenseLedger can update the model.
     */
    public function update(User $user, ExpenseLedger $model): bool
    {
        return true;
    }

    /**
     * Determine whether the expenseLedger can delete the model.
     */
    public function delete(User $user, ExpenseLedger $model): bool
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
     * Determine whether the expenseLedger can restore the model.
     */
    public function restore(User $user, ExpenseLedger $model): bool
    {
        return false;
    }

    /**
     * Determine whether the expenseLedger can permanently delete the model.
     */
    public function forceDelete(User $user, ExpenseLedger $model): bool
    {
        return false;
    }
}
