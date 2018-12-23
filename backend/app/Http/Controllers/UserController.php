<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
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
    public function search(Request $request, UserRepo $userRepo)
    {
        $q = $request->query('q');
        $users = $userRepo->search($q);

        return UserSearchResource::collection($users);
    }

    /**
     * Update the specified resource in storage.
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

        $data['slug'] = str_slug($data['username'] , '-');

        // handling the uploaded photo
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $fileName = public_path('storage/users/') . $user->avatar;
            # delete the previous avatar if exists
            if ($user->avatar != null && file_exists($fileName)) {
                unlink($fileName);
            }

            $avatar = $request->file('avatar');
            $avatarName = time() . '-' . $avatar->getClientOriginalName();
            $data['avatar'] = $avatarName;
            \Image::make($avatar)->fit(300, 300)->save(public_path('storage/users/') . $avatarName);

        } else {
            unset($data['avatar']);
        }

        $user = $AuthUserRepo->update($data);

        return response()->json([
            'user' => new UserResource($user)
        ], 200);
    }
}
