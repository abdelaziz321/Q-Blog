<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserSearchResource;
use App\Repositories\User\RepositoryInterface as UserRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

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
     * add a new message to the firestore.
     *
     * @param array $_GET['message']
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function message(Request $request, AuthUserRepo $authUserRepo)
    {
        // REVIEW: we may change this later but it accomplish the same target
        $this->authorize('viewUsers', 'App\\User');

        $request->validate([
            'message' => 'required|string'
        ]);

        // send the message to firestore db
        

        return response()->json([
            'message' => 32
        ], 200);
    }
}
