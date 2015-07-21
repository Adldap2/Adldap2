<?php

namespace Adldap\Objects;

abstract class AbstractObject
{
    /**
     * Holds the current objects original attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Constructor.
     *
     * Sets the object's attributes property.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
    }

    /**
     * Dynamically retrieve attributes on the object.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the object.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return AbstractObject
     */
    public function __set($key, $value)
    {
        return $this->setAttribute($key, $value);
    }

    /**
     * Retrieves the specified key from the attribute array.
     *
     * If a sub-key is specified, it will try
     * and retrieve it from the parent keys array.
     *
     * @param int|string $key
     * @param int|string $subKey
     *
     * @return mixed
     */
    public function getAttribute($key, $subKey = null)
    {
        if(!is_null($subKey)) {
            if ($this->hasAttribute($key, $subKey)) {
                return $this->attributes[$key][$subKey];
            }
        } else {
            if ($this->hasAttribute($key)) {
                return $this->attributes[$key];
            }
        }

        return null;
    }

    /**
     * Retrieves the attributes array property.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets an attribute in the objects attributes array.
     *
     * @param int|string $key
     * @param mixed      $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Sets the attributes property.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Returns true / false if the specified attribute
     * exists in the attributes array.
     *
     * @param int|string $key
     * @param int|string $subKey
     *
     * @return bool
     */
    public function hasAttribute($key, $subKey = null)
    {
        if (array_key_exists($key, $this->attributes)) {
            /*
             * If a sub key is given, we'll check if
             * it exists in the nested attribute array
             */
            if(!is_null($subKey)) {
                if(is_array($this->attributes[$key]) && array_key_exists($subKey, $this->attributes[$key])) {
                    return true;
                } else {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Returns the number of attributes inside
     * the attributes property.
     *
     * @return int
     */
    public function countAttributes()
    {
        return count($this->getAttributes());
    }
}
