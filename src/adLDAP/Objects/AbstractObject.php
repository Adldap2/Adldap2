<?php

namespace adLDAP\Objects;

use adLDAP\Exceptions\adLDAPException;

/**
 * Class AbstractObject
 * @package adLDAP\Objects
 */
abstract class AbstractObject
{
    /**
     * The required attributes for the toSchema method
     *
     * @var array
     */
    protected $required = array();

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
     * Retrieves the attributes array property
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
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
     * Validates the required attributes for preventing null keys
     *
     * @return bool
     * @throws adLDAPException
     */
    public function validateRequired()
    {
        $errors = array();

        $message = 'Missing compulsory field [%s]';

        /*
         * Go through each required attribute
         * and make sure they're not null
         */
        foreach($this->required as $required)
        {
            if($this->getAttribute($required) === null)
            {
                $errors[] = sprintf($message, $required);
            }
        }

        if(count($errors) > 0)
        {
            // Throw an exception with the first error message
            throw new adLDAPException($errors[0]);
        }

        return true;
    }
}