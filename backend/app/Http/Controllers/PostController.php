<?php

namespace App\Http\Controllers;

use App\Post;
use App\Comment;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostRowResource;
use App\Http\Resources\CommentResource;
use \Illuminate\Auth\Access\AuthorizationException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        ########################################################
        #===             It works, don't touch.             ===#
        ########################################################
        $request->validate([
            'views'           => ['regex:#(ASC|DESC)#i'],
            'date'            => ['regex:#(ASC|DESC)#i'],
            'comments'        => ['regex:#(ASC|DESC)#i'],
            'recommendations' => ['regex:#(ASC|DESC)#i']
        ]);

        $query = Post::query();

        // views
        $query->when($request->has('views'), function ($query) use ($request) {
            return $query->orderBy('views', $request->query('views', 'DESC'));
        });
        // date
        $query->when($request->has('date'), function ($query) use ($request) {
            return $query->orderBy('id', $request->query('date', 'DESC'));
        });
        // comments
        $query->when($request->has('comments'), function ($query) use ($request) {
            return $query->orderBy('comments_count', $request->query('comments', 'DESC'));
        });
        // recommendations
        $query->when($request->has('recommendations'), function ($query) use ($request) {
            return $query->orderBy('recommendations_count', $request->query('recommendations', 'DESC'));
        });
        // title
        $query->when($request->has('title'), function ($query) use ($request) {
            return $query->where('title', 'LIKE', "%{$request->query('title', '')}%");
        });
        // author
        $query->when($request->has('author'), function ($query) use ($request) {
            return $query->whereHas('author', function ($query) use ($request) {
                $query->where('slug', $request->query('author', ''));
            });
        });
        // category
        $query->when($request->has('category'), function ($query) use ($request) {
            return $query->whereHas('category', function ($query) use ($request) {
                $query->where('slug', $request->query('category', ''));
            });
        });
        // tags
        $query->when($request->has('tags'), function ($query) use ($request) {
            return $query->whereHas('tags', function ($query) use ($request) {
                $tags = \App\Tag::whereIn('slug', $request->query('tags', ''))->get();
                $tagsIds = $tags->pluck('id')->all();
                $query->whereIn('id', $tagsIds);
            });
        });

        $posts = $query->with(['category', 'author'])
                       ->withCount(['comments', 'recommendations'])
                       ->published()
                       ->paginate(8);

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
        $post = Post::with(['category', 'author', 'tags'])
                    ->withCount(['comments', 'recommendations'])
                    ->leftJoin('recommendations AS recommended', function ($join) {
                        $join->on('posts.id', '=', 'recommended.post_id')
                             ->where('recommended.user_id', auth()->user()->id ?? 0);
                    })
                    ->selectRaw('count(DISTINCT recommended.user_id) AS recommended')
                    ->groupBy('posts.id')
                    ->where('slug', $slug)
                    ->published()
                    ->firstOrFail();

        $post->increment('views');

        $comments = Comment::with(['user'])
                            ->leftJoin('votes AS total', function ($join) {
                                $join->on('comments.id', '=', 'total.comment_id');
                            })
                            ->leftJoin('votes AS voted', function ($join) {
                                $join->on('comments.id', '=', 'voted.comment_id')
                                     ->where('voted.user_id', auth()->user()->id ?? 0);
                            })
                            ->selectRaw('comments.*, sum(DISTINCT voted.vote) AS voted, sum(total.vote) AS votes')
                            ->groupBy('comments.id')
                            ->where('post_id', $post->id)
                            ->get();

        $comments = $comments->transform(function ($comment) {
            return new CommentResource($comment);
        });

        return response()->json([
            'post' => new PostResource($post),
            'comments' => $comments
        ], 200);
    }

    public function recommend(Post $post)
    {
        if(auth()->user()->isBanned()) {
            throw new AuthorizationException("Error");
        }

        $post->recommendations()->attach([auth()->user()->id]);

        return response()->json([
            'message' => "you recommended '{$post->title}' post successfully"
        ], 200);
    }

    public function unrecommend(Post $post)
    {
        $post->recommendations()->detach(auth()->user()->id);

        return response()->json([
            'message' => "you unrecommended '{$post->title}' post successfully"
        ], 200);
    }
}
