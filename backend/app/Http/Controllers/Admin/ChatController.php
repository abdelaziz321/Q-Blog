<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserSearchResource;
use App\Repositories\User\RepositoryInterface as UserRepo;

class ChatController extends Controller
{
    /**
     * get the {id, username, avater} of each given user id.
     *
     * @param array $_GET['ids'] array of users ids
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function users(Request $request, UserRepo $userRepo)
    {
        $this->authorize('viewUsers', 'App\\User');

        $userIds = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer'
        ])['ids'];

        $users = $userRepo->getUsers($userIds);

        return UserSearchResource::collection($users);
    }

    /**
     * add a new message to the chat.
     *
     * @param array $_GET['body']
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function message(Request $request)
    {
        return $request->all();
    }
}
