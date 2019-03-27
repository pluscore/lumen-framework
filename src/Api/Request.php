<?php

namespace Plus\Api;

use Illuminate\Support\Str;
use Zttp\Zttp;

class Request
{
    private $resource;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function include($include)
    {
        $this->resource->query['include'] = $include;

        return $this;
    }

    public function filter($key, $value)
    {
        $this->resource->query['filter'][$key] = $value;

        return $this;
    }

    public function get()
    {
        if ($faker = get_class($this->resource)::$faker) {
            return $faker->collection
                ->filter(function ($item) {
                    return Str::contains($this->resource->path(), $item['path']);
                })
                ->pluck('resource');
        }

        $response = Zttp::get($this->resource->path());

        if ($response->isOk()) {
            return collect($response->json()['data'])->map(function ($item) {
                $resourceClass = get_class($this->resource);
                return new $resourceClass($item);
            });
        }

        throw new \RuntimeException;
    }
}
