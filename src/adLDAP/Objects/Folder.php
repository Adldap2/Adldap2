<?php

namespace adLDAP\Objects;

use adLDAP\Exceptions\adLDAPException;

/**
 * Class Folder
 * @package adLDAP\Objects
 */
class Folder extends AbstractObject
{
    /**
     * The required attributes to validate
     * when validateRequired is called.
     *
     * @var array
     */
    protected $required = array(
        'ou_name',
        'container'
    );

    /**
     * Validates the required attributes. This will throw
     * an adLDAPException on failure.
     *
     * @return bool
     * @throws adLDAPException
     */
    public function validateRequired()
    {
        parent::validateRequired();

        if ( ! is_array($this->getAttribute('container')))
        {
            $message = 'Container attribute must be an array';

            throw new adLDAPException($message);
        }

        return true;
    }
}
