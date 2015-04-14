<?php

namespace adLDAP\Objects;

use adLDAP\Exceptions\adLDAPException;

/**
 * Class User
 * @package adLDAP\Objects
 */
class User extends AbstractObject
{
    /**
     * The required attributes for the toSchema method
     *
     * @var array
     */
    protected $required = array(
        'username',
        'firstname',
        'surname',
        'email',
        'container',
    );

    /**
     * Checks the attributes for errors and returns the attributes array.
     *
     * @return array
     * @throws adLDAPException
     */
    public function toSchema()
    {
        $this->validateRequired();

        // Set the display name if it's not set
        if ($this->getAttribute('display_name') === null)
        {
            $displayName = $this->getAttribute('firstname') . " " . $this->getAttribute('surname');

            $this->setAttribute('display_name', $displayName);
        }

        return $this->getAttributes();
    }

    /**
     * Validates the the required or specified attributes.
     *
     * @param array $only
     * @return bool
     * @throws adLDAPException
     */
    public function validateRequired($only = array())
    {
        parent::validateRequired($only);

        if ( ! is_array($this->getAttribute('container'))) throw new adLDAPException('Container attribute must be an array');

        return true;
    }
}