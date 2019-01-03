<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Helpers\UploadingFiles;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\CommentResource;
use App\Http\Requests\Admin\PostRequest;
use App\Http\Resources\PaginatedCollection;
use App\Repositories\Tag\RepositoryInterface as TagRepo;
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
        $this->authorize('viewPosts', 'App\\Post');

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
        $this->authorize('viewPosts', 'App\\Post');

        $limit = 15;
        $posts = $this->postRepo->getPaginatedPosts(
            $limit, $request->query('page', 1), 0
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
    public function show(string $slug, AuthUserRepo $authUserRepo)
    {
        $this->authorize('viewPosts', 'App\\Post');

        $post = $this->postRepo->getPost($slug);

        return response()->json([
            'post' => new PostResource($post)
        ], 200);
    }

    /**
     * get the comments of the given post.
     *
     * @param  string $slug the slug of the post
     * @return \Illuminate\Http\Response
     */
    public function postComments(Request $request, string $slug, CommentRepo $commentRepo)
    {
        $comments = $commentRepo->getPostComments(
            $slug, 8, $request->query('page', 1), false
        );

        return CommentResource::collection($comments);
    }

    /**
     * create a new post.
     *
     * @param string        $_GET['title']
     * @param string        $_GET['body']          --html content--
     * @param int           $_GET['category_id']
     * @param array         $_GET['tags']
     * @param UploadedFile  $_GET['caption']
     *
     * @param  PostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request, TagRepo $tagRepo)
    {
        $data = $request->all();

        $data['body'] = UploadingFiles::uploadBodyImages($data['body']);
        $data['caption'] = UploadingFiles::uploadCaption($request->file('caption'));

        $post = $this->postRepo->create($data);

        $tagsIds = $tagRepo->addTagsIfNotExists($data['tags']);

        $post = $this->postRepo->assignTagsTo($tagsIds, $post->id);

        return response()->json([
            'post' => new PostResource($post)
        ], 200);
    }

    /**
     * Update the given post.
     *
     * @param string        $_GET['title']
     * @param string        $_GET['body']          --html content--
     * @param int           $_GET['category_id']
     * @param array         $_GET['tags']
     * @param UploadedFile  $_GET['caption']
     *
     * @param  PostRequest  $request
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, string $slug, TagRepo $tagRepo)
    {
        $data = $request->all();

        $post = $this->postRepo->getPost($slug);
        $oldTagsIds = $this->postRepo->getPostTagsIds($post->id);

        // update the post caption if a valid file given
        if ($request->hasFile('caption') && $request->file('caption')->isValid()) {
            $data['caption'] = UploadingFiles::uploadCaption(
                $request->file('caption'), $post->caption ?? ''
            );
        }
        else {
            unset($data['caption']);
        }

        $data['body'] = UploadingFiles::uploadBodyImages($data['body'], $post->body);

        $this->postRepo->update($post->slug, $data);

        $tagsIds = $tagRepo->addTagsIfNotExists($data['tags']);
        $post = $this->postRepo->assignTagsTo($tagsIds, $post->id);

        $tagRepo->removeTagsIfRequired($oldTagsIds);

        return response()->json([
            'post' => new PostResource($post)
        ], 200);
    }

    /**
     * delete the given post also remove the post tags if they weren't
     * assigned to other posts
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug, TagRepo $tagRepo)
    {
        $post = $this->postRepo->getPost($slug);
        $this->authorize('delete', $post);

        $oldTagsIds = $this->postRepo->getPostTagsIds($post->id);

        UploadingFiles::removeCaption($post->caption ?? '');
        UploadingFiles::removeBodyImages($post->body);

        $this->postRepo->delete($post->id);

        $tagRepo->removeTagsIfRequired($oldTagsIds);

        return response()->json([
            'message' => "'{$post->title}' post has been deleted successfully"
        ], 200);
    }


    /**
     * assign the given tags to the given post
     * also remove the post tags if they weren't assigned to other posts
     *
     * @param array    $_GET['tags']
     *
     * @param  Request $request
     * @param  string  $slug    the slug of the post
     * @return \Illuminate\Http\Response
     */
    public function assignTags(Request $request, string $slug, TagRepo $tagRepo)
    {
        $tags = $request->validate([
            'tags' => 'required|array'
        ])['tags'];

        $post = $this->postRepo->getPost($slug);
        $this->authorize('assignTags', $post);

        $oldTagsIds = $this->postRepo->getPostTagsIds($post->id);

        $tagsIds = $tagRepo->addTagsIfNotExists($tags);
        $post = $this->postRepo->assignTagsTo($tagsIds, $post->id);

        $tagRepo->removeTagsIfRequired($oldTagsIds);

        return response()->json([
            'post'      => new PostResource($post)
        ], 200);
    }

    /**
     * the authenticated user publish|unpublish the given post depending on
     * the given query parameter `action`.
     *
     * @param  string $_GET['action']  possible values ==> publish|unpublish
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function publishing(Request $request, string $slug, AuthUserRepo $authUserRepo)
    {
        $action = $request->validate([
            'action' => ['required', 'regex:#^(publish|unpublish)$#'],
        ])['action'];

        $post = $this->postRepo->getBy('slug', $slug);
        $this->authorize('publishing', $post);

        switch ($action) {
            case 'publish':
                $this->postRepo->publishing($post->id, true);
                break;

            case 'unpublish':
                $this->postRepo->publishing($post->id, false);
                break;

            default:
                # we fall in a black hole
                return;
        }

        return response()->json([
            'message' => "the post '{$post->title}' has been {$action}ed successfully"
        ], 200);
    }
}
