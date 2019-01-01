<?php

namespace App\Http\Resources;

use App\Repositories\BaseRepository;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginatedCollection extends ResourceCollection
{
    private $total;
    private $perPage;
    public $collects;

    public function __construct($resource, string $collects, int $total, int $perPage)
    {
        $this->total = $total;
        $this->perPage = $perPage;
        $this->collects = 'App\\Http\\Resources\\' . $collects . 'Resource';

        parent::__construct($resource->values());
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'total'    => $this->total,
            'per_page' => $this->perPage,
            'data'     => $this->collection
        ];
    }
}
