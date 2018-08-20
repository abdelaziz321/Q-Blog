<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->method() == 'PUT') {
            $category = $this->category;
            return auth()->user()->can('update', $category);
        } else {
            return auth()->user()->can('createOrDelete', \App\Category::class);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $category = $this->category;
        $request = $this;

        return [
            'title' => [
                'required',
                'min:3',
                function($attribute, $value, $fail) use ($request, $category) {
                    $slug = str_slug($request->title, '-');
                    if ($request->method() == 'PUT') {
                        $categories = \App\Category::where('slug', $slug)
                                                   ->where('id', '!=', $category->id)
                                                   ->get();
                    } else {
                        $categories = \App\Category::where('slug', $slug)
                                                   ->get();
                    }

                    if (!$categories->isEmpty()) {
                        return $fail("'{$value}' is alrady exists.");
                    }
                },
            ],
            'moderator'   => 'required|integer',
            'description' => 'required'
        ];
    }
}
