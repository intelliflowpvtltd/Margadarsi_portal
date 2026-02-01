<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     * Clear permission cache when role changes.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user): void
    {
        // Clear permission cache if role changed
        if ($user->isDirty('role_id')) {
            $user->clearPermissionCache();
        }
    }

    /**
     * Handle the User "retrieved" event.
     * Ensures permissions are loaded with role relationship.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function retrieved(User $user): void
    {
        // Eager load role if not already loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role.permissions');
        }
    }
}
