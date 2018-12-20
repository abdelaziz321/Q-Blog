<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Repositories\Comment\RepositoryInterface as CommentRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class CommentController extends Controller
{
    private $commentRepo;

    public function __construct(CommentRepo $commentRepo)
    {
        $this->commentRepo = $commentRepo;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request)
    {
        $data = $request->all();

        $comment = $this->commentRepo->create($data);

        return response()->json([
            'comment' => new CommentResource($comment)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\CommentRequest  $request
     * @param  int  $id the id of the comment
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, int $id)
    {
        $this->commentRepo->update($id, $request->body);

        return response()->json([
            'comment' => new CommentResource($comment)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id the id of the comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id, AuthUserRepo $authUserRepo)
    {
        $comment = $this->commentRepo->getBy('id', $id);

        $authUserRepo->can('delete', $comment);

        $this->commentRepo->delete($id);

        return response()->json([
            'message' => "your comment has been deleted successfully"
        ], 200);
    }

    /**
     * the authenticated user vote up the given comment
     *
     * @param  int $id the id of the comment
     * @return \Illuminate\Http\Response
     */
    public function vote(int $id, AuthUserRepo $authUserRepo)
    {
        $authUserRepo->can('createOrVote', 'App\\Comment');

        $this->commentRepo->vote($id, 1);

        return response()->json([
            'message' => "you voted up this comment successfully"
        ], 200);
    }

    /**
     * the authenticated user vote down the given comment
     *
     * @param  int $id the id of the comment
     * @return \Illuminate\Http\Response
     */
    public function unvote(int $id, AuthUserRepo $authUserRepo)
    {
        $authUserRepo->can('createOrVote', 'App\\Comment');

        $this->commentRepo->vote($id, -1);

        return response()->json([
            'message' => "you voted down this comment successfully"
        ], 200);
    }
}
