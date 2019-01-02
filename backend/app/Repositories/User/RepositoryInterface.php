<?php

namespace App\Repositories\User;

interface RepositoryInterface
{
    /**
     * get first $limit authors have more posts
     *
     * @param  integer $limit
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPopularAuthors($limit);

    /**
     * get pageinated users with counting:
     * [posts, comments, votes, recommendations]
     *
     * @param  int     $limit
     * @param  int     $offset
     * @param  boolean $banned
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedUsers(int $limit, int $page = 1, bool $banned = false);

    /**
     * get the $slug user with counting:
     * [posts, comments, votes, recommendations]
     *
     * @param  string $slug
     * @return \stdClass
     */
    public function getUser(string $slug);

    /**
     * search authors using their username & email
     *
     * @param  string $q the string by which we will search
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function searchAuthors(string $q);

    /**
     * search unbanned users using their username & email
     *
     * @param  string $q the string by which we will search
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function search(string $q);

    /**
     * create a new user
     *
     * @param  array  $userData ['username', 'email', 'password', 'description']
     * @return \stdClass
     */
    public function create(array $userData);

    /**
     * update the given user using the $data arary.
     *
     * @param  array $data {username, slug, description, avatar}
     * @param  int   $id the id of the user
     * @return void
     */
    public function updateUser(array $data, int $id);

    /**
     * delete the given $slug user and return its username
     *
     * @param  string $slug
     * @return string $username
     */
    public function delete(string $slug);

    /**
     * update the privilege of the given user to be a moderator
     * if the given user is admin he will still as admin
     *
     * @param int $id the id of the user
     */
    public function setAsModeratorIfNotAdmin(int $id);

    /**
     * if there is no category related to the given moderator so
     * he is now a regular user.
     * if the given user is admin he will still as admin
     *
     * @param int $id
     * @return void
     */
    public function setAsRegularUserIfRequired(int $id);

    /**
     * assign role to the given $slug user
     *
     * @param  string $slug
     * @param  string $role
     * @return string
     */
    public function assignRole(string $slug, string $role);
}
