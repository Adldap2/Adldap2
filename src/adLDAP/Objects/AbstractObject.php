<?php

namespace adLDAP\Objects;

/**
 * Class AbstractObject
 * @package adLDAP\Objects
 */
abstract class AbstractObject
{
    /**
     * Holds the current objects attributes.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->setAttributes($attributes);
    }

    /**
     * Dynamically retrieve attributes on the object.
     *
     * @param $key
     * @return bool
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Retrieves the specified key from the attribute array.
     *
     * @param $key
     * @return bool
     */
    public function getAttribute($key)
    {
        if(array_key_exists($key, $this->attributes))
        {
            return $this->attributes[$key];
        }

        return null;
    }

    /**
     * Sets the key on the objects attribute array.
     *
     * @param int|string $key
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Sets the attributes property
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes = array())
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Returns the objects attributes to a adLDAP compatible
     * array. Array key checking is done here before returning.
     *
     * @return array
     */
    abstract public function toSchema();
}