<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function viewUsers(User $user)
    {
        return $user->privilege > 1;
    }


    /**
     * Determine whether the user can assign role to users.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function assignRole(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update his profile.
     *
     * @param  \App\User  $user
     * @param  \App\User  $profile
     * @return mixed
     */
    public function update(User $user, User $profile)
    {    
        return $user->id == $profile->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user, User $deleted)
    {
        return $user->isAdmin() || $user->id == $deleted->id;
    }
}
