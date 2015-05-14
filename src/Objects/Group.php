<?php

namespace Adldap\Objects;

use Adldap\Exceptions\AdldapException;

/**
 * Class Group.
 */
class Group extends AbstractObject
{
    /**
     * The required attributes to validate against
     * when calling the method validateRequired.
     *
     * @var array
     */
    protected $required = [
        'group_name',
        'description',
        'container',
    ];

    /**
     * Validate the required attributes.
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function validateRequired()
    {
        parent::validateRequired();

        if (!is_array($this->getAttribute('container'))) {
            $message = 'Container attribute must be an array.';

            throw new AdldapException($message);
        }

        return true;
    }
}
