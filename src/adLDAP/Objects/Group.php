<?php

namespace adLDAP\Objects;

use adLDAP\Exceptions\adLDAPException;

/**
 * Class Group
 * @package adLDAP\Objects
 */
class Group extends AbstractObject
{
    /**
     * The required attributes to validate against
     * when calling the method validateRequired.
     *
     * @var array
     */
    protected $required = array(
        'group_name',
        'description',
        'container',
    );

    /**
     * Validate the required attributes.
     *
     * @return bool
     * @throws adLDAPException
     */
    public function validateRequired()
    {
        parent::validateRequired();

        if( ! is_array($this->getAttribute('container')))
        {
            $message = 'Container attribute must be an array.';

            throw new adLDAPException($message);
        }

        return true;
    }
}