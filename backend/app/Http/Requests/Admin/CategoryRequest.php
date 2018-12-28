<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;
use App\Repositories\Category\RepositoryInterface as CategoryRepo;

class CategoryRequest extends FormRequest
{
    private $authUserRepo;
    private $categoryRepo;

    public function __construct(AuthUserRepo $authUserRepo, CategoryRepo $categoryRepo)
    {
        $this->authUserRepo = $authUserRepo;
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->method() == 'PUT') {
            $categorySlug = $this->route('category');
            $category = $this->categoryRepo->getWithModerator($categorySlug);

            return $this->authUserRepo->can('update', $category);
        }
        else {
            return $this->authUserRepo->can('createOrDelete', 'App\\Category');
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
        $urlSlug = $this->route('category');

        return [
            'title' => [
                'required',
                'min:3',
                function($attribute, $value, $fail) use ($method, $newSlug, $urlSlug) {
                    if ($method == 'PUT') {
                        # we want the new slug to be different from all slugs in DB
                        # except from the slug given in the url
                        $slugExist = $this->categoryRepo->checkIfExist($newSlug, $urlSlug);
                    }
                    else {
                        # we want the new slug to be different from all slugs in DB
                        $slugExist = $this->categoryRepo->checkIfExist($newSlug);
                    }

                    if ($slugExist) {
                        return $fail("'{$value}' is alrady exists.");
                    }
                },
            ],
            'moderator'   => 'required|integer',
            'description' => 'required'
        ];
    }
}
