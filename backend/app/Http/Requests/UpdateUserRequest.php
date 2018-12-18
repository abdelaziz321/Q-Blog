<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\User\RepositoryInterface as UserRepo;

class UpdateUserRequest extends FormRequest
{
    private $userRepo;

    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $urlSlug = $this->route('user');
        return $this->userRepo->can('update', ['App\\User', $urlSlug]);
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
                    $users = $this->userRepo->checkIfExist($newSlug, $urlSlug ?? '');

                    if ($users > 0) {
                        return $fail("'{$value}' is alrady exists.");
                    }
                },
            ]
        ];
    }
}
