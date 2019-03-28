<?php

namespace Plus\Api;

use Faker\Generator;
use Illuminate\Support\Str;

class ResourceFaker
{
    /**
     * The resource that faked.
     *
     * @var Resource
     */
    private $resource;

    /**
     * Store collection for faked resources.
     *
     * @var Collection
     */
    public $collection;

    /**
     * Construct ResourceFaker.
     *
     * @param Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
        $this->collection = collect();
    }

    /**
     * Add include query to the resource.
     *
     * @param  string $value
     * @return $this
     */
    public function include($value)
    {
        $this->resource->query['include'] = $value;

        return $this;
    }

    /**
     * Create an faked resource.
     *
     * @param  array  $overrides
     * @return Resource
     */
    public function create($overrides = [])
    {
        $attributes = array_merge(
            $this->resource->mock(app(Generator::class)),
            $overrides
        );

        $this->collection->push([
            'path' => $this->resource->path(),
            'resource' => $this->resource->make($attributes),
        ]);

        return $resource;
    }

    /**
     * Get faked resources for the resource.
     *
     * @param  Resource $resource
     * @return Collection
     */
    public function get(Resource $resource)
    {
        return $this->collection->filter(function ($item) use ($resource) {
            return Str::contains($resource->path(), $item['path']);
        })->pluck('resource');
    }
}
