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

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewPosts', Post::class);

        $posts = Post::with(['category', 'author'])
                     ->withCount(['comments', 'recommendations'])
                     ->orderBy('id', 'desc')
                     ->paginate(15);

        $posts->getCollection()->transform(function ($post) {
            return new PostRowResource($post);
        });

        return $posts;
    }

    public function unPublishedPosts()
    {
        $this->authorize('viewPosts', Post::class);

        $posts = Post::with(['category', 'author'])
                     ->withCount(['comments', 'recommendations'])
                     ->orderBy('id', 'desc')
                     ->unPublished()
                     ->paginate(15);

        $posts->getCollection()->transform(function ($post) {
            return new PostRowResource($post);
        });

        return $posts;
    }

    public function postComments($slug)
    {
        $this->authorize('viewPosts', Post::class);

        $post = Post::with(['comments.user', 'comments.votes'])
                    ->where('slug', $slug)
                    ->firstOrFail();

        $comments = $post->comments->transform(function ($comment) {
            return new CommentResource($comment);
        });

        return $comments;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $data = $request->all();

        $data['slug'] = str_slug($request->title , '-');
        $data['author_id'] = auth()->user()->id;

        // handling the uploaded photo
        if ($request->hasFile('caption') && $request->file('caption')->isValid()) {
            $caption = $request->file('caption');
            $captionName = time() . '-' . $caption->getClientOriginalName();
            $data['caption'] = $captionName;
            Image::make($caption)->fit(800, 500)->save(public_path('storage/posts/') . $captionName);
        }

        $post = Post::create($data);
        $post->addTags($data['tags']);
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
