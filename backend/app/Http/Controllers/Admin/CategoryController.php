<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Resources\PaginatedCollection;
use App\Repositories\Post\RepositoryInterface as PostRepo;
use App\Repositories\User\RepositoryInterface as UserRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;
use App\Repositories\Category\RepositoryInterface as CategoryRepo;

class CategoryController extends Controller
{
    private $categoryRepo;

    public function __construct(CategoryRepo $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * get paginated categories with moderators
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, AuthUserRepo $authUserRepo)
    {
        $this->authorize('viewCategories', 'App\\Category');

        $limit = 10;
        $categories = $this->categoryRepo->getPaginatedCategoriesWithModeratos(
            $limit, $request->query('page', 1)
        );

        $total = $this->categoryRepo->getTotalPaginated();

        # PaginatedCollection(resource, collects, total, per_page)
        return new PaginatedCollection($categories, 'Category', $total , $limit);
    }

    /**
     * get paginated posts of the given category
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $slug the slug of the category
     * @return \Illuminate\Http\Response
     */
    public function getCategoryPosts(Request $request, string $slug, PostRepo $postRepo, AuthUserRepo $authUserRepo)
    {
        $this->authorize('viewCategories', 'App\\Category');

        $limit = 8;
        $posts = $postRepo->getPaginatedCategoryPosts(
            $slug, $limit, $request->query('page', 1)
        );

        $total = $postRepo->getTotalPaginated();

        # PaginatedCollection(resource, collects, total, per_page)
        return new PaginatedCollection($posts, 'PostRow', $total , $limit);
    }

    /**
     * create a new category.
     *
     * @param $_POST['title']
     * @param $_POST['description']
     * @param $_POST['moderator']
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request, UserRepo $userRepo)
    {
        $data = $request->all();

        $category = $this->categoryRepo->create($data);
        $userRepo->setAsModeratorIfNotAdmin($category->moderator);

        return response()->json([
            'category'  => new CategoryResource($category)
        ], 200);
    }

    /**
     * get the $slug category with its moderator.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug, AuthUserRepo $authUserRepo)
    {
        $this->authorize('viewCategories', 'App\\Category');

        $category = $this->categoryRepo->getWithModerator($slug);

        return response()->json([
          'category' => new CategoryResource($category)
        ]);
    }

    /**
     * update the given $slug category also:
     * - update the privilege of the new moderator if not admin
     * - set the privilege of the old moderator as regular user if he doesn't
     * moderate other categories
     *
     * @param $_POST['title']
     * @param $_POST['description']
     * @param $_POST['moderator']
     *
     * @param  CategoryRequest  $request
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, string $slug, UserRepo $userRepo)
    {
        $data = $request->all();

        list($category, $oldModerator) = $this->categoryRepo->update($slug, $data);

        $userRepo->setAsRegularUserIfRequired($oldModerator);
        $userRepo->setAsModeratorIfNotAdmin($category->moderator);

        return response()->json([
            'category'  => new CategoryResource($category)
        ], 200);
    }

    /**
     * delete the given $slug category and set the privilege of its moderator
     * as regular user if he is not an admin or moderate other categories.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug, AuthUserRepo $authUserRepo)
    {
        $this->authorize('createOrDelete', 'App\\Category');

        list($title, $moderator) = $this->categoryRepo->delete($slug);

        $authUserRepo->setAsRegularUserIfRequired($moderator);

        return response()->json([
            'message'   => "'{$title}' category has been deleted successfully"
        ], 200);
    }
}
