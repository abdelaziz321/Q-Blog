<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserSearchResource;

class UserController extends Controller
{
    /**
     * search for authors have posts using their emails and username
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $q = $request->query('q');
        $users = User::has('posts')
                     ->where(function ($query) use ($q) {
                         $query->where('username', 'like', "%{$q}%")
                               ->orWhere('email', 'like', "%{$q}%");
                     })
                     ->get();


        $users->transform(function ($user) {
            return new UserSearchResource($user);
        });

        return $users;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $slug
     *
     * @param string $username
     * @param string $password
     * @param string $password_confirmation
     * @param string $description
     * @param string $avatar
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        $data = $request->all();
        $data['slug'] = str_slug($request->username , '-');
        $data['password'] = \Hash::make($data['password']);

        // handling the uploaded photo
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            # delete the previous avatar if exists
            if ($user->avatar != null) {
                unlink(public_path('storage/users/') . $user->avatar);
            }
            $avatar = $request->file('avatar');
            $avatarName = time() . '-' . $avatar->getClientOriginalName();
            $data['avatar'] = $avatarName;
            \Image::make($avatar)->fit(300, 300)->save(public_path('storage/users/') . $avatarName);
        } else {
            unset($data['avatar']);
        }

        $user->update($data);
        $user->load(['posts', 'comments', 'votes', 'recommendations']);

        return response()->json([
            'user' => new UserResource($user)
        ], 200);
    }
}
