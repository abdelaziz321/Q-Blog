<?php

namespace App\Repositories\Comment;

interface RepositoryInterface
{
    /**
     * get pageinated comments with the total votes
     *
     * @param  int $limit
     * @param  int $page
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedComments($limit, $page);

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
    public function getPostComments(string $slug, int $limit, int $page, bool $published = false);

    /**
     * get the comment of the given id with count the votes
     *
     * @param  int    $id the id of the comment
     * @return \stdClass
     */
    public function getCommentWithVotes(int $id);

    /**
     * create a new comment
     *
     * @param  array $data consists of {body, post_id}
     * @return \stdClass
     */
    public function create($data);

    /**
     * update the given comment
     *
     * @param  int $id the id of the comment
     * @param  string $body
     * @return \stdClass
     */
    public function update($id, $body);

    /**
     * delete the given comment
     *
     * @param  int   $id
     * @return void
     */
    public function delete(int $id);

    /**
     * the authenticated user unvote && vote up|down the given comment id
     *
     * @param  int $id the id of the comment
     * @param  int $value 1 => up & -1 => down & 0 => unvote
     * @return void
     */
    public function vote(int $id, int $value);
}
