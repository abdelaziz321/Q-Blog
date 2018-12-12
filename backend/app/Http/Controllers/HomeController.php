<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Category;

use Illuminate\Http\Request;

use App\Http\Resources\TagResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategorySearchResource;

use App\Repositories\Tag\RepositoryInterface as TagRepo;
use App\Repositories\User\RepositoryInterface as UserRepo;
use App\Repositories\Category\RepositoryInterface as CategoryRepo;

class HomeController extends Controller
{
    private $tagRepo;
    private $userRepo;
    private $categoryRepo;

    public function __construct(TagRepo $tagRepo, UserRepo $userRepo, CategoryRepo $categoryRepo)
    {
        $this->tagRepo = $tagRepo;
        $this->userRepo = $userRepo;
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * get top 5|8|5 categories|tags|authors have posts
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function sidebar()
    {
        $tags = $this->tagRepo->getPaginatedTags(8);
        $authors = $this->userRepo->getPaginatedUsers(5);
        $categories = $this->categoryRepo->getPaginatedCategories(5);

        return response()->json([
            'tags'       => TagResource::collection($tags),
            'authors'    => UserResource::collection($authors),
            'categories' => CategoryResource::collection($categories)
        ], 200);
    }

    /**
     * search for categories using their title.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function searchCategories(Request $request)
    {
        $q = $request->query('q');
        $categories = $this->categoryRepo->searchUsingTitle($q);

        return CategorySearchResource::collection($categories);
    }

    /**
     * search for tags using their title.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function searchTags(Request $request)
    {
        $q = $request->query('q');
        $tags = $this->tagRepo->searchUsingTitle($q);

        return TagResource::collection($tags);
    }


}
