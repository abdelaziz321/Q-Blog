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
     * @param  int $limit
     * @param  int $page
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
     * get the comments of the given post.
     * - if the $published variable = true, the method will return the comments
     * only if their post was published
     *
     * @param  string $slug
     * @param  int    $limit
     * @param  int    $page
     * @param  boolean $published 1 => get only if published
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPostComments(string $slug, int $limit, int $page, bool $published = false)
    {
        $offset = ($page - 1) * $limit;

        $userId = resolve(AuthUserRepo::class)->user()->id ?? 0;

        return Comment::with(['user'])
            ->whereHas('post', function ($query) use ($slug, $published) {
                $query->where('slug', $slug);
                if ($published) {
                    $query->published();
                }
            })
            ->leftJoin('votes AS total', function ($join) {
                $join->on('comments.id', '=', 'total.comment_id');
            })
            ->leftJoin('votes AS voted', function ($join) use ($userId) {
                $join->on('comments.id', '=', 'voted.comment_id')
                ->where('voted.user_id', $userId);
            })
            ->selectRaw('comments.*, sum(total.vote) AS votes, sum(DISTINCT voted.vote) AS voted')
            ->groupBy('comments.id')
            ->orderBy('id', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    /**
     * get the comment of the given id with count the votes
     *
     * @param  int    $id the id of the comment
     * @return \stdClass
     */
    public function getCommentWithVotes(int $id)
    {
        if (empty($this->_record)) {
            $this->_record = Comment::where('id', $id)
                ->leftJoin('votes AS total', function ($join) {
                    $join->on('comments.id', '=', 'total.comment_id');
                })
                ->selectRaw('comments.*, sum(total.vote) AS votes')
                ->groupBy('comments.id')
                ->firstOrFail();
        }

        return $this->_record;
    }

    /**
     * create a new comment
     *
     * @param  array $data consists of {body, post_id}
     * @return \stdClass
     */
    public function create($data)
    {
        $user = resolve(AuthUserRepo::class)->user();

        $comment = new Comment;
        $comment->body = $data['body'];
        $comment->post_id = $data['post_id'];
        $comment->user_id = $user->id;
        $comment->save();

        $comment->votes = 0;

        return $comment;
    }

    /**
     * update the given comment
     *
     * @param  int $id the id of the comment
     * @param  string $body
     * @return \stdClass
     */
    public function update($id, $body)
    {
        $comment = $this->getCommentWithVotes($id);
        $comment->body = $body;
        $comment->save();

        return $comment;
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
     * the authenticated user unvote && vote up|down the given comment id
     *
     * @param  int $id the id of the comment
     * @param  int $value 1 => up & -1 => down & 0 => unvote
     * @return void
     */
    public function vote(int $id, int $value)
    {
        $user = resolve(AuthUserRepo::class)->user();
        $comment = $this->getBy('id', $id);

        if ($value === 0) {
            $comment->votes()->detach([$user->id]);
        }
        else {
            $comment->votes()->syncWithoutDetaching([
                $user->id => ['vote' => $value]
            ]);
        }
    }
}
