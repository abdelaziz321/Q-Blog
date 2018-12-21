<?php

namespace App\Repositories\Post;

use App\Post;
use App\Repositories\BaseRepository;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class Repository extends BaseRepository implements RepositoryInterface
{
    protected $_model = '\\App\\Post';

    /**
     * get pageinated posts sorted according to the given rules array
     * this method return only the published posts
     * the array will be something like:
     * ['views' => 'DESC', 'title' => 'vue']
     *
     * @param  array $rules
     * @param  int   $limit
     * @param  int   $page
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getSortedPaginatedPosts(array $rules, int $limit, int $page = 1)
    {
        $query = Post::query();

        // views
        if (isset($rules['views'])) {
            $query->orderBy('views', $rules['views']);
        }

        // date
        if (isset($rules['date'])) {
            $query->orderBy('id', $rules['date']);
        }

        // comments
        if (isset($rules['comments'])) {
            $query->orderBy('comments_count', $rules['comments']);
        }

        // recommendations
        if (isset($rules['recommendations'])) {
            $query->orderBy('recommendations_count', $rules['recommendations']);
        }

        // title
        if (isset($rules['title'])) {
            $query->where('title', 'LIKE', "%{$rules['title']}%");
        }

        // author
        if (isset($rules['author'])) {
            $query->whereHas('author', function ($query) use ($rules) {
                $query->where('slug', $rules['author']);
            });
        }

        // category
        if (isset($rules['category'])) {
            $query->whereHas('category', function ($query) use ($rules) {
                $query->where('slug', $rules['category']);
            });
        }

        // tags
        if (isset($rules['tags'])) {
            $query->whereHas('tags', function ($query) use ($rules) {
                $tags = \App\Tag::whereIn('slug', $rules['tags'] ?? [])->pluck('id')->all();
                $query->whereIn('id', $tags);
            });
        }

        $posts = $query->with(['category', 'author'])
            ->withCount(['comments', 'recommendations'])
            ->published()
            ->get();

        $this->_total = $posts->count();

        $offset = ($page - 1) * $limit;
        return $posts->slice($offset, $limit);
    }

    /**
     * get pageinated posts - get the published or unPublished posts or both.
     *
     * @param  int  $limit
     * @param  int  $page
     * @param  int  $published  | -1 | published + unPublished |
     *                          |  0 | unPublished             |
     *                          |  1 | published               |
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedPosts(int $limit, int $page = 1, int $published = -1)
    {
        $query = Post::query();

        if ($published == 1) {
            $query->published();
        }
        elseif ($published == 0) {
            $query->unPublished();
        }

        $posts = $query->with(['category', 'author'])
            ->withCount(['comments', 'recommendations'])
            ->orderBy('id', 'desc')
            ->get();

        $this->_total = $posts->count();

        $offset = ($page - 1) * $limit;
        return $posts->slice($offset, $limit);
    }

    /**
     * get pageinated posts includeing the unpublished posts from the given category.
     *
     * @param  string   $slug   slug of a category
     * @param  int      $limit
     * @param  int      $page
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedCategoryPosts(string $slug, int $limit, int $page = 1)
    {
        $posts = Post::with(['category', 'author'])
            ->whereHas('category', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->withCount(['comments', 'recommendations'])
            ->orderBy('id', 'desc')
            ->get();

        $this->_total = $posts->count();

        $offset = ($page - 1) * $limit;
        return $posts->slice($offset, $limit);
    }

    /**
     * get the post $slug with its comments.
     * - the post will have attribute `recommended` to determine if the authenticated user
     * recommended this post or not.
     * - each comment will have attribute `voted` to determine if the authenticated user
     * voted this comment up or down or didn't vote.
     *
     * @param  string $slug
     */
    public function getPostWithItsComments(string $slug)
    {
        return Post::with(['category', 'author', 'tags', 'comments' => function ($query) {
                $query->leftJoin('votes AS total', function ($join) {
                    $join->on('comments.id', '=', 'total.comment_id');
                })
                ->leftJoin('votes AS voted', function ($join) {
                    $join->on('comments.id', '=', 'voted.comment_id')
                        ->where('voted.user_id', auth()->user()->id ?? 0);
                })
                ->selectRaw('comments.*, sum(DISTINCT voted.vote) AS voted, sum(total.vote) AS votes')
                ->groupBy('comments.id');
            }, 'comments.user'])

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
    }

    public function create(array $data)
    {
        $data['author_id'] = auth()->user()->id;
    }

    /**
     * Get the total number of views for all posts
     *
     * @return int
     */
    public function countViews()
    {
        return Post::selectRaw('sum(views) AS views')
            ->first()
            ->views;
    }

    /**
     * increment the field $field in the post that has id $id
     *
     * @param  string $field
     * @param  int    $id
     * @return void
     */
    public function increment(string $field, int $id)
    {
        Post::where('id', $id)->increment($field);
    }

    /**
     * the authenticated user recommend the given post
     *
     * @param  int $postId
     * @return void
     */
    public function recommend($postId)
    {
        $user = resolve(AuthUserRepo::class)->user();

        $post = $this->getBy('id', $postId);
        $post->recommendations()->sync($user->id, false);
    }

    /**
     * the authenticated user unrecommend the given post
     *
     * @param  int $postId
     * @return void
     */
    public function unrecommend($postId)
    {
        $user = resolve(AuthUserRepo::class)->user();

        $post = $this->getBy('id', $postId);
        $post->recommendations()->detach($user->id);
    }
}
