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
            'avatar'                => $this->when($this->avatar,  $this->avatar),
            'slug'                  => $this->slug,
            'username'              => $this->username,
            'email'                 => $this->email,
            'about'                 => $this->when($this->description,  $this->description),
            'joined_at'             => $joinedAt,
            'role'                  => $this->role(),
            'total_posts'           => $this->when($this->posts_count, $this->posts_count),
            'total_comments'        => $this->when($this->comments_count, $this->comments_count),
            'total_votes'           => $this->when($this->votes_count, $this->votes_count),
            'total_recommendations' => $this->when($this->recommendations_count, $this->recommendations_count)
        ];

        return $array;
    }
}
