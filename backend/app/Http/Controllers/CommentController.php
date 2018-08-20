<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request)
    {
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;

        $comment = Comment::create($data);
        $comment->votes = 0;

        return response()->json([
            'comment' => new CommentResource($comment)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        $comment->body = $request->body;
        $comment->save();

        return response()->json([
            'comment' => new CommentResource($comment)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'message' => "your comment has been deleted successfully"
        ], 200);
    }

    public function vote(Comment $comment)
    {
        $this->authorize('createOrVote', Comment::class);

        $comment->votes()->syncWithoutDetaching([
            auth()->user()->id => ['vote' => 1]
        ]);

        return response()->json([
            'message' => "you voted up this comment successfully"
        ], 200);
    }

    public function unvote(Comment $comment)
    {
        $this->authorize('createOrVote', Comment::class);

        $comment->votes()->syncWithoutDetaching([
            auth()->user()->id => ['vote' => -1]
        ]);

        return response()->json([
            'message' => "you voted down this comment successfully"
        ], 200);
    }
}
