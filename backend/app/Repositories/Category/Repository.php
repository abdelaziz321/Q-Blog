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
     * get the $slug category with the moderator of this category
     *
     * @param  string $slug
     * @return \stdClass
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
     * create a new category and return the category with its moderator
     *
     * @param  array $data consists of {title, slug, description, moderator}
     * @return \stdClass
     */
    public function create($data)
    {
        $data['slug'] = str_slug($data['title'] , '-');
        $category = Category::create($data);
        $category->load('moderator');

        return $category;
    }

    /**
     * update the given $slug category and return the category with the old moderator
     *
     * @param  string $slug
     * @param  array  $data consists of {title, description, moderator}
     * @return array  [$category, $oldModerator]
     */
    public function update(string $slug, array $data)
    {
        $category = $this->getWithModerator($slug);
        $oldModerator = $category->moderator;

        $data['slug'] = str_slug($data['title'] , '-');
        $category->update($data);

        return [$category, $oldModerator];
    }

    /**
     * delete the given $slug category and return its [title, moderator]
     *
     * @param  string $slug
     * @return array [$title, $array]
     */
    public function delete(string $slug)
    {
        $category = $this->getBy('slug', $slug);
        $moderator = $category->moderator;
        $title = $category->title;

        $category->delete();

        return [$title, $moderator];
    }

    /**
     * determine if the given user moderate any existance category.
     *
     * @param  int     $id the user id
     * @return boolean
     */
    public function isModerate(int $id)
    {
        return Category::where('moderator', $id)->count() > 0;
    }

    /**
     * search categories using their title
     *
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
