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
                $query->whereIn('slug', $rules['tags']);
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
     * get pageinated posts - get the published or unPublished posts or both dependeing
     * on the $published varibale.
     * | val |      === result ===      |
     * | -1  | published && unPublished |
     * |  0  |       unPublished        |
     * |  1  |        published         |
     *
     * @param  int  $limit
     * @param  int  $page
     * @param  int  $published
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
     * get pageinated posts includeing the unpublished posts for the given category.
     *
     * @param  string   $slug   the slug of the category
     * @param  int      $limit
     * @param  int      $page
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedCategoryPosts(string $slug, int $limit, int $page = 1)
    {
        $posts = $this->getPainatedPostsFor('category', $slug);

        $this->_total = $posts->count();

        $offset = ($page - 1) * $limit;
        return $posts->slice($offset, $limit);

    }

    /**
     * get pageinated posts includeing the unpublished posts for the given tag.
     *
     * @param  string   $slug   the slug of the tag
     * @param  int      $limit
     * @param  int      $page
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedTagPosts(string $slug, int $limit, int $page = 1)
    {
        $posts = $this->getPainatedPostsFor('tags', $slug);

        $this->_total = $posts->count();

        $offset = ($page - 1) * $limit;
        return $posts->slice($offset, $limit);

    }

    /**
     * get pageinated posts includeing the unpublished posts for the given user.
     *
     * @param  string   $slug   the slug of the user
     * @param  int      $limit
     * @param  int      $page
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedUserPosts(string $slug, int $limit, int $page = 1)
    {
        $posts = $this->getPainatedPostsFor('author', $slug);

        $this->_total = $posts->count();

        $offset = ($page - 1) * $limit;
        return $posts->slice($offset, $limit);

    }

    /**
     * get pageinated posts includeing the unpublished posts for the given relation
     *
     * @param  string $relation
     * @param  string $slug
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function getPainatedPostsFor(string $relation, string $slug)
    {
        return Post::with(['category', 'author'])
            ->withCount(['comments', 'recommendations'])
            ->whereHas($relation, function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get the post $slug with its relations 'category', 'author', 'tags'...
     * - the post will have attribute `recommended` to determine if the
     * authenticated user recommended this post or not.
     * - if the $published variable = true, the method will return the post
     * only if it was published
     *
     * @param  string  $slug
     * @param  boolean $published 1 => get only if published
     * @return \stdClass
     */
    public function getPost(string $slug, bool $published = false)
    {
        if (empty($this->_record)) {
            $query = Post::query();

            if ($published) {
                $query->published();
            }

            $userId = resolve(AuthUserRepo::class)->user()->id ?? 0;

            $this->_record = $query->with(['category', 'author', 'tags'])
                ->withCount(['comments', 'recommendations'])
                ->where('slug', $slug)
                ->leftJoin('recommendations AS recommended', function ($join) use ($userId) {
                    $join->on('posts.id', '=', 'recommended.post_id')
                    ->where('recommended.user_id', $userId);
                })
                ->selectRaw('count(DISTINCT recommended.user_id) AS recommended')
                ->groupBy('posts.id')
                ->firstOrFail();
        }

        return $this->_record;
    }

    /**
     * create a new post
     *
     * @param  array $data consists of {title, body, category_id, caption}
     * @return \stdClass
     */
    public function create(array $data)
    {
        $this->_record = Post::create([
            'slug'        => str_slug($data['title'], '-'),
            'title'       => $data['title'],
            'body'        => $data['body'],
            'caption'     => $data['caption'],
            'author_id'   => resolve(AuthUserRepo::class)->user()->id,
            'category_id' => $data['category_id']
        ]);

        return $this->_record;
    }

    /**
     * update the given post.
     *
     * @param  string $slug
     * @param  array  $data consists of {title, body, category_id, caption}
     * @return array  [$category, $oldModerator]
     */
    public function update(string $slug, array $data)
    {
        $post = $this->getBy('slug', $slug);

        $post->title = $data['title'];
        $post->slug = str_slug($data['title'], '-');
        $post->body = $data['body'];
        $post->category_id = $data['category_id'];

        if (isset($data['caption'])) {
            $post->caption = $data['caption'];
        }

        $post->save();

        return $post;
    }

    /**
     * delete the given post
     *
     * @param  int   $id
     * @return void
     */
    public function delete(int $id)
    {
        Post::where('id', $id)->delete();
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
     * the authenticated user recommend|unrecommend the given post
     * depending on the value of the boolean variable $recommend
     * true  => recommend the post
     * false => unrecommend the post
     *
     * @param int  $id the id of the post we want to recommend|unrecommend
     * @param bool $recommend
     * @return void
     */
    public function recommendation(int $id, bool $recommend)
    {
        $user = resolve(AuthUserRepo::class)->user();
        $post = $this->getBy('id', $id);

        if ($recommend) {
            $post->recommendations()->sync($user->id, false);
        }
        else {
            $post->recommendations()->detach($user->id);
        }
    }

    /**
     * publish|unpublish the given post depending on the given bool variable $publish
     * true  => publish the post
     * false => unpublish the post
     *
     * @param int     $id       the id of the post
     * @param boolean $publish
     * @return void
     */
    public function publishing(int $id, bool $publish)
    {
        $post = $this->getBy('id', $id);

        $post->published = $publish;
        $post->published_at = ($publish) ? now()->toDateTimeString() : null;

        $post->save();
    }

    /**
     * assign the given tags id to the given post.
     *
     * @param  array  $tags the ids of the tags
     * @param  int    $id   the id of the post
     * @return void
     */
    public function assignTagsTo(array $tags, int $id)
    {
        $post = $this->getBy('id', $id);
        $post->tags()->sync($tags);

        $post->load('tags');
        return $post;
    }

    /**
     * get array of the ids of the given post'tags
     *
     * @param  int    $id the id of the post
     * @return array
     */
    public function getPostTagsIds(int $id)
    {
        $post = $this->getBy('id', $id);
        $post->loadMissing('tags');

        return $post->tags->pluck('id')->all();
    }
}
