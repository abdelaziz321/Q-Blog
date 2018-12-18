<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\User\RepositoryInterface as UserRepo;
use App\Repositories\Comment\RepositoryInterface as CommentRepo;

class CommentRequest extends FormRequest
{
    private $userRepo;
    private $commentRepo;

    public function __construct(UserRepo $userRepo, CommentRepo $commentRepo)
    {
        $this->userRepo = $userRepo;
        $this->commentRepo = $commentRepo;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->method() == 'PUT') {
            $comment = $this->commentRepo->get($this->route('comment'));
            return $this->userRepo->can('update', $comment);
        }
        else {
            return $this->userRepo->can('createOrVote', 'App\\Comment');
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
