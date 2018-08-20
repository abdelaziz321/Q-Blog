<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('updateOrDelete', $this->tag);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request = $this;
        $tag = $this->tag;

        return [
            'name' => [
                'min:2',
                'max:15',
                function($attribute, $value, $fail) use ($request, $tag) {
                    $tags = \App\Tag::where('slug', str_slug($request->name, '-'))
                                    ->where('id', '!=', $tag->id)
                                    ->get();

                    if (!$tags->isEmpty()) {
                        return $fail("'{$value}' is already exists.");
                    }
                },
            ]
        ];
    }
}
