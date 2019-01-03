<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\Post\RepositoryInterface as PostRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class PostRequest extends FormRequest
{
    private $postRepo;
    private $authUserRepo;

    public function __construct(AuthUserRepo $authUserRepo, PostRepo $postRepo)
    {
        $this->postRepo = $postRepo;
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
            $postSlug = $this->route('post');
            $post = $this->postRepo->getPost($postSlug);

            return $this->authUserRepo->can('update', $post);
        }
        else {
            return $this->authUserRepo->can(
                'create', ['App\\Post', $this->category_id]
            );
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $method = $this->method();
        // the requested title slug
        $newSlug = str_slug($this->title, '-');
        // the slug given in the url
        $urlSlug = $this->route('post');

        $rules = [
            'title' => [
                'required',
                'min:3',
                function($attribute, $value, $fail) use ($method, $newSlug, $urlSlug) {
                    if ($method === 'PUT') {
                        # we want the new slug to be different from all slugs in DB
                        # except from the slug given in the url
                        $slugExist = $this->postRepo->checkIfExist($newSlug, $urlSlug);
                    }
                    else {
                        # we want the new slug to be different from all slugs in DB
                        $slugExist = $this->postRepo->checkIfExist($newSlug);
                    }

                    if ($slugExist) {
                        return $fail("'{$value}' is alrady exists.");
                    }
                },
            ],
            'body'        => 'required',
            'caption'     => ['image'],
            'tags'        => 'array',
            'category_id' => 'required|integer'
        ];

        if ($method === 'POST') {
            $rules['caption'][] = 'required';
        }

        return $rules;
    }
}
