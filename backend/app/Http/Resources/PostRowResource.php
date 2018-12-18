<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostRowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $published_at = null;
        if ($this->published) {
          $published_at = now()
              ->createFromFormat('Y-m-d H:i:s', $this->published_at)
              ->diffForHumans();
        }

        $array = [
            'id'                    => $this->id,
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'caption'               => $this->caption,
            'views'                 => $this->views ?? 1,
            'published'             => $this->published ?? 0,
            'published_at'          => $published_at,
            'total_comments'        => $this->comments_count ?? 0,
            'total_recommendations' => $this->recommendations_count ?? 0
        ];
        $this->getPostAuthor($array);
        $this->getPostCategory($array);

        return $array;
    }

    /**
     * add the author of the post to the array only if the relation `author`
     * is loaded and there exist a reference `author_id` in the DB
     */
    private function getPostAuthor(&$array)
    {
        if (!$this->relationLoaded('author')) {
            return;
        }

        $author = $this->author;
        if (empty($author)) {
            return;
        }

        $array['author'] = [
            'slug' => $author->slug,
            'name' => $author->username
        ];
    }

    /**
     * add the category to the array only if the relation `category`
     * is loaded and there exist a reference `category_id` in the DB
     */
    private function getPostCategory(&$array)
    {
        if (!$this->relationLoaded('category')) {
            return;
        }

        $category = $this->category;
        if (empty($category)) {
            return;
        }

        $array['category'] = [
            'id'    => $category->id,
            'slug'  => $category->slug,
            'title' => $category->title
        ];
    }
}
