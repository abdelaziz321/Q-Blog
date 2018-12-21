<?php

namespace App\Repositories\User;

use App\User;
use App\Repositories\BaseRepository;

class Repository extends BaseRepository implements RepositoryInterface
{
    protected $_model = '\\App\\User';

    /**
     * get first $limit authors have more posts
     *
     * @param  integer $limit
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPopularAuthors($limit)
    {
        return User::withCount(['posts' => function ($query) {
                $query->published();
            }])
            ->where('privilege', '>', 1)
            ->orderBy('posts_count', 'desc')
            ->take($limit)
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
        $user = $this->getBy($field, $value);

        $user->slug = $data['slug'];
        $user->username = $data['username'];
        $user->description = $data['description'];

        if ( isset($data['avatar']) ) {
            $user->avatar = $data['avatar'];
        }

        $user->save();

        return $user;
    }

    /**
     * update the privilege of the given user to be a moderator
     * if the given user is admin he will still as admin
     *
     * @param int $id the id of the user
     */
    public function setAsModerator(int $id)
    {
        User::where('id', $id)
            ->where('privilege', '!=', 4)
            ->update(['privilege' => 3]);
    }

    /**
     * if there is no category related to the given moderator so
     * he is now a regular user.
     * if the given user is admin he will still as admin
     *
     * @param int $id
     * @return void
     */
    public function setAsRegularUserIfRequired(int $id)
    {
        $user = $this->getBy('id', $id);

        # we will take no action if he is an admin
        if ($user->isAdmin()) {
            return;
        }

        # we will take no action if he still moderate some categories
        if ($user->category()->exists()) {
            return;
        }

        # sorry man, you are fired - :'( -
        $user->privilege = 1;
        $user->save();
    }

    /**
     * Determine if the user has an ability.
     *
     * @param  array $args
     * @return boolean
     */
    public function can(...$args)
    {
        return $this->_record->can(...$args);
    }
}
