<?php

namespace App\Repositories\Chat;

use App\Message;
use App\Repositories\BaseRepository;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class Repository extends BaseRepository implements RepositoryInterface
{
    protected $_model = '\\App\\Message';

    /**
     * get pageinated messages
     * if ($id = 20 and limit = 10) the api will return the following messages:
     * 19 18 17 16 15 14 13 12 11 10
     *
     * @param  int $limit
     * @param  int $id     the last message id
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedMessages(int $limit, int $id)
    {
        $query = Message::query();

        // get latest messages if no id provided
        if ($id !== 0) {
            $query->where('id', '<', $id);
        }

        return $query->with(['user'])
            ->orderBy('id', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * create a new message
     *
     * @param  string $message the body of the message
     * @return \stdClass
     */
    public function create(string $message)
    {
        $user = resolve(AuthUserRepo::class)->user();

        $messageModel = new Message;
        $messageModel->message = $message;
        $messageModel->user_id = $user->id;
        $messageModel->save();

        return $messageModel;
    }
}
