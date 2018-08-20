<?php

namespace App\Http\Controllers\Admin;

use App\Post;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\PostRowResource;
use App\Http\Resources\UserSearchResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewUsers', User::class);

        $users = User::withCount([
            'posts', 'comments', 'votes', 'recommendations'
        ])->paginate(15);

        $users->getCollection()->transform(function ($user) {
            return new UserResource($user);
        });

        return $users;
    }

    /**
     * Display a listing of the banned Users.
     *
     * @return \Illuminate\Http\Response
     */
    public function bannedUsers()
    {
        $this->authorize('viewUsers', User::class);

        $users = User::where('privilege', 0)
                     ->withCount(['posts', 'comments', 'votes', 'recommendations'])
                     ->paginate(15);

        $users->getCollection()->transform(function ($user) {
            return new UserResource($user);
        });

        return $users;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $this->authorize('viewUsers', User::class);

        $user = User::withCount(['posts', 'comments', 'votes', 'recommendations'])
                    ->where('slug', $slug)
                    ->firstOrFail();

        return response()->json([
            'user' => new UserResource($user)
        ], 200);
    }

    public function getUserPosts($slug)
    {
        $this->authorize('viewUsers', User::class);

        $user = User::where('slug', $slug)->firstOrFail();
        $posts = Post::with(['category', 'author'])
                     ->withCount(['comments', 'recommendations'])
                     ->where('author_id', $user->id)
                     ->orderBy('id', 'desc')
                     ->paginate(10);

        $posts->getCollection()->transform(function ($post) {
            return new PostRowResource($post);
        });

        return $posts;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();
        return response()->json([
            'message'   => "'{$user->username}' has been deleted successfully"
        ], 200);
    }

    public function ban(User $user)
    {
        $this->authorize('assignRole', User::class);

        if ($user->role() === 'moderator') {
          $user->load('category');
          $category = $user->category->title;

          return response()->json([
              'message'   => "You can't ban '{$user->username}', this user is the moderator of the category '$category'"
          ], 401);
        }

        $user->privilege = 0;
        $user->save();

        return response()->json([
            'message'   => "'{$user->username}' has been banned successfully"
        ], 200);
    }

    public function unban(User $user)
    {
        $this->authorize('assignRole', User::class);

        $user->privilege = 1;
        $user->save();

        return response()->json([
            'message'   => "'{$user->username}' has been unbanned successfully"
        ], 200);
    }

    public function assignRole(Request $request, User $user)
    {
        $this->authorize('assignRole', User::class);
        $request->validate([
          'role' => [
              'required',
              'regex:(admin|author|regular\ user|banned\ user)'
            ]
        ]);

        switch ($request->role) {
          case 'admin':
            $user->privilege = 4;
            break;
          case 'author':
            $user->privilege = 2;
            break;
          case 'regular user':
            $user->privilege = 1;
            break;
          case 'banned user':
            $user->privilege = 0;
            break;
        }
        $user->save();

        return response()->json([
            'message'   => "'{$user->username}' has been updated successfully"
        ], 200);
    }
    /*
    * search for users using their emails and username
    *
    * @return \Illuminate\Http\Response
    */
    public function search(Request $request)
    {
        $q = $request->query('q');
        $users = User::where('privilege', '>', 0)
                     ->where('username', 'like', "%{$q}%")
                     ->orWhere('email', 'like', "%{$q}%")
                     ->get();

        $users->transform(function ($user) {
            return new UserSearchResource($user);
        });

        return $users;
    }
}
