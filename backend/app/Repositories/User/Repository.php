<?php

namespace App\Repositories\User;

use App\User;

class Repository implements RepositoryInterface
{
    /**
     * the authenticated user
     */
    private $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * get pageinated unbanned users
     *
     * @param  integer $limit
     * @param  integer $offset
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedUsers($limit, $offset = 0)
    {
        return User::withCount(['posts' => function ($query) {
                $query->published();
            }])
            ->where('privilege', '>', 1)
            ->orderBy('posts_count', 'desc')
            ->take($limit)
            ->skip($offset)
            ->get();
    }

    /**
     * the authenticated user recommend the given post
     *
     * @param  string $slug
     * @return void
     */
    public function recommend(string $slug)
    {
        $id = \App\Post::where('slug', $slug)->pluck('id')->all();
        $this->user->recommendations()->sync($id, false);
    }

    /**
     * the authenticated user unrecommend the given post
     *
     * @param  string $slug
     * @return void
     */
    public function unrecommend(string $slug)
    {
        $id = \App\Post::where('slug', $slug)->pluck('id')->all();
        $this->user->recommendations()->detach($id);
    }

    /**
     * @param  string $q the string by which we will search
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function search(string $q)
    {
        return User::has('posts')
            ->where(function ($query) use ($q) {
                $query->where('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get();
    }


    /**
     * check if the given $slug exists in the users table.
     * we don't care if the $except slug exists or not.
     *
     * @param  string $slug
     * @param  string $except
     * @return int
     */
    public function checkIfExist(string $slug, string $except = '')
    {
        return User::where('slug', $slug)
            ->where('slug', '!=', $except)
            ->count();
    }

    /**
     * return the current authenticated user
     *
     * @return \App\User
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * update the authenticated user
     *
     * @param  array  $data
     * @return void
     */
    public function update(array $data)
    {
        $this->user->slug = $data['slug'];
        $this->user->username = $data['username'];
        $this->user->description = $data['description'];

        if ( isset($data['avatar']) ) {
            $this->user->avatar = $data['avatar'];
        }

        $this->user->save();
    }

    /**
     * Determine if the user has an ability.
     *
     * @param  array $args
     * @return boolean
     */
    public function can(...$args)
    {
        $action = array_shift($args);
        array_unshift($args, User::class);

        return $this->user->can($action, $args);
    }

}
