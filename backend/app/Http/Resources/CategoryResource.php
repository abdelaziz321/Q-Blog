<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $array = [
            'id'            => $this->id,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'total_posts'   => $this->posts_count ?? 0
        ];
        $this->getCategoryModerator($array);

        return $array;
    }

    /**
     * add the moderator of the category to the array only if the relation is loaded.
     */
    private function getCategoryModerator(&$array)
    {
        if (!$this->relationLoaded('moderator')) {
            return;
        }

        $moderator = $this->getRelationValue('moderator');
        if (empty($moderator)) {
            return;
        }

        $array['moderator'] = [
            'id'        => $moderator->id,
            'slug'      => $moderator->slug,
            'avatar'    => $moderator->avatar,
            'username'  => $moderator->username
        ];
    }
}
