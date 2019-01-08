<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $createdAt = now()->createFromFormat('Y-m-d H:i:s', $this->created_at ?? now())
                          ->diffForHumans();

        $updatedAt = now()->createFromFormat('Y-m-d H:i:s', $this->updated_at ?? now())
                          ->diffForHumans();

        $array = [
            'id'         => $this->id,
            'body'       => $this->body,
            'votes'      => $this->votes ?? 0,
            'voted'      => $this->voted ?? 0,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'user'       => [
                'slug' => $this->user->slug,
                'name' => $this->user->username,
                'avatar' => $this->user->avatar
            ]
        ];

        $this->getCommentPost($array);

        return $array;
    }

    /**
     * add the post of the comment to the array only if the relation is loaded
     */
    private function getCommentPost(&$array)
    {
        if (!$this->relationLoaded('post')) {
            return;
        }

        $post = $this->post;
        if (empty($post)) {
            return;
        }

        $array['post'] = [
            'title'    => $post->title,
            'slug'     => $post->slug,
            'author'   => $post->author_id,
            'category' => $post->category_id
        ];
    }
}
