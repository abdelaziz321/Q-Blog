<?php

namespace App\Http\Controllers\Admin;

use App\Tag;
use App\Post;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TagRequest;
use App\Http\Resources\PostRowResource;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', Tag::class);

        $tags = Tag::withCount('posts')
                   ->orderBy('posts_count', 'desc')
                   ->paginate(10);

        $tags->getCollection()->transform(function ($tag) {
            return new TagResource($tag);
        });

        return $tags;
    }

    public function getTagPosts($slug)
    {
        $this->authorize('view', Tag::class);

        $tag = Tag::where('slug', $slug)->firstOrFail();
        $posts = Post::with(['author', 'category'])
                     ->withCount(['comments', 'recommendations'])
                     ->join('post_tag', function ($join) use ($tag) {
                          $join->on('post_tag.post_id', '=', 'posts.id')
                               ->where('post_tag.tag_id',  $tag->id);
                     })
                     ->orderBy('id', 'desc')
                     ->paginate(10);

        $posts->getCollection()->transform(function ($post) {
            return new PostRowResource($post);
        });

        return $posts;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $this->authorize('view', Tag::class);

        $tag = Tag::withCount('posts')
                    ->where('slug', $slug)
                    ->firstOrFail();

        return response()->json([
            'tag' => new TagResource($tag)
        ], 200);
    }

    public function update(TagRequest $request, Tag $tag)
    {
        $tag->name = $request->name;
        $tag->slug = str_slug($request->name, '-');
        $tag->save();

        return response()->json([
            'tag' => new TagResource($tag)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        $this->authorize('updateOrDelete', Tag::class);

        $tag->delete();

        return response()->json([
            'message' => "tag '{$tag->name}' has been deleted successfully"
        ], 200);
    }
}
