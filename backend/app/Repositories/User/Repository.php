<?php

namespace App\Repositories\User;

use App\User;

class Repository implements RepositoryInterface
{
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
}
