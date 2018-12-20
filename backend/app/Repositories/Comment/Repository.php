<?php

namespace App\Repositories\Comment;

use App\Comment;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class Repository implements RepositoryInterface
{
    /**
     * the current comment we are working in
     *
     * @var App\Comment
     */
    private $comment;

    /**
     * get the comment which has id=$id from the propety $comment or from DB
     *
     * @param  int $id the id of the comment
     * @return App\Comment
     */
    public function get(int $id)
    {
        if (empty($comment) || $comment->id !== $id) {
            $this->comment = Comment::where('id', $id)->firstOrFail();
        }

        return $this->comment;
    }

    /**
     * create a new comment
     *
     * @param  array $data consists of {body, post_id}
     * @return Aoo\Comment
     */
    public function create($data)
    {
        $user = resolve(AuthUserRepo::class)->user();

        $comment = new Comment;
        $comment->body = $data['body'];
        $comment->post_id = $data['post_id'];
        $comment->user_id = $user->id;
        $comment->save();

        return $comment;
    }

    /**
     * update the given comment
     *
     * @param  int $id the id of the comment
     * @param  string $body
     * @return void
     */
    public function update($id, $body)
    {
        $comment = $this->get($id);
        $comment->body = $body;
        $comment->save();
    }

    /**
     * delete the given comment
     *
     * @param  int $id
     * @return [type]     [description]
     */
    public function delete(int $id)
    {
        Comment::where('id', $id)->delete();
    }

    /**
     * the authenticated user vote up|down the given comment id
     *
     * @param  int $id the id of the comment
     * @param  int $value 1 => up & -1 => down
     * @return void
     */
    public function vote(int $id, int $value)
    {
        $user = resolve(AuthUserRepo::class)->user();

        $comment = $this->get($id);
        $comment->votes()->syncWithoutDetaching([
            $user->id => ['vote' => $value]
        ]);
    }
}
