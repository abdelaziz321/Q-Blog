<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the authenticated user can view the users info.
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
     * Determine whether the authenticated user can assign role to the given user model.
     * the user can't change the role of the user model if:
     * - the $model role = moderator && the $desiredRole != [moderator, admin]
     * - the $model role != [admin, moderator] && $desiredRole = moderator
     * - the $model role = admin && the $model moderate categories && the $desiredRole != [moderator, admin]
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @param  boolean    $moderate determin if the $model moderate some categories
     * @return mixed
     */
    public function assignRole(User $user, User $model, string $desiredRole, bool $moderate = false)
    {
        $modelRole = $model->role();

        if ($modelRole === $desiredRole) {
            return true;
        }

        // deny if he is now a moderator and we want to make him regular|banned|author
        if ($modelRole === 'moderator' && $desiredRole !== 'admin') {
            return false;
        }

        // deny if he is now regular|banned|author and we want to make him a moderator
        if ($modelRole !== 'admin' && $desiredRole === 'moderator') {
            return false;
        }

        // deny if he is now an admin moderate some categories and we want to make him regular|banned|author
        if ($modelRole === 'admin' && $desiredRole !== 'moderator' && $moderate) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the authenticated user can update the profile which has the given slug.
     *
     * @param  \App\User  $user
     * @param  string     $slug the slug of the profile user want to updated
     * @return mixed
     */
    public function update(User $user, string $slug)
    {
        return $user->slug === $slug;
    }

    /**
     * Determine whether the authenticated user can delete the given user model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return $user->isAdmin() || $user->id == $model->id;
    }
}
