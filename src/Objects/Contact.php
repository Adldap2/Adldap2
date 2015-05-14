<?php

namespace Adldap\Objects;

use Adldap\Exceptions\AdldapException;

/**
 * Class Contact.
 */
class Contact extends AbstractObject
{
    /**
     * The required attributes.
     *
     * @var array
     */
    protected $required = [
        'display_name',
        'email',
        'container',
    ];

    /**
     * Validates the required attributes.
     *
     * @param array $only
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function validateRequired($only = [])
    {
        parent::validateRequired($only);

        if (!is_array($this->getAttribute('container'))) {
            throw new AdldapException('Container attribute must be an array.');
        }

        return true;
    }
}
