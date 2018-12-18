<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $array = (new PostRowResource($this))->toArray($request);
        $array['body'] = $this->body;
        $array['caption'] = $this->caption;
        $array['recommended'] = $this->recommended;

        $this->getPostTags($array);

        return $array;
    }

    /**
     * add the tags to the array only if the relation `tags`
     */
    private function getPostTags(&$array)
    {
        if (!$this->relationLoaded('tags')) {
            return;
        }
        $tags = $this->tags;

        if (empty($tags)) {
            return;
        }

        $tags->transform(function($tag) {
            return [
                'id'    => $tag->id,
                'name'  => $tag->name,
                'slug'  => $tag->slug,
            ];
        });

        $array['tags'] = $tags;
    }
}
