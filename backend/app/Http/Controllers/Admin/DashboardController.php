<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $views = \App\Post::selectRaw('sum(views) AS views')
                          ->first()->views;

        return response()->json([
            'posts'    => \App\Post::count(),
            'comments' => \App\Comment::count(),
            'users'    => \App\User::count(),
            'views'    => $views
        ]);
    }
}
