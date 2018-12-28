<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class UpdateUserRequest extends FormRequest
{
    private $authUserRepo;

    public function __construct(AuthUserRepo $authUserRepo)
    {
        $this->authUserRepo = $authUserRepo;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $urlSlug = $this->route('user');
        return $this->authUserRepo->can('update', ['App\\User', $urlSlug]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // the requested username slug
        $newSlug = str_slug($this->username, '-');
        // the slug given in the url
        $urlSlug = $this->route('user');

        return [
            'username' => [
                'required',
                'min:5',
                function($attribute, $value, $fail) use ($newSlug, $urlSlug) {
                    # we want the new slug to be different from all slugs in DB
                    # except from the slug given in the url
                    $slugExist = $this->authUserRepo->checkIfExist($newSlug, $urlSlug);

                    if ($slugExist) {
                        return $fail("'{$value}' is alrady exists.");
                    }
                },
            ]
        ];
    }
}
