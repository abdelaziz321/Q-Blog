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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, AuthUserRepo $authUserRepo)
    {
        $authUserRepo->can('viewCategories', 'App\\Category');

        $limit = 10;
        $categories = $this->categoryRepo->getPaginatedCategoriesWithModeratos(
            $limit, $request->query('page', 1)
        );

        $total = $this->categoryRepo->getTotalPaginated();

        # PaginatedCollection(resource, collects, total, per_page)
        return new PaginatedCollection($categories, 'Category', $total , $limit);
    }

    /**
     * get paginated posts of the category $slug
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $slug the slug of the category
     * @return \Illuminate\Http\Response
     */
    public function getCategoryPosts(Request $request, string $slug, PostRepo $postRepo, AuthUserRepo $authUserRepo)
    {
        $authUserRepo->can('viewCategories', '\\App\\Category');

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request, UserRepo $userRepo)
    {
        $data = $request->all();
        $data['slug'] = str_slug($request->title , '-');

        $category = $this->categoryRepo->create($data);
        $userRepo->setAsModerator($category->moderator);

        return response()->json([
            'category'  => new CategoryResource($category)
        ], 200);
    }

    /**
     * get the category $slug with the slug.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug, AuthUserRepo $authUserRepo)
    {
        $authUserRepo->can('viewCategories', 'App\\Category');

        $category = $this->categoryRepo->getWithModerator($slug);

        return response()->json([
          'category' => new CategoryResource($category)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, string $slug, UserRepo $userRepo)
    {
        $data = $request->all();
        $data['slug'] = str_slug($request->title , '-');

        $category = $this->categoryRepo->update($slug, $data);
        $userRepo->setAsModerator($category->moderator);

        return response()->json([
            'category'  => new CategoryResource($category)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $category, AuthUserRepo $authUserRepo)
    {
        $authUserRepo->can('createOrDelete', 'App\\Category');

        $category = $this->categoryRepo->delete($slug);

        return response()->json([
            'message'   => "'{$category->title}' category has been deleted successfully"
        ], 200);
    }
}
