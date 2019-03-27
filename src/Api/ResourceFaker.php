<?php

namespace Plus\Api;

use Faker\Generator;
use Illuminate\Support\Collection;
use Plus\Resource\Model;

class ResourceFaker
{
    private $resource;
    public $collection;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
        $this->collection = new Collection;
    }

    public function include($value)
    {
        $this->resource->query['include'] = $value;

        return $this;
    }

    public function create($overrides = [])
    {
        $attributes = array_merge(
            $this->resource->mock(app(Generator::class)),
            $overrides
        );

        $class = get_class($this->resource);

        $this->collection->push([
            'path' => $this->resource->path(),
            'resource' => $resource = new $class($attributes),
        ]);

        return $resource;
    }
}
