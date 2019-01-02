<?php

namespace App\Repositories\Category;

interface RepositoryInterface
{
    /**
     * get first $limit categories have more posts
     *
     * @param  int $limit
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPopularCategories($limit);

    /**
     * get pageinated categories with counting posts
     * also get the moderator of each category
     *
     * @param  int $limit
     * @param  int $offset
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedCategoriesWithModeratos(int $limit, int $page = 1);

    /**
     * get the $slug category with the moderator of this category
     *
     * @param  string $slug
     * @return \stdClass
     */
    public function getWithModerator(string $slug);

    /**
     * create a new category and return the category with its moderator
     *
     * @param  array $data consists of {title, slug, description, moderator}
     * @return \stdClass
     */
    public function create($data);

    /**
     * update the given $slug category and return the category with the old moderator
     *
     * @param  string $slug
     * @param  array  $data consists of {title, description, moderator}
     * @return array  [$category, $oldModerator]
     */
    public function update(string $slug, array $data);

    /**
     * delete the given $slug category and return its [title, moderator]
     *
     * @param  string $slug
     * @return array [$title, $array]
     */
    public function delete(string $slug);

    /**
     * determine if the given user moderate any existance category.
     *
     * @param  int     $id the user id
     * @return boolean
     */
    public function isModerate(int $id);

    /**
     * search categories using their title
     *
     * @param  string $q the string by which we will search
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function search(string $q);
}
