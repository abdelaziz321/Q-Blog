<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginatedPostCollection extends ResourceCollection
{
    private $postRepo;
    private $perPage;
    public $collects = 'App\Http\Resources\PostRowResource';

    public function __construct($resource, $perPage)
    {
        parent::__construct($resource);
        $this->perPage = $perPage;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->postRepo = resolve('App\Repositories\Post\RepositoryInterface');

        return [
            'data'     => $this->collection,
            'total'    => $this->postRepo->getTotalPosts(),
            'per_page' => $this->perPage

        ];
    }
}
