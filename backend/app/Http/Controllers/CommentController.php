<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
     * create a new comment on the given $_POST['post_id']
     *
     * @param string $_POST['body']
     * @param int    $_POST['post_id']
     *
     * @param  CommentRequest $request
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
     * update the given $id comment using $_POST['body']
     *
     * @param string $_POST['body']
     *
     * @param  CommentRequest $request
     * @param  int  $id the id of the comment
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, int $id)
    {
        $comment = $this->commentRepo->update($id, $request->body);

        return response()->json([
            'comment' => new CommentResource($comment)
        ], 200);
    }

    /**
     * destroy the comment only if the user own this comment or admin|moderator
     *
     * @param  int  $id the id of the comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id, AuthUserRepo $authUserRepo)
    {
        $comment = $this->commentRepo->getBy('id', $id);

        $this->authorize('delete', $comment);

        $this->commentRepo->delete($id);

        return response()->json([
            'message' => "your comment has been deleted successfully"
        ], 200);
    }

    /**
     * the authenticated user unvote && vote up|down the given comment id
     *
     * @param string $_GET['action']    possible values ==> up|down|del
     *
     * @param  int $id the id of the comment
     * @return \Illuminate\Http\Response
     */
    public function voting(Request $request, int $id, AuthUserRepo $authUserRepo)
    {
        $action = $request->validate([
            'action' => ['required', 'regex:#^(up|down|del)$#'],
        ])['action'];

        $this->authorize('createOrVote', 'App\\Comment');

        switch ($action) {
            case 'up':
                $this->commentRepo->vote($id, 1);
                $msg = 'voted up';
                break;

            case 'down':
                $this->commentRepo->vote($id, -1);
                $msg = 'voted down';
                break;

            case 'del':
                $this->commentRepo->vote($id, 0);
                $msg = 'deleted your vote for';
                break;

            default:
                # we fall in a black hole
                return;
        }

        return response()->json([
            'message' => "you $msg this comment successfully"
        ], 200);
    }
}
