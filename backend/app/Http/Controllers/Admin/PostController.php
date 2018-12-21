<?php

namespace App\Http\Controllers\Admin;

use Image;
use App\Post;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostRowResource;
use App\Http\Resources\CommentResource;
use App\Http\Requests\Admin\PostRequest;
use App\Http\Resources\PaginatedCollection;
use App\Repositories\Post\RepositoryInterface as PostRepo;
use App\Repositories\Comment\RepositoryInterface as CommentRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class PostController extends Controller
{
    private $postRepo;

    public function __construct(PostRepo $postRepo)
    {
        $this->postRepo = $postRepo;
    }

    /**
     * get pageinated posts - the published and unPublished posts.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, AuthUserRepo $authUserRepo)
    {
        $authUserRepo->can('viewPosts', 'App\\Post');

        $limit = 15;
        $posts = $this->postRepo->getPaginatedPosts(
            $limit, $request->query('page', 1)
        );

        $total = $this->postRepo->getTotalPaginated();

        # PaginatedCollection(resource, collects, total, per_page)
        return new PaginatedCollection($posts, 'PostRow', $total , $limit);
    }

    /**
     * get pageinated posts - the unPublished posts.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function unPublishedPosts(Request $request, AuthUserRepo $authUserRepo)
    {
        $authUserRepo->can('viewPosts', 'App\\Post');

        $limit = 15;
        $posts = $this->postRepo->getPaginatedPosts(
            $limit, $request->query('page', 1), 0
        );

        $total = $this->postRepo->getTotalPaginated();

        # PaginatedCollection(resource, collects, total, per_page)
        return new PaginatedCollection($posts, 'PostRow', $total , $limit);
    }

    /**
     * get the comments of the given post.
     *
     * @param  string $slug the slug of the post
     * @return \Illuminate\Http\Response
     */
    public function postComments(string $slug, AuthUserRepo $authUserRepo, CommentRepo $commentRepo)
    {
        $authUserRepo->can('viewPosts', 'App\\Post');

        $comments = $commentRepo->getPostComments($slug);

        return CommentResource::Collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request, TagRepo $tagRepo)
    {
        $data = $request->all();
        $data['slug'] = str_slug($request->title , '-');



        // handling the uploaded photo
        if ($request->hasFile('caption') && $request->file('caption')->isValid()) {
            $caption = $request->file('caption');
            $captionName = time() . '-' . $caption->getClientOriginalName();
            $data['caption'] = $captionName;
            Image::make($caption)->fit(800, 500)->save(public_path('storage/posts/') . $captionName);
        }


        $tagsIds = $tagRepo->insertTagsIfNotExists();

        $post = Post::create($data);

        $post->load(['category', 'author', 'tags']);

        return response()->json([
            'post' => new PostResource($post)
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
        $this->authorize('viewPosts', Post::class);

        $post = Post::with(['category', 'author', 'tags'])
                    ->withCount(['comments', 'recommendations'])
                    ->where('slug', $slug)
                    ->firstOrFail();

        return response()->json([
            'post' => new PostResource($post)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        $data = $request->all();
        $data['slug'] = str_slug($request->title , '-');

        // handling the uploaded photo
        if ($request->hasFile('caption') && $request->file('caption')->isValid()) {
            # delete the previos caption if exists
            if ($post->caption != null) {
                unlink(public_path('storage/posts/') . $post->caption);
            }
            $caption = $request->file('caption');
            $captionName = time() . '-' . $caption->getClientOriginalName();
            $data['caption'] = $captionName;
            Image::make($caption)->fit(800, 500)->save(public_path('storage/posts/') . $captionName);
        } else {
            unset($data['caption']);
        }

        $post->update($data);
        $post->addTags($data['tags']);
        $post->load(['category', 'author', 'tags']);

        return response()->json([
            'post' => new PostResource($post)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();
        if ($post->caption) {
          unlink(public_path('storage/posts/') . $post->caption);
        }

        return response()->json([
            'message' => "'{$post->title}' post has been deleted successfully"
        ], 200);
    }

    public function publish(Post $post)
    {
        $this->authorize('publish', $post);

        $post->published = 1;
        $post->published_at = now()->toDateTimeString();
        $post->save();

        return response()->json([
            'message' => "'{$post->title}' post has been published successfully"
        ], 200);
    }

    public function unpublish(Post $post)
    {
        $this->authorize('publish', $post);

        $post->published = 0;
        $post->published_at = now()->toDateTimeString();
        $post->save();

        return response()->json([
            'message' => "'{$post->title}' post has been unPublished successfully"
        ], 200);
    }

    public function assignTags(Request $request, Post $post)
    {
        $this->authorize('assignTags', $post);

        $post->addTags($request->tags);
        $post->load(['category', 'author', 'tags']);

        return response()->json([
            'post'      => new PostResource($post)
        ], 200);
    }
}
