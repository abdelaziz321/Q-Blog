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
            ->whereHas('posts', function ($query) {
                $query->published();
            })
            ->where('privilege', '>', 1)
            ->orderBy('posts_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * get pageinated users with counting:
     * [posts, comments, votes, recommendations]
     *
     * @param  int     $limit
     * @param  int     $offset
     * @param  boolean $banned
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedUsers(int $limit, int $page = 1, bool $banned = false)
    {
        $query = User::query();

        if ($banned) {
            $query->where('privilege', 0);
        }

        $users = $query->withCount([
            'posts', 'comments', 'votes', 'recommendations'
        ])->get();

        $this->_total = $users->count();

        $offset = ($page - 1) * $limit;
        return $users->slice($offset, $limit);
    }

    /**
     * get the $slug user with counting:
     * [posts, comments, votes, recommendations]
     *
     * @param  string $slug
     * @return \stdClass
     */
    public function getUser(string $slug)
    {
        if (empty($this->_record)) {
            $this->_record = User::withCount(['posts', 'comments', 'votes', 'recommendations'])
                ->where('slug', $slug)
                ->firstOrFail();
        }

        return $this->_record;
    }

    /**
     * search authors using their username & email
     *
     * @param  string $q the string by which we will search
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function searchAuthors(string $q)
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
     * search unbanned users using their username & email
     *
     * @param  string $q the string by which we will search
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function search(string $q)
    {
        return User::where('privilege', '>', 0)
            ->where(function ($query) use ($q) {
                $query->where('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get();
    }

    /**
     * create a new user
     *
     * @param  array  $userData ['username', 'email', 'password', 'description']
     * @return \stdClass
     */
    public function create(array $userData)
    {
        $this->_record = User::create([
            'slug'        => str_slug($userData['username']),
            'username'    => $userData['username'],
            'email'       => $userData['email'],
            'password'    => \Hash::make($userData['password']),
            'privilege'   => 1,
            'description' => $userData['description']
        ]);

        return $this->_record;
    }

    /**
     * update the given user using the $data arary.
     *
     * @param  array $data {username, slug, description, avatar}
     * @param  int   $id the id of the user
     * @return void
     */
    public function updateUser(array $data, int $id)
    {
        $user = $this->getBy('id', $id);

        $user->slug = str_slug($data['username'] , '-');
        $user->username = $data['username'];
        $user->description = $data['description'];

        if (isset($data['avatar'])) {
            $user->avatar = $data['avatar'];
        }

        $user->save();

        return $user;
    }

    /**
     * delete the given $slug user and return its username
     *
     * @param  string $slug
     * @return string $username
     */
    public function delete(string $slug)
    {
        $user = $this->getBy('slug', $slug);
        $username = $user->username;

        $user->delete();

        return $username;
    }

    /**
     * update the privilege of the given user to be a moderator
     * if the given user is admin he will still as admin
     *
     * @param int $id the id of the user
     */
    public function setAsModeratorIfNotAdmin(int $id)
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
        $user = User::withCount('category')
            ->where('id', $id)
            ->firstOrFail();

        # we will take no action if he is an admin
        if ($user->isAdmin()) {
            return;
        }

        # we will take no action if he still moderate some categories
        if ($user->category_count > 0) {
            return;
        }

        # sorry man, you are fired - :'( -
        $user->privilege = 1;
        $user->save();
    }

    /**
     * assign role to the given $slug user
     *
     * @param  string $slug
     * @param  string $role
     * @return string
     */
    public function assignRole(string $slug, string $role)
    {
        $user = $this->getBy('slug', $slug);

        switch ($role) {
            case 'admin':
                $user->privilege = 4;
                break;

            case 'author':
                $user->privilege = 2;
                break;

            case 'regular':
                $user->privilege = 1;
                break;

            case 'banned':
                $user->privilege = 0;
                break;

            default:
                # we fall in a black hole
                return;
        }

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
