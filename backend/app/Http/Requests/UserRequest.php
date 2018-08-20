<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->method() == 'PUT') {
            $user = $this->user;
            return auth()->user()->can('update', $user);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request = $this;
        $user = $this->user;

        return [
            'password' => 'required|min:5|max:256',
            'username' => [
                'required',
                'min:5',
                function($attribute, $value, $fail) use ($request, $user) {
                    $slug = str_slug($request->username, '-');
                    if ($request->method() == 'PUT') {
                        $users = \App\User::where('slug', $slug)
                                          ->where('id', '!=', $user->id)
                                          ->get();
                    } else {
                        $users = \App\User::where('slug', $slug)
                                          ->get();
                    }

                    if (!$users->isEmpty()) {
                        return $fail("'{$value}' is alrady exists.");
                    }
                },
            ]
        ];
    }
}
