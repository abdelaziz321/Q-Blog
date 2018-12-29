<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserSearchResource;
use App\Http\Resources\PaginatedCollection;
use App\Repositories\Post\RepositoryInterface as PostRepo;
use App\Repositories\User\RepositoryInterface as UserRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;
use App\Repositories\Category\RepositoryInterface as CategoryRepo;

class UserController extends Controller
{
    private $authUserRepo;

    public function __construct(AuthUserRepo $authUserRepo)
    {
        $this->authUserRepo = $authUserRepo;
    }

    /**
     * get paginated users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewUsers', 'App\\User');

        $limit = 10;
        $users = $this->authUserRepo->getPaginatedUsers(
            $limit, $request->query('page', 1), false
        );

        $total = $this->authUserRepo->getTotalPaginated();

        # PaginatedCollection(resource, collects, total, per_page)
        return new PaginatedCollection($users, 'User', $total , $limit);
    }

    /**
     * get paginated banned users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bannedUsers(Request $request)
    {
        $this->authorize('viewUsers', 'App\\User');

        $limit = 10;
        $users = $this->authUserRepo->getPaginatedUsers(
            $limit, $request->query('page', 1), true
        );

        $total = $this->authUserRepo->getTotalPaginated();

        # PaginatedCollection(resource, collects, total, per_page)
        return new PaginatedCollection($users, 'User', $total , $limit);
    }

    /**
     * get the $slug user.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug, UserRepo $userRepo)
    {
        $this->authorize('viewUsers', 'App\\User');

        $user = $userRepo->getUser($slug);

        return response()->json([
            'user' => new UserResource($user)
        ], 200);
    }

    /**
     * get paginated posts of the given user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $slug the slug of the user
     * @return \Illuminate\Http\Response
     */
    public function getUserPosts(Request $request, string $slug, PostRepo $postRepo)
    {
        $this->authorize('viewUsers', 'App\\User');

        $limit = 8;
        $posts = $postRepo->getPaginatedUserPosts(
            $slug, $limit, $request->query('page', 1)
        );

        $total = $postRepo->getTotalPaginated();

        # PaginatedCollection(resource, collects, total, per_page)
        return new PaginatedCollection($posts, 'PostRow', $total , $limit);
    }

    /**
     * delete the given $slug user.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug, UserRepo $userRepo)
    {
        $user = $userRepo->getBy('slug', $slug);

        $this->authorize('delete', $user);

        $username = $userRepo->delete($slug);

        return response()->json([
            'message'   => "the user '{$username}' has been deleted successfully"
        ], 200);
    }

    /**
     * assign role to the given $slug user
     *
     * @param  string $_GET['role'] possible values ==> admin|moderator|author|regular|banned
     *
     * @param  Request $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function assignRole(Request $request, string $slug, UserRepo $userRepo, CategoryRepo $categoryRepo)
    {
        $role = $request->validate([
            'role' => ['required', 'regex:(admin|moderator|author|regular|banned)']
        ])['role'];

        $user = $userRepo->getBy('slug', $slug);
        # does the admin $user have any category he is moderate
        $moderate = ($user->role() === 'admin') ? $categoryRepo->isModerate($user->id) : false;

        $this->authorize('assignRole', [$user, $role, $moderate]);

        $this->authUserRepo->assignRole($slug, $role);

        return response()->json([
            'message'   => "'{$user->username}' has been updated successfully"
        ], 200);
    }

    /**
     * search for unbanned users using their username and email.
     *
     * @param string $_GET['q'] the string by which we will search
     *
     * @param Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $q = $request->validate( ['q' => 'required'] )['q'];
        $users = $this->authUserRepo->search($q);

        return UserSearchResource::collection($users);
    }
}
