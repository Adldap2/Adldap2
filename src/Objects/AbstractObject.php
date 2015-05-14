<?php

namespace Adldap\Objects;

use Adldap\Exceptions\AdldapException;

/**
 * Class AbstractObject.
 */
abstract class AbstractObject
{
    /**
     * The validation messages of the object.
     *
     * @var array
     */
    public $messages = [
        'required' => 'Missing compulsory field [%s]',
    ];

    /**
     * The required attributes for the toSchema methods.
     *
     * @var array
     */
    protected $required = [];

    /**
     * Holds the current objects attributes.
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
     * @param $key
     *
     * @return bool
     */
    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return;
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
     * Sets the key on the objects attribute array.
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
     * @param $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        if (array_key_exists($attribute, $this->attributes)) {
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

    /**
     * Sets the required attributes for validation.
     *
     * @param array $required
     *
     * @return $this
     */
    public function setRequired(array $required = [])
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Validates the required attributes for preventing null keys.
     *
     * If an array is provided, then the specified required attributes
     * are only validated.
     *
     * @param array $only
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function validateRequired($only = [])
    {
        if (count($only) > 0) {
            return $this->validateSpecific($only);
        }

        /*
         * Go through each required attribute
         * and make sure they're not null
         */
        foreach ($this->required as $required) {
            if ($this->getAttribute($required) === null) {
                throw new AdldapException(sprintf($this->messages['required'], $required));
            }
        }

        return true;
    }

    /**
     * Validates the specified attributes inside the required array.
     *
     * The attributes inside the required array must also exist inside the
     * required property.
     *
     * @param array $required
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function validateSpecific(array $required = [])
    {
        foreach ($required as $field) {
            /*
             * If the field is in the required array, and the
             * object attribute equals null, we'll throw an exception.
             */
            if (in_array($field, $this->required) && $this->getAttribute($field) === null) {
                throw new AdldapException(sprintf($this->messages['required'], $field));
            }
        }

        return true;
    }
}
