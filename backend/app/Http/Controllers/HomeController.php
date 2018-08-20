<?php

namespace App\Http\Controllers;

use App\Tag;
use App\User;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategorySearchResource;

class HomeController extends Controller
{
    /**
     * get top 5|8|5 categories|tags|authors have posts
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function sidebar()
    {
        $tags = Tag::withCount(['posts' => function ($query) {
	                     $query->published();
                   }])
                   ->orderBy('posts_count', 'desc')
                   ->take(8)
                   ->get();

        $authors = User::where('privilege', '>', 1)
                       ->withCount(['posts' => function ($query) {
		                       $query->published();
		                   }])
                       ->orderBy('posts_count', 'desc')
                       ->take(5)
                       ->get();

       $categories = Category::withCount(['posts' => function ($query) {
                                 $query->published();
	                           }])
                             ->orderBy('posts_count', 'desc')
                             ->take(5)
                             ->get();

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
        $categories = Category::where('title', 'like', "%{$q}%")
                     ->get();


        $categories->transform(function ($category) {
            return new CategorySearchResource($category);
        });

        return $categories;
    }

    /**
     * search for tags using their title.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function searchTags(Request $request)
    {
        $q = $request->query('q');
        $tags = Tag::where('name', 'like', "%{$q}%")->get();

        $tags->transform(function ($tag) {
            return new TagResource($tag);
        });

        return $tags;
    }


}
