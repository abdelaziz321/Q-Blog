<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'message' => $this->message,
            'user'    => [
                'slug'   => $this->user->slug,
                'name'   => $this->user->username,
                'avatar' => $this->user->avatar
            ]
        ];
    }
}
