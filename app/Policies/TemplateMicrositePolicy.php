<?php

namespace App\Policies;

use App\Models\TemplateMicrosite;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TemplateMicrositePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TemplateMicrosite $templateMicrosite): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TemplateMicrosite $templateMicrosite): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TemplateMicrosite $templateMicrosite): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TemplateMicrosite $templateMicrosite): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TemplateMicrosite $templateMicrosite): bool
    {
        //
    }
}
