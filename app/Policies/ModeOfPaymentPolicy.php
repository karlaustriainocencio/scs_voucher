<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ModeOfPayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModeOfPaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_mode::of::payment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ModeOfPayment $modeOfPayment): bool
    {
        return $user->can('view_mode::of::payment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_mode::of::payment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ModeOfPayment $modeOfPayment): bool
    {
        return $user->can('update_mode::of::payment');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ModeOfPayment $modeOfPayment): bool
    {
        return $user->can('delete_mode::of::payment');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_mode::of::payment');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ModeOfPayment $modeOfPayment): bool
    {
        return $user->can('force_delete_mode::of::payment');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_mode::of::payment');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ModeOfPayment $modeOfPayment): bool
    {
        return $user->can('restore_mode::of::payment');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_mode::of::payment');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ModeOfPayment $modeOfPayment): bool
    {
        return $user->can('replicate_mode::of::payment');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_mode::of::payment');
    }
}
