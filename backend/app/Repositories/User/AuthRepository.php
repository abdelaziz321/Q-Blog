<?php

namespace App\Repositories\User;

use App\User;

class AuthRepository extends Repository implements AuthRepositoryInterface
{
    public function __construct()
    {
        $this->user = auth()->user();
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
    public function update(array $data, string $field = null, $value = null)
    {
        parent::update($data, 'id', $this->user->id);
    }
}
