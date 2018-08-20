<?php

namespace App\Http\Controllers;

use Hash;
use Auth;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     */
    public function __construct()
    {
        $this->middleware('jwt.refresh')->only('refresh');
        $this->middleware('jwt.auth', ['only' => ['user', 'logout']]);
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $password_confirmation
     * @param string $description
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $userData = $request->validate([
            'email'       => 'required|email|unique:users',
            'password'    => 'required|confirmed|min:5|max:256',
            'username'    => 'required|min:3'
        ]);

        $userData['password'] = Hash::make($request->password);
        $userData['slug'] = str_slug($userData['username']);
        $userData['joined_at'] = now()->toDateTimeString();
        $userData['privilege'] = 1;

        $user = User::create($userData);

        $token = auth()->attempt($userData);

        return $this->authenticatedResponse($token, $user);
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $userData = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:5|max:256'
        ]);

        if (!$token = auth()->attempt($userData)) {
            return response()->json([
                'message' => 'Wrong email or password'
            ], 400);
        }

        return $this->authenticatedResponse($token);
    }

    public function user()
    {
        return response()->json([
            'user' => new UserResource(auth()->user())
        ], 200);
    }

    public function refresh()
    {
        return response()->json([], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'you logged out successfully'
        ], 200);
    }

    public function authenticatedResponse($token, $user = null)
    {
        return response()->json([
            'user'  => new UserResource($user ?? auth()->user()),
            'token' => $token,
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 200);
    }
}
