<?php

namespace App\Http\Controllers\Admin;

use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', Comment::class);

        $comments = Comment::with(['user', 'post', 'replyTo.user'])
                           ->leftJoin('votes AS total', function ($join) {
                              $join->on('comments.id', '=', 'total.comment_id');
                           })
                           ->selectRaw('comments.*, sum(total.vote) AS votes')
                           ->groupBy('comments.id')
                           ->orderBy('id', 'desc')
                           ->paginate(20);

        $comments->getCollection()->transform(function ($comment) {
            return new CommentResource($comment);
        });

        return $comments;
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
            'message'   => "this comment has been deleted successfully"
        ], 200);
    }
}
