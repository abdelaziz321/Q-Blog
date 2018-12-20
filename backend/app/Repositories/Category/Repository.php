<?php

namespace App\Repositories\Category;

use App\Category;
use App\Repositories\BaseRepository;

class Repository extends BaseRepository implements RepositoryInterface
{
    protected $_model = '\\App\\Category';

    /**
     * get first $limit categories have more posts
     *
     * @param  int $limit
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPopularCategories($limit)
    {
        return Category::withCount(['posts' => function ($query) {
                $query->published();
            }])
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  string $q the string by which we will search
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function search(string $q)
    {
        return Category::where('title', 'like', "%{$q}%")
            ->limit(10)
            ->get();
    }
}
