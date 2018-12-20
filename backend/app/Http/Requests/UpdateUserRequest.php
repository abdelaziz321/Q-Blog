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
        $username = $this->username;
        // the slug given in the url
        $slug = $this->route('user');
        $urlSlug = $this->route('user');

        return [
            'username' => [
                'required',
                'min:5',
                function($attribute, $value, $fail) use ($username, $urlSlug) {
                    # we want the slug to be different from all slugs in DB
                    # except from the $urlSlug
                    $newSlug = str_slug($username, '-');
                    $users = $this->authUserRepo->checkIfExist($newSlug, $urlSlug ?? '');

                    if ($users > 0) {
                        return $fail("'{$value}' is alrady exists.");
                    }
                },
            ]
        ];
    }
}
