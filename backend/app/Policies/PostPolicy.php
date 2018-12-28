<?php

namespace App\Policies;

use App\User;
use App\Post;
use App\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function viewPosts(User $user)
    {
        return $user->privilege > 1;
    }

    /**
     * Determine whether the user can create posts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, int $category_id)
    {
        # authors and admins
        if (in_array($user->privilege, [2, 4])) {
            return true;
        }

        # moderator can only add post in its category
        if ($user->isModerator($category_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        # regular|banned-users can't add posts
        if ($user->privilege < 2) {
            return false;
        }

        return $user->id == $post->author_id;
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        # admin
        if ($user->isAdmin()) {
            return true;
        }

        # owner
        if ($user->id == $post->author_id) {
            return true;
        }

        # moderator can delete post in its category
        if ($user->isModerator($post->category_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can publish|unpublish the given post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function publishing(User $user, Post $post)
    {
        # admin
        if ($user->isAdmin()) {
            return true;
        }

        # moderator can publish posts in its category
        if ($user->isModerator($post->category_id)) {
            return true;
        }

        return false;
    }

    public function assignTags(User $user, Post $post)
    {
        # admin
        if ($user->isAdmin()) {
            return true;
        }

        # owner
        if ($user->id == $post->author_id) {
            return true;
        }

        # moderator can publish posts in its category
        if ($user->isModerator($post->category_id)) {
            return true;
        }

        return false;
    }

    public function recommend(User $user, Post $post)
    {
        # banned user can't recommend
        if ($user->isBanned()) {
            return false;
        }

        # user can't recommend his own post
        if ($user->id === $post->user_id) {
            return false;
        }

        return true;
    }
}
