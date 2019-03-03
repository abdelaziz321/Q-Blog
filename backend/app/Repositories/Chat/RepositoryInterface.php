<?php

namespace App\Repositories\Chat;

interface RepositoryInterface
{
    /**
     * get pageinated messages
     * if ($id = 20 and limit = 10) the api will return the following messages:
     * 19 18 17 16 15 14 13 12 11 10
     *
     * @param  int $limit
     * @param  int $id     the last message id
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedMessages(int $limit, int $id);

    /**
     * create a new message
     *
     * @param  string $message the body of the message
     * @return void
     */
    public function create(string $message);
}
