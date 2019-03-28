<?php

namespace Plus\Api;

use Faker\Generator;
use Illuminate\Contracts\Support\Arrayable;

abstract class Resource implements Arrayable
{
    /**
     * The faker instance for the resource type.
     *
     * @var ResourceFaker
     */
    public static $faker = [];

    /**
     * The resource attributes.
     *
     * @var array
     */
    private $attributes;

    /**
     * The http url to the resource.
     *
     * @var string
     */
    public $url;

    /**
     * Query string to the resource.
     *
     * @var array
     */
    public $query;

    /**
     * Construct Resource.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
        $this->query = [];
    }

    /**
     * Enable the fake mode and get the faker.
     *
     * @return ResourceFaker
     */
    public static function fake()
    {
        $class = get_called_class();

        if (! isset(static::$faker[$class])) {
            static::$faker[$class] = new ResourceFaker(new static);
        }

        return static::$faker[$class];
    }

    /**
     * Get key name of the resource.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'id';
    }

    /**
     * Convert the resource to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Make a request instance for the resource.
     *
     * @return Request
     */
    public function newRequest()
    {
        return new Request($this);
    }

    /**
     * Make a new instance of the resource.
     *
     * @param  array  $attributes
     * @return Resource
     */
    public static function make($attributes = [])
    {
        return new static($attributes);
    }

    /**
     * Generate a mock attributes for a resource.
     *
     * @param  Generator $faker
     * @return array
     */
    public static function mock(Generator $faker)
    {
        throw new \Exception('Mock not supported in this resource.');
    }

    /**
     * Map property call to resource attribute.
     *
     * @param  string $field
     * @return mixed
     */
    public function __get($field)
    {
        return $this->attributes[$field];
    }

    /**
     * @param  string $name
     * @param  mixed $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array(
            [(new static)->newRequest(), $name],
            $arguments
        );
    }
}
