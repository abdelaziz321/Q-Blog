<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserSearchResource;
use App\Repositories\User\RepositoryInterface as UserRepo;

class UserController extends Controller
{
    private $userRepo;

    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * search for authors have posts using their emails and username
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $q = $request->query('q');
        $users = $this->userRepo->search($q);

        return UserSearchResource::collection($users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $slug
     *
     * @param string $username == provides ==> slug
     * @param string $description
     * @param string $avatar
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, string $userSlug)
    {
        $data = $request->all();
        $user = $this->userRepo->user();

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

        $this->userRepo->update($data);

        return response()->json([
            'user' => new UserResource($user)
        ], 200);
    }
}
