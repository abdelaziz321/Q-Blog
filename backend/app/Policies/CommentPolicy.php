<?php

namespace App\Policies;

use App\User;
use App\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the comment.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->privilege > 1;
    }

    public function createOrVote(User $user)
    {
        return $user->privilege > 0;
    }

    public function update(User $user, Comment $comment)
    {
        # owner
        if ($user->id == $comment->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the comment.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return mixed
     */
    public function delete(User $user, Comment $comment)
    {
        # owner
        if ($user->id == $comment->user_id) {
            return true;
        }

        # admin
        if ($user->isAdmin()) {
            return true;
        }

        # moderator
        $comment->load('post');
        $post = $comment->post()->first();
        if ($user->isModerator($post->category_id)) {
            return true;
        }

        # author
        if ($user->id == $post->id) {
            return true;
        }

        return false;
    }
}
