<?php

namespace Plus\Api;

use Illuminate\Support\Collection;
use RuntimeException;
use Zttp\Zttp;

class Request
{
    /**
     * The resource class that requesting.
     *
     * @var Resource
     */
    private $resource;

    /**
     * The http url to the resource.
     *
     * @var string
     */
    protected $url;

    /**
     * Query string to the resource.
     *
     * @var array
     */
    protected $query;

    /**
     * Construct Request
     *
     * @param Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
        $this->url = $this->resource->url;
        $this->query = $this->resource->query;
    }

    /**
     * Add include parameter to the reosurce.
     *
     * @param  string $include
     * @return $this
     */
    public function include($include)
    {
        $this->query['include'] = $include;

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
        $this->query['filter'][$key] = $value;

        return $this;
    }

    /**
     * Get the url to the resource. Build full url with url and queryt string.
     *
     * @return string
     */
    public function url($key = null)
    {
        $query = http_build_query($this->query);

        return $this->url.($key ? "/{$key}" : '').($query ? "?{$query}" : '');
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
            $this->getFaker()->get($this) : $this->mapResourceArray($this->send($this->url()));
    }

    public function mapResourceArray($array)
    {
        return collect($array)->map(function ($attributes) {
            return $this->mapResource($attributes);
        });
    }

    public function mapResource($attributes)
    {
        return $this->resource->make($attributes);
    }

    /**
     * Find a resource by its key.
     *
     * @param  string $key
     * @return Resource
     */
    public function find($key)
    {
        return $this->mapResource(
            $this->send($this->url($key))
        );
    }

    /**
     * Send the http request.
     *
     * @return Collection
     */
    public function send($url)
    {
        $response = Zttp::withHeaders(['Accept' => 'application/json'])->get($url);

        if ($response->isOk()) {
            return $response->json()['data'];
        }

        throw new RuntimeException($response->body());
    }
}
