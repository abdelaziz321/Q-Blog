<?php

namespace App\Repositories\Category;

use App\Category;
use App\Repositories\BaseRepository;
use App\Repositories\User\RepositoryInterface as UserRepo;

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
     * get the category $slug with the moderator of this category
     *
     * @param  string $slug
     * @return \App\Category
     */
    public function getWithModerator(string $slug)
    {
        if (empty($this->_record)) {
            $this->_record = Category::with(['moderator'])
                ->withCount('posts')
                ->where('slug', $slug)
                ->firstOrFail();
        }

        return $this->_record;
    }

    /**
     * get pageinated categories with counting posts
     * also get the moderator of each category
     *
     * @param  int $limit
     * @param  int $offset
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedCategoriesWithModeratos(int $limit, int $page = 1)
    {
        $categories = Category::with('moderator')
            ->withCount('posts')
            ->get();

        $this->_total = $categories->count();

        $offset = ($page - 1) * $limit;
        return $categories->slice($offset, $limit);
    }

    /**
     * create a new category
     *
     * @param  array $data consists of {title, slug, description, moderator}
     * @return \App\Category
     */
    public function create($data)
    {
        $category = Category::create($data);
        $category->load('moderator');

        return $category;
    }

    /**
     * update the given $slug category
     *
     * @param  string $slug
     * @param  array  $data consists of {title, slug, description, moderator}
     * @return \App\Category
     */
    public function update(string $slug, array $data)
    {
        $category = $this->getWithModerator($slug);
        $moderator = $category->moderator;

        $category->update($data);

        resolve(UserRepo::class)->setAsRegularUserIfRequired($moderator);

        return $category;
    }

    public function delete(string $slug)
    {
        $category = $this->getBy('slug', $slug);
        $moderator = $category->moderator;

        $category->delete();

        resolve(UserRepo::class)->setAsRegularUserIfRequired($moderator);
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

    /**
     * check if the given $slug exists in the users table.
     * we don't care if the $except slug exists or not.
     *
     * @param  string $slug
     * @param  string $except
     * @return int
     */
    public function checkIfExist(string $slug, string $except = '')
    {
        return Category::where('slug', $slug)
            ->where('slug', '!=', $except)
            ->count();
    }
}
