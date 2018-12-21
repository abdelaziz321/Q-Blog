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

    /**
     * 1. we will get the tags from DB that matches the given tags array
     * 2. we will reduce the extracted tags array from the given tags array
     * 3. we will insert the result tags array into the DB && get the inserted ids
     * 4. we will use sync to build the relationship between the posts & tags
     */
    public function addTags($arr)
    {
        $arr = (array) $arr;
        $tagsIds = [];
        $tags = [];

        foreach ($arr as $value) {
            $tags[str_slug($value)] = $value;
        }

        #1 slugs of the already existing tags
        $exist = \DB::table('tags')->select(['id', 'slug'])->whereIn('slug', array_keys($tags))->get();
        $exist->transform(function ($item) use (&$tagsIds) {
            $tagsIds[] = $item->id;
            return $item->slug;
        });

        #2 slugs of the received new tags
        $result = array_diff(array_keys($tags), $exist->toArray());

        #3 array of (name, slug) for the received new tags
        $prepareTagsArr = array_map(function ($item) use ($tags) {
            return [
                'name' => $tags[$item],
                'slug' => $item
            ];
        }, $result);

        $insertedTags = $this->tags()->createMany(array_values($prepareTagsArr));

        #4 the last modification for the $tagsIds
        array_map(function ($item) use (&$tagsIds) {
            $tagsIds[] = $item->id;
        }, $insertedTags);

        $this->tags()->sync($tagsIds);
    }

    public function insertTagsIfNotExists(array $tags)
    {
        # insert
    }
}
