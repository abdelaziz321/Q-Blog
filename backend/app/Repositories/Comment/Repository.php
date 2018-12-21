<?php

namespace App\Repositories\Comment;

use App\Comment;
use App\Repositories\BaseRepository;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class Repository extends BaseRepository implements RepositoryInterface
{
    protected $_model = '\\App\\Comment';

    /**
     * get pageinated comments with the total votes
     *
     * @param  integer $limit
     * @param  integer $page
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedComments($limit, $page)
    {
        $comments = Comment::with(['user', 'post', 'replyTo.user'])
            ->leftJoin('votes AS total', function ($join) {
                $join->on('comments.id', '=', 'total.comment_id');
            })
            ->selectRaw('comments.*, sum(total.vote) AS votes')
            ->groupBy('comments.id')
            ->orderBy('id', 'desc')
            ->get();

        $this->_total = $comments->count();

        $offset = ($page - 1) * $limit;
        return $comments->slice($offset, $limit);
    }

    /**
     * create a new comment
     *
     * @param  array $data consists of {body, post_id}
     * @return \App\Comment
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
        $comment = $this->getBy('id', $id);
        $comment->body = $body;
        $comment->save();
    }

    /**
     * delete the given comment
     *
     * @param  int   $id
     * @return void
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

        $comment = $this->getBy('id', $id);
        $comment->votes()->syncWithoutDetaching([
            $user->id => ['vote' => $value]
        ]);
    }
}
