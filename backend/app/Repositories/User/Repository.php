<?php

namespace App\Repositories\User;

use App\User;

class Repository implements RepositoryInterface
{
    protected $user;

    /**
     * get the user which has $field=$value from the propety $user or from DB
     *
     * @param  string $field
     * @param  mixed $value
     * @return App\Post
     */
    public function getBy(string $field, $value)
    {
        if (empty($this->user) || $this->user->$field !== $value) {
            $this->user = User::where($field, $value)->firstOrFail();
        }

        return $this->user;
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
     * update the user which has $field=$value using the $data arary
     * only update [username, slug, description, avatar]
     *
     * @param  array  $data
     * @param  string $field
     * @param  string $value
     * @return void
     */
    public function update(array $data, string $field, $value)
    {
        $this->getBy($field, $value);

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
        return $this->user->can(...$args);
    }
}
