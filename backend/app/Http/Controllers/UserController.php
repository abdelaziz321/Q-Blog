<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\UploadingFiles;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserSearchResource;
use App\Repositories\User\RepositoryInterface as UserRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class UserController extends Controller
{
    /**
     * search for authors have posts using their emails and username
     *
     * @param string $_GET['q']
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function searchAuthors(Request $request, UserRepo $userRepo)
    {
        $q = $request->validate( ['q' => 'required'] )['q'];
        $users = $userRepo->search($q);

        return UserSearchResource::collection($users);
    }

    /**
     * Update the given user.
     *
     * @param string $_POST['username']
     * @param string $_POST['description']
     * @param string $_POST['avatar']
     *
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, string $slug, AuthUserRepo $AuthUserRepo)
    {
        $data = $request->all();
        $user = $AuthUserRepo->user();

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $data['avatar'] = UploadingFiles::uploadAvatar(
                $request->file('avatar'), $user->avatar ?? ''
            );
        }
        else {
            unset($data['avatar']);
        }

        $user = $AuthUserRepo->update($data);

        return response()->json([
            'user' => new UserResource($user)
        ], 200);
    }
}
