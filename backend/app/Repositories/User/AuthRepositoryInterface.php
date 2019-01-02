<?php

namespace App\Repositories\User;

interface AuthRepositoryInterface
{
    /**
     * return the current authenticated user
     *
     * @return \stdClass
     */
    public function user();

    /**
     * update the authenticated user
     *
     * @param  array  $data
     * @return void
     */
    public function update(array $data);
}
