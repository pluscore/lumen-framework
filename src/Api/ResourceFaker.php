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
     * The resource that faked.
     *
     * @var Resource
     */
    private $request;

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
        $this->request = $this->resource->newRequest();
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
        $this->request->include($value);

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
            'url' => $this->request->url(),
            'resource' => $resource = $this->resource->make($attributes),
        ]);

        return $resource;
    }

    /**
     * Get faked resources for the resource.
     *
     * @param  Request $request
     * @return Collection
     */
    public function get(Request $request)
    {
        return $this->collection->filter(function ($item) use ($request) {
            return Str::contains($request->url(), $item['url']);
        })->pluck('resource');
    }
}
