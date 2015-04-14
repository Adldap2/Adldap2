<?php

namespace adLDAP\Objects;

use adLDAP\Exceptions\adLDAPException;

/**
 * Class Contact
 * @package adLDAP\Objects
 */
class Contact extends AbstractObject
{
    /**
     * The required attributes.
     *
     * @var array
     */
    protected $required = array(
        'display_name',
        'email',
        'container'
    );

    /**
     * Validates the required attributes.
     *
     * @throws adLDAPException
     */
    public function validateRequired()
    {
        parent::validateRequired();

        if ( ! is_array($this->getAttribute("container"))) throw new adLDAPException("Container attribute must be an array.");

        return true;
    }
}