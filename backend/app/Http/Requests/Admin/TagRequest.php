<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\Tag\RepositoryInterface as TagRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;


class TagRequest extends FormRequest
{
    private $tagRepo;
    private $authUserRepo;

    public function __construct(AuthUserRepo $authUserRepo, TagRepo $tagRepo)
    {
        $this->tagRepo = $tagRepo;
        $this->authUserRepo = $authUserRepo;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->authUserRepo->can('updateOrDelete', 'App\\Tag');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // the requested name slug
        $newSlug = str_slug($this->name, '-');
        // the slug given in the url
        $urlSlug = $this->route('tag');

        return [
            'name' => [
                'min:2',
                'max:15',
                function($attribute, $value, $fail) use ($newSlug, $urlSlug) {
                    # we want the new slug to be different from all slugs in DB
                    # except from the slug given in the url
                    $slugExist = $this->tagRepo->checkIfExist($newSlug, $urlSlug);

                    if ($slugExist) {
                        return $fail("'{$value}' is already exists.");
                    }
                },
            ]
        ];
    }
}
