<?php

namespace App\Repositories\User;

use App\User;

class AuthRepository extends Repository implements AuthRepositoryInterface
{
    public function __construct()
    {
        $this->_record = auth()->user();
    }

    /**
     * return the current authenticated user
     *
     * @return \stdClass
     */
    public function user()
    {
        return $this->_record;
    }

    /**
     * update the authenticated user
     *
     * @param  array  $data
     * @return void
     */
    public function update(array $data)
    {
        return parent::updateUser($data, $this->_record->id);
    }
}
