<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TagRequest;
use App\Http\Resources\PaginatedCollection;
use App\Repositories\Tag\RepositoryInterface as TagRepo;
use App\Repositories\Post\RepositoryInterface as PostRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class TagController extends Controller
{
    private $tagRepo;

    public function __construct(TagRepo $tagRepo)
    {
        $this->tagRepo = $tagRepo;
    }

    /**
     * get paginated tags with count of posts
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, AuthUserRepo $authUserRepo)
    {
        $this->authorize('view', 'App\\Tag');

        $limit = 10;
        $tags = $this->tagRepo->getPaginatedTags(
            $limit, $request->query('page', 1)
        );

        $total = $this->tagRepo->getTotalPaginated();

        # PaginatedCollection(resource, collects, total, per_page)
        return new PaginatedCollection($tags, 'Tag', $total , $limit);
    }

    /**
     * get paginated posts of the given tag
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $slug the slug of the category
     * @return \Illuminate\Http\Response
     */
    public function getTagPosts(Request $request, string $slug, PostRepo $postRepo, AuthUserRepo $authUserRepo)
    {
        $this->authorize('view', 'App\\Tag');

        $limit = 10;
        $posts = $postRepo->getPaginatedTagPosts(
            $slug, $limit, $request->query('page', 1)
        );

        $total = $postRepo->getTotalPaginated();

        # PaginatedCollection(resource, collects, total, per_page)
        return new PaginatedCollection($posts, 'PostRow', $total , $limit);
    }

    /**
     * get the $slug tag with counting the posts associated with it.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug, AuthUserRepo $authUserRepo)
    {
        $this->authorize('view', 'App\\Tag');

        $tag = $this->tagRepo->getWithCountPosts($slug);

        return response()->json([
            'tag' => new TagResource($tag)
        ], 200);
    }


    /**
     * update the given $slug tag
     *
     * @param $_POST['name']
     *
     * @param  TagRequest $request
     * @param  string     $slug
     * @return \Illuminate\Http\Response
     */
    public function update(TagRequest $request, string $slug)
    {
        $data = $request->all();

        $tag = $this->tagRepo->update($slug, $data);

        return response()->json([
            'tag' => new TagResource($tag)
        ], 200);
    }

    /**
     * delete the $slug tag
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug, AuthUserRepo $authUserRepo)
    {
        $this->authorize('updateOrDelete', 'App\\Tag');

        $name = $this->tagRepo->delete($slug);

        return response()->json([
            'message' => "tag '{$name}' has been deleted successfully"
        ], 200);
    }
}
