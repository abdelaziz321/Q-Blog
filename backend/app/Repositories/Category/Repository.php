<?php

namespace App\Repositories\Category;

use App\Category;

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

    /**
     * @param  string $q the string by which we will search
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function searchUsingTitle(string $q)
    {
        return Category::where('title', 'like', "%{$q}%")
            ->limit(10)
            ->get();
    }
}
