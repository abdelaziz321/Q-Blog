<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\User\RepositoryInterface as UserRepo;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => ['login', 'register']
        ]);
    }

    /**
     * @param string $_POST['username']
     * @param string $_POST['email']
     * @param string $_POST['password']
     * @param string $_POST['password_confirmation']
     * @param string $_POST['description']
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request, UserRepo $userRepo)
    {
        $userData = $request->validate([
            'username'    => 'required|min:3',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|confirmed|min:5|max:256',
            'description' => 'string|max:120'
        ]);

        $user = $userRepo->create($userData);

        $token = \Auth::attempt([
            'email'    => $userData['email'],
            'password' => $userData['password']
        ]);

        return $this->authenticatedResponse($token, $user);
    }

    /**
     * @param string $_POST['email']
     * @param string $_POST['password']
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $userData = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:5|max:256'
        ]);

        $token = \Auth::attempt($userData);

        if (!$token) {
            return response()->json([
                'message' => 'Wrong email or password'
            ], 400);
        }

        return $this->authenticatedResponse($token);
    }

    /**
     * get the current authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        return response()->json([
            'user' => new UserResource(\Auth::user())
        ], 200);
    }

    /**
     * refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = \Auth::refresh();

        return $this->authenticatedResponse($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        \Auth::logout();

        return response()->json([
            'message' => 'successfully logged out'
        ], 200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @param  \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticatedResponse($token, $user = null)
    {
        return response()->json([
            'user'  => new UserResource($user ?? \Auth::user()),
            'token' => $token,
            'expires_in' => \Auth::factory()->getTTL() * 60
        ], 200);
    }
}
