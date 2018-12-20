<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\User\RepositoryInterface as UserRepo;
use App\Repositories\Post\RepositoryInterface as PostRepo;
use App\Repositories\Comment\RepositoryInterface as CommentRepo;

class DashboardController extends Controller
{
    private $userRepo;
    private $postRepo;
    private $commentRepo;

    public function __construct(UserRepo $userRepo, PostRepo $postRepo, CommentRepo $commentRepo)
    {
        $this->userRepo = $userRepo;
        $this->postRepo = $postRepo;
        $this->commentRepo = $commentRepo;
    }

    /**
     * Display the count of posts|users|comments|views
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'posts'    => $this->postRepo->count(),
            'users'    => $this->userRepo->count(),
            'comments' => $this->commentRepo->count(),
            'views'    => $this->postRepo->countViews()
        ]);
    }
}
