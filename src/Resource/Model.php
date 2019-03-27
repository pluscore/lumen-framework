<?php

namespace Plus\Resource;

use Illuminate\Contracts\Support\Arrayable;

class Model implements Arrayable
{
    /**
     * Attributes of the model.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Construct ResourceModel.
     *
     * @param array $attributes
     */
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Convert to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }
}
