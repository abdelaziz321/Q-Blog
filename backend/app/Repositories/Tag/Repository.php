<?php

namespace App\Repositories\Tag;

use App\Tag;

class Repository implements RepositoryInterface
{
    /**
     * get pageinated tags
     *
     * @param  integer $limit
     * @param  integer $offset
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedTags($limit, $offset = 0)
    {
        return Tag::withCount(['posts' => function ($query) {
                $query->published();
            }])
           ->orderBy('posts_count', 'desc')
           ->take($limit)
           ->skip($offset)
           ->get();
    }

    /**
     * @param  string $q the string by which we will search
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function searchUsingTitle(string $q)
    {
        return Tag::where('name', 'like', "%{$q}%")
            ->limit(10)
            ->get();
    }
}
