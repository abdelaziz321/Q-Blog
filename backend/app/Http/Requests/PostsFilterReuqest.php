<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostsFilterReuqest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'views'           => ['regex:#^(ASC|DESC)$#i'],
            'date'            => ['regex:#^(ASC|DESC)$#i'],
            'comments'        => ['regex:#^(ASC|DESC)$#i'],
            'recommendations' => ['regex:#^(ASC|DESC)$#i'],
            'title'           => 'string',
            'author'          => 'string',
            'category'        => 'string',
            'tags'            => 'array'
        ];
    }
}
