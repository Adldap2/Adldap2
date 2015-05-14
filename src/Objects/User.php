<?php

namespace Adldap\Objects;

use Adldap\Exceptions\AdldapException;

/**
 * Class User.
 */
class User extends AbstractObject
{
    /**
     * The required attributes for the toSchema methods.
     *
     * @var array
     */
    protected $required = [
        'username',
        'firstname',
        'surname',
        'email',
        'container',
    ];

    /**
     * Checks the attributes for existence and returns the attributes array.
     *
     * @return array
     *
     * @throws AdldapException
     */
    public function toCreateSchema()
    {
        $this->validateRequired();

        if (!is_array($this->getAttribute('container'))) {
            throw new AdldapException('Container attribute must be an array');
        }

        // Set the display name if it's not set
        if ($this->getAttribute('display_name') === null) {
            $displayName = $this->getAttribute('firstname').' '.$this->getAttribute('surname');

            $this->setAttribute('display_name', $displayName);
        }

        return $this->getAttributes();
    }

    /**
     * Checks the username attribute for existence and returns the attributes array.
     *
     * @return array
     *
     * @throws AdldapException
     */
    public function toModifySchema()
    {
        $this->validateRequired(['username']);

        if ($this->hasAttribute('container')) {
            if (!is_array($this->getAttribute('container'))) {
                throw new AdldapException('Container attribute must be an array');
            }
        }

        return $this->getAttributes();
    }

    public function create()
    {
    }
}
