<?php

namespace Plus\Api;

use Illuminate\Support\Collection;
use RuntimeException;
use Zttp\Zttp;

class Request
{
    /**
     * The resource that requesting.
     *
     * @var Resource
     */
    private $resource;

    /**
     * Construct Request
     *
     * @param Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Add include parameter to the reosurce.
     *
     * @param  string $include
     * @return $this
     */
    public function include($include)
    {
        $this->resource->query['include'] = $include;

        return $this;
    }

    /**
     * Add a filter to the resource.
     *
     * @param  string $key
     * @param  string $value
     * @return $this
     */
    public function filter($key, $value)
    {
        $this->resource->query['filter'][$key] = $value;

        return $this;
    }

    /**
     * Get the faker of the resource.
     *
     * @return ResourceFaker
     */
    public function getFaker()
    {
        return Resource::$faker[get_class($this->resource)];
    }

    /**
     * Determine if the request is faking.
     *
     * @return boolean
     */
    public function isFaking()
    {
        return isset(Resource::$faker[get_class($this->resource)]);
    }

    /**
     * Send the request and get the result.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        return $this->isFaking() ?
            $this->getFaker()->get($this->resource) : $this->send();
    }

    /**
     * Send the http request.
     *
     * @return Collection
     */
    public function send()
    {
        $response = Zttp::withHeader(['Accept' => 'application/json'])->get($this->resource->url());

        if ($response->isOk()) {
            return collect($response->json()['data'])->map(function ($item) {
                return $this->resource->make($item);
            });
        }

        throw new RuntimeException($response->json());
    }
}
