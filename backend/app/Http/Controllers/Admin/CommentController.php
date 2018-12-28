<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaginatedCollection;
use App\Repositories\Comment\RepositoryInterface as CommentRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class CommentController extends Controller
{
    private $commentRepo;
    private $authUserRepo;

    public function __construct(CommentRepo $commentRepo, AuthUserRepo $authUserRepo)
    {
        $this->commentRepo = $commentRepo;
        $this->authUserRepo = $authUserRepo;
    }

    /**
     * get all comments with the sum of votes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', 'App\\Comment');

        $limit = 20;
        $comments = $this->commentRepo->getPaginatedComments(
            $limit, $request->query('page', 1)
        );

        $total = $this->commentRepo->getTotalPaginated();

        # PaginatedCollection(resource, collects, total, per_page)
        return new PaginatedCollection($comments, 'Comment', $total, $limit);
    }

    /**
     * remove the given comment.
     *
     * @param  int  $id the id of the comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $comment = $this->commentRepo->getBy('id', $id);
        $this->authorize('delete', $comment);

        $this->commentRepo->delete($comment->id);

        return response()->json([
            'message'   => "this comment has been deleted successfully"
        ], 200);
    }
}
