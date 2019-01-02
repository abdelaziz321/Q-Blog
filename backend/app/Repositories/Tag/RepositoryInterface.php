<?php

namespace App\Repositories\Tag;

interface RepositoryInterface
{
    /**
     * get first $limit tags have more posts
     *
     * @param  int $limit
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPopularTags($limit);

    /**
     * get pageinated tags with counting posts
     * also order tags by the number of posts
     *
     * @param  int $limit
     * @param  int $offset
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedTags(int $limit, int $page = 1);

    /**
     * get the $slug tag with counting the posts associated with it.
     *
     * @param  string $slug
     * @return \stdClass
     */
    public function getWithCountPosts(string $slug);

    /**
     * update the given $slug tag
     *
     * @param  string $slug
     * @param  array  $data consists of {name}
     * @return \stdClass
     */
    public function update(string $slug, array $data);

    /**
     * delete the given $slug tag and return its name
     *
     * @param  string $slug
     * @return string $name
     */
    public function delete(string $slug);

    /**
     * search tags using their name
     *
     * @param  string $q the string by which we will search
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function search(string $q);

    /**
     * assign the given $tags names to the given post $id.     
     *
     * @param  array  $givenTags Ex: ['Larave', 'Web Develoment']
     * @return array  contains all the ids of the [inserted and already existed tags]
     */
    public function addTagsIfNotExists(array $givenTags);

    /**
     * remove the given tags if they they aren't assigned to any posts.
     *
     * @param  array  $tagsIds
     * @return void
     */
    public function removeTagsIfRequired(array $tagsIds);
}
