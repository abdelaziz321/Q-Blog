<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
      if ($this->method() == 'PUT') {
          $post = $this->post;
          return auth('api')->user()->can('update', $post);
      } else {
          $request = $this;
          return auth('api')->user()->can('create', [\App\Post::class, $request->category_id]);
      }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      $post = $this->post;
      $request = $this;

      return [
          'title' => [
              'required',
              'min:3',
              function($attribute, $value, $fail) use ($request, $post) {
                  $slug = str_slug($request->title, '-');
                  if ($request->method() == 'PUT') {
                      $posts = \App\Post::where('slug', $slug)
                                        ->where('id', '!=', $post->id)
                                        ->get();
                  } else {
                      $posts = \App\Post::where('slug', $slug)
                                        ->get();
                  }

                  if (!$posts->isEmpty()) {
                      return $fail("'{$value}' is alrady exists.");
                  }
              },
          ],
          'body'        => 'required',
          'category_id' => 'required|integer'
      ];
    }
}
