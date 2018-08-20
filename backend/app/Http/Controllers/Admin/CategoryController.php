<?php

namespace App\Http\Controllers\Admin;

use App\Post;
use App\User;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostRowResource;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Resources\CategorySearchResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewCategories', Category::class);

        $categories = Category::with('moderator')
                              ->withCount('posts')
                              ->paginate(5);

        $categories->getCollection()->transform(function ($category) {
            return new CategoryResource($category);
        });

        return $categories;
    }

    public function getCategoryPosts($slug)
    {
        $this->authorize('viewCategories', Category::class);

        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Post::with('author')
                     ->withCount(['comments', 'recommendations'])
                     ->where('category_id', $category->id)
                     ->orderBy('id', 'desc')
                     ->paginate(10);

        $posts->getCollection()->transform(function ($post) {
            return new PostRowResource($post);
        });

        return $posts;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $data = $request->all();
        $data['slug'] = str_slug($request->title , '-');

        $category = Category::create($data);
        $category->load('moderator');

        User::where('id', $category->moderator)->update(['privilege' => 3]);

        return response()->json([
            'category'  => new CategoryResource($category)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $this->authorize('viewCategories', Category::class);

        $category = Category::with(['moderator'])
                            ->withCount('posts')
                            ->where('slug', $slug)
                            ->firstOrFail();

        return response()->json([
          'category' => new CategoryResource($category)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->all();

        // if there is no category related to this moderator so he is now a regular user.
        $moderator = User::where('id', $category->moderator)->firstOrFail();
        $data['slug'] = str_slug($request->title , '-');
        $category->update($data);
        
        if (!$moderator->category()->exists()) {
            # you are fired
            $moderator->privilege = 1;
            $moderator->save();
        }

        $category->load('moderator');

        User::where('id', $category->moderator)->update(['privilege' => 3]);

        return response()->json([
            'category'  => new CategoryResource($category)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Category $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $this->authorize('createOrDelete', Category::class);

        $moderator = User::where('id', $category->moderator)->firstOrFail();

        $category->delete();
        // if there is no category related to this moderator so he is now a regular user.
        if (!$moderator->category()->exists()) {
            # you are fired
            $moderator->privilege = 1;
            $moderator->save();
        }

        return response()->json([
            'message'   => "'{$category->title}' category has been deleted successfully"
        ], 200);
    }
}
