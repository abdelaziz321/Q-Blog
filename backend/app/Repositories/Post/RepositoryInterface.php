<?php

namespace App\Repositories\Post;

interface RepositoryInterface
{
    /**
     * get pageinated posts sorted according to the given rules array
     * this method return only the published posts
     * the array will be something like:
     * ['views' => 'DESC', 'title' => 'vue']
     *
     * @param  array $rules
     * @param  int   $limit
     * @param  int   $page
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getSortedPaginatedPosts(array $rules, int $limit, int $page = 1);

    /**
     * get pageinated posts - get the published or unPublished posts or both dependeing
     * on the $published varibale.
     * | val |      === result ===      |
     * | -1  | published && unPublished |
     * |  0  |       unPublished        |
     * |  1  |        published         |
     *
     * @param  int  $limit
     * @param  int  $page
     * @param  int  $published
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedPosts(int $limit, int $page = 1, int $published = -1);

    /**
     * get pageinated posts includeing the unpublished posts for the given category.
     *
     * @param  string   $slug   the slug of the category
     * @param  int      $limit
     * @param  int      $page
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedCategoryPosts(string $slug, int $limit, int $page = 1);

    /**
     * get pageinated posts includeing the unpublished posts for the given tag.
     *
     * @param  string   $slug   the slug of the tag
     * @param  int      $limit
     * @param  int      $page
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedTagPosts(string $slug, int $limit, int $page = 1);

    /**
     * get pageinated posts includeing the unpublished posts for the given user.
     *
     * @param  string   $slug   the slug of the user
     * @param  int      $limit
     * @param  int      $page
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedUserPosts(string $slug, int $limit, int $page = 1);

    /**
     * Get the post $slug with its relations 'category', 'author', 'tags'...
     * - the post will have attribute `recommended` to determine if the
     * authenticated user recommended this post or not.
     * - if the $published variable = true, the method will return the post
     * only if it was published
     *
     * @param  string  $slug
     * @param  boolean $published 1 => get only if published
     * @return \stdClass
     */
    public function getPost(string $slug, bool $published = false);

    /**
     * create a new post
     *
     * @param  array $data consists of {title, body, category_id, caption}
     * @return \stdClass
     */
    public function create(array $data);

    /**
     * update the given post.
     *
     * @param  string $slug
     * @param  array  $data consists of {title, body, category_id, caption}
     * @return array  [$category, $oldModerator]
     */
    public function update(string $slug, array $data);

    /**
     * delete the given post
     *
     * @param  int   $id
     * @return void
     */
    public function delete(int $id);

    /**
     * Get the total number of views for all posts
     *
     * @return int
     */
    public function countViews();

    /**
     * increment the field $field in the post that has id $id
     *
     * @param  string $field
     * @param  int    $id
     * @return void
     */
    public function increment(string $field, int $id);

    /**
     * the authenticated user recommend|unrecommend the given post
     * depending on the value of the boolean variable $recommend
     * true  => recommend the post
     * false => unrecommend the post
     *
     * @param int  $id the id of the post we want to recommend|unrecommend
     * @param bool $recommend
     * @return void
     */
    public function recommendation(int $id, bool $recommend);

    /**
     * publish|unpublish the given post depending on the given bool variable $publish
     * true  => publish the post
     * false => unpublish the post
     *
     * @param int     $id       the id of the post
     * @param boolean $publish
     * @return void
     */
    public function publishing(int $id, bool $publish);

    /**
     * assign the given tags id to the given post.
     *
     * @param  array  $tags the ids of the tags
     * @param  int    $id   the id of the post
     * @return void
     */
    public function assignTagsTo(array $tags, int $id);

    /**
     * get array of the ids of the given post'tags
     *
     * @param  int    $id the id of the post
     * @return array
     */
    public function getPostTagsIds(int $id);
}
