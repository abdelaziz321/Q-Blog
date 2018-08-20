<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $joinedAt = null;
        if ($this->joined_at) {
            $joinedAt = now()->createFromFormat('Y-m-d H:i:s', $this->joined_at)
                             ->diffForHumans();
        }

        $array = [
            'id'                    => $this->id,
            'avatar'                => $this->avatar,
            'slug'                  => $this->slug,
            'username'              => $this->username,
            'email'                 => $this->email,
            'about'                 => $this->description,
            'joined_at'             => $joinedAt,
            'role'                  => $this->role(),
            'total_posts'           => $this->posts_count,
            'total_comments'        => $this->comments_count,
            'total_votes'           => $this->votes_count,
            'total_recommendations' => $this->recommendations_count
        ];

        $this->getModeratorCategory($array);

        return $array;
    }

    public function getModeratorCategory(&$array)
    {
        if ($array['role'] != 'moderator') {
            return;
        }

        $category = $this->category()->first();
        if (empty($category)) {
            return;
        }

        $array['category'] = [
            'id'    => $this->id,
            'slug'  => $this->slug,
            'title' => $this->title
        ];
    }
}
