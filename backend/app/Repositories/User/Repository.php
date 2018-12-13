<?php

namespace App\Repositories\User;

use App\User;

class Repository implements RepositoryInterface
{
    /**
     * the authenticated user
     */
    private $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * get pageinated unbanned users
     *
     * @param  integer $limit
     * @param  integer $offset
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedUsers($limit, $offset = 0)
    {
        return User::withCount(['posts' => function ($query) {
                $query->published();
            }])
            ->where('privilege', '>', 1)
            ->orderBy('posts_count', 'desc')
            ->take($limit)
            ->skip($offset)
            ->get();
    }

    /**
     * the authenticated user recommend the given post
     *
     * @param  string $slug
     * @return void
     */
    public function recommend(string $slug)
    {
        $id = \App\Post::where('slug', $slug)->pluck('id')->all();
        $this->user->recommendations()->sync($id, false);
    }

    /**
     * the authenticated user unrecommend the given post
     *
     * @param  string $slug
     * @return void
     */
    public function unrecommend(string $slug)
    {
        $id = \App\Post::where('slug', $slug)->pluck('id')->all();
        $this->user->recommendations()->detach($id);
    }

}
