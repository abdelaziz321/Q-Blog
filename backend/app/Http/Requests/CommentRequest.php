<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\Comment\RepositoryInterface as CommentRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class CommentRequest extends FormRequest
{
    private $commentRepo;
    private $authUserRepo;

    public function __construct(AuthUserRepo $authUserRepo, CommentRepo $commentRepo)
    {
        $this->commentRepo = $commentRepo;
        $this->authUserRepo = $authUserRepo;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->method() == 'PUT') {
            $commentId = $this->route('comment');
            $comment = $this->commentRepo->getCommentWithVotes($commentId);

            return $this->authUserRepo->can('update', $comment);
        }
        else {
            return $this->authUserRepo->can('createOrVote', 'App\\Comment');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules['body'] = 'required';

        if ($this->method() == 'POST') {
            $rules['post_id'] = 'required|numeric';
        }

        return $rules;
    }
}
