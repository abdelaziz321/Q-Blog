<?php

namespace App\Http\Controllers;

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
    /**
     * get top 5|8|5 categories|tags|authors have posts
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function sidebar(TagRepo $tagRepo, UserRepo $userRepo, CategoryRepo $categoryRepo)
    {
        $tags = $tagRepo->getPopularTags(8);
        $authors = $userRepo->getPopularAuthors(5);
        $categories = $categoryRepo->getPopularCategories(5);

        return response()->json([
            'tags'       => TagResource::collection($tags),
            'authors'    => UserResource::collection($authors),
            'categories' => CategoryResource::collection($categories)
        ], 200);
    }

    /**
     * search for categories using their title.
     *
     * @param string $_GET['q'] the string by which we will search
     *
     * @param Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function searchCategories(Request $request, CategoryRepo $categoryRepo)
    {
        $q = $request->validate( ['q' => 'required'] )['q'];
        $categories = $categoryRepo->search($q);

        return CategorySearchResource::collection($categories);
    }

    /**
     * search for tags using their title.
     *
     * @param string $_GET['q'] the string by which we will search
     *
     * @param Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function searchTags(Request $request, TagRepo $tagRepo)
    {
        $q = $request->validate( ['q' => 'required'] )['q'];
        $tags = $tagRepo->search($q);

        return TagResource::collection($tags);
    }


}
