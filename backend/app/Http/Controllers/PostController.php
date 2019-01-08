<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Resources\CommentResource;
use App\Http\Requests\PostsFilterReuqest;
use App\Http\Resources\PaginatedCollection;
use App\Repositories\Comment\RepositoryInterface as CommentRepo;
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
     * get sorted paginated posts
     *
     * @param string $_GET['views']             possible values ==> DESC|ASC
     * @param string $_GET['date']              possible values ==> DESC|ASC
     * @param string $_GET['comments']          possible values ==> DESC|ASC
     * @param string $_GET['recommendations']   possible values ==> DESC|ASC
     * @param string $_GET['title']
     * @param string $_GET['author']
     * @param string $_GET['category']
     * @param array  $_GET['tags']
     *
     * @param PostsFilterReuqest $request
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

        # PaginatedCollection(resource, collects, total, per_page)
        return new PaginatedCollection($posts, 'PostRow', $total , $limit);
    }

    /**
     * Display the specified resource.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug)
    {
        $post = $this->postRepo->getPost($slug, true);

        return response()->json([
            'post' => new PostResource($post)
        ], 200);
    }

    /**
     * get the comments of the given published post.
     *
     * @param  string $slug the slug of the post
     * @return \Illuminate\Http\Response
     */
    public function postComments(Request $request, string $slug, CommentRepo $commentRepo)
    {
        $comments = $commentRepo->getPostComments(
            $slug, 5, $request->query('page', 1), true
        );

        return CommentResource::collection($comments);
    }

    /**
     * the authenticated user recommend|unrecommend the given post depending on
     * the given query parameter `action`.
     *
     * @param  string $_GET['action']  possible values ==> recommend|unrecommend
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function recommendation(Request $request, string $slug, AuthUserRepo $authUserRepo)
    {
        $action = $request->validate([
            'action' => ['required', 'regex:#^(recommend|unrecommend)$#'],
        ])['action'];

        $post = $this->postRepo->getBy('slug', $slug);
        $this->authorize('recommend', $post);

        switch ($action) {
            case 'recommend':
                $this->postRepo->recommendation($post->id, true);
                break;

            case 'unrecommend':
                $this->postRepo->recommendation($post->id, false);
                break;

            default:
                # we fall in a black hole
                return;
        }

        return response()->json([
            'message' => "the post has been {$action}ed successfully"
        ], 200);
    }
}
