<?php

namespace App\Repositories\Tag;

use App\Tag;
use App\Repositories\BaseRepository;

class Repository extends BaseRepository implements RepositoryInterface
{
    /**
     * get first $limit tags have more posts
     *
     * @param  int $limit
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPopularTags($limit)
    {
        return Tag::withCount(['posts' => function ($query) {
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
        return Tag::where('name', 'like', "%{$q}%")
            ->limit(10)
            ->get();
    }
}
