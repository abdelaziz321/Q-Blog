<?php

namespace App\Repositories\Post;

use App\Post;

class Repository implements RepositoryInterface
{
    /**
     * get pageinated categoryies
     *
     * @param  integer $limit
     * @param  integer $offset
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedCategories($limit, $offset = 0)
    {
        return Category::withCount(['posts' => function ($query) {
                $query->published();
            }])
            ->orderBy('posts_count', 'desc')
            ->take($limit)
            ->skip($offset)
            ->get();
    }
}
