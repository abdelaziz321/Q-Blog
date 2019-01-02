<?php

namespace App\Repositories\Tag;

use App\Tag;
use App\Repositories\BaseRepository;

class Repository extends BaseRepository implements RepositoryInterface
{
    protected $_model = '\\App\\Tag';

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
     * get pageinated tags with counting posts
     * also order tags by the number of posts
     *
     * @param  int $limit
     * @param  int $offset
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedTags(int $limit, int $page = 1)
    {
        $categories = Tag::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->get();

        $this->_total = $categories->count();

        $offset = ($page - 1) * $limit;
        return $categories->slice($offset, $limit);
    }

    /**
     * get the $slug tag with counting the posts associated with it.
     *
     * @param  string $slug
     * @return \stdClass
     */
    public function getWithCountPosts(string $slug)
    {
        if (empty($this->_record)) {
            $this->_record = Tag::withCount('posts')
                ->where('slug', $slug)
                ->firstOrFail();
        }

        return $this->_record;
    }

    /**
     * update the given $slug tag
     *
     * @param  string $slug
     * @param  array  $data consists of {name}
     * @return \stdClass
     */
    public function update(string $slug, array $data)
    {
        $tag = $this->getWithCountPosts($slug);

        $tag->update([
            'name' => $data['name'],
            'slug' => str_slug($data['name'], '-')
        ]);

        return $tag;
    }

    /**
     * delete the given $slug tag and return its name
     *
     * @param  string $slug
     * @return string $name
     */
    public function delete(string $slug)
    {
        $tag = $this->getBy('slug', $slug);
        $name = $tag->name;

        $tag->delete();

        return $name;
    }

    /**
     * search tags using their name
     *
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
     * assign the given $tags names to the given post $id.
     *
     * 1. we will get all tags '$existedTags' from DB that matches the given tags array.
     * 2. we will reduce the '$existedTags' array from the given tags array to get '$newTags'.
     *      - now we have array of new tags that need to be inserted in DB.
     * 3. we will insert the '$newTags' in DB and get their ids.
     * finally, we will return the ids of the inserted tags with the ids of the $existedTags
     *
     * @param  array  $givenTags Ex: ['Larave', 'Web Develoment']
     * @return array  contains all the ids of the [inserted and already existed tags]
     */
    public function addTagsIfNotExists(array $givenTags)
    {
        // this array will contains all the ids of the [inserted and already existed tags]
        $ids = [];

        // 1)
        // we will get the slugs of the $givenTags by generateing array contains
        // elements like this: ['web-develoment' => 'Web Develoment']
        foreach ($givenTags as $tag) {
            $temp[str_slug($tag, '-')] = $tag;
        }
        $givenTags = $temp;

        // we will get the already existed tags.
        $existedTags = Tag::select(['id', 'slug', 'name'])
            ->whereIn('slug', array_keys($givenTags))
            ->get();

        $ids = $existedTags->pluck('id')->all();
        $existedTags = $existedTags->pluck('name', 'slug')->all();

        // 2) $givenTags - $existedTags
        $newTags = array_diff_key($givenTags, $existedTags);

        // 3) insert the new tags and get their ids
        foreach ($newTags as $slug => $name) {
            $tag = Tag::create([
                'name' => $name,
                'slug' => $slug
            ]);

            $ids[] = $tag->id;
        }

        return $ids;
    }

    /**
     * remove the given tags if they they aren't assigned to any posts.
     *
     * @param  array  $tagsIds
     * @return void
     */
    public function removeTagsIfRequired(array $tagsIds)
    {
        $tags = Tag::whereIn('id', $tagsIds)
            ->withCount('posts')
            ->get();

        $freeTags = $tags->filter(function ($tag) {
            return $tag->posts_count === 0;
        });

        $freeTagsIds = $freeTags->pluck('id')->all();
        Tag::destroy($freeTagsIds);
    }
}
