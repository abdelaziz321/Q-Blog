<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Http\Resources\CommentResource;
use App\Http\Requests\PostsFilterReuqest;
use App\Http\Resources\PaginatedCollection;
use App\Repositories\Post\RepositoryInterface as PostRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class PostController extends Controller
{
    private $postRepo;

    public function __construct(PostRepo $postRepo)
    {
        $this->postRepo = $postRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PostsFilterReuqest $request)
    {
        $limit = 8;
        $rules = $request->all();

        $posts = $this->postRepo->getSortedPaginatedPosts(
            $rules, $limit, $request->query('page', 1)
        );

        $total = $this->postRepo->getTotalPaginated();

        # PaginatedCollection(resource, collects, repo, per_page)
        return new PaginatedCollection($posts, 'PostRow', $total , $limit);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $post = $this->postRepo->getPostWithItsComments($slug);
        $this->postRepo->increment('views', $post->id);

        return response()->json([
            'post'     => new PostResource($post),
            'comments' => CommentResource::collection($post->comments)
        ], 200);
    }

    /**
     * the authenticated user recommend the given post
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function recommend(string $slug, AuthUserRepo $authUserRepo)
    {
        $post = $this->postRepo->getBy('slug', $slug);

        $authUserRepo->can('recommend', $post);

        $this->postRepo->recommend($post->id);

        return response()->json([
            'message' => "the post has been recommended successfully"
        ], 200);
    }

    /**
     * the authenticated user unrecommend the given post
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function unrecommend(string $slug, AuthUserRepo $authUserRepo)
    {
        $post = $this->postRepo->getBy('slug', $slug);

        $authUserRepo->can('recommend', $post);

        $this->postRepo->unrecommend($post->id);

        return response()->json([
            'message' => "the post has been unrecommended successfully"
        ], 200);
    }
}
