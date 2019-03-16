<?php

namespace Plus\Auth;

use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Support\Arrayable;

class User extends GenericUser implements Arrayable
{
    /**
     * Determine if the user is an admin.
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return isset($this->attributes['is_admin']) && $this->attributes['is_admin'];
    }

    /**
     * Determine if the user is an officer of the given publisher.
     *
     * @param  string  $publisherId
     * @return boolean
     */
    public function isOfficerOf($publisherId)
    {
        return collect($this->attributes['publishers'])->where('id', $publisherId)->count() > 0;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }
}
