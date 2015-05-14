<?php

namespace Adldap\Objects;

use Adldap\Exceptions\AdldapException;

/**
 * Class Folder.
 */
class Folder extends AbstractObject
{
    /**
     * The required attributes to validate
     * when validateRequired is called.
     *
     * @var array
     */
    protected $required = [
        'ou_name',
        'container',
    ];

    /**
     * Validates the required attributes. This will throw
     * an AdldapException on failure.
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function validateRequired()
    {
        parent::validateRequired();

        if (!is_array($this->getAttribute('container'))) {
            $message = 'Container attribute must be an array';

            throw new AdldapException($message);
        }

        return true;
    }
}
