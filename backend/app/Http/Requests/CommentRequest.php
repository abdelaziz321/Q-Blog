<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->method() == 'PUT') {
            $comment = $this->comment;
            return auth()->user()->can('update', $comment);
        } else {
            return auth()->user()->can('createOrVote', \App\Comment::class);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'body' => 'required',
        ];

        if ($this->method() == 'POST') {
            $rules['post_id'] = 'required|numeric';
        }

        return $rules;
    }
}
