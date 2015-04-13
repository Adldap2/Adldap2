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
     * Checks the attributes for errors and returns the attributes array.
     *
     * @return array
     * @throws adLDAPException
     */
    public function toSchema()
    {
        // Holds errors with the current attributes
        $errors = array();

        if ($this->getAttribute('username') === null) $errors[] = 'Missing compulsory field [username]';

        if ($this->getAttribute('firstname') === null) $errors[] = 'Missing compulsory field [firstname]';

        if ($this->getAttribute('surname') === null) $errors[] = 'Missing compulsory field [surname]';

        if ($this->getAttribute('email') === null) $errors[] = 'Missing compulsory field [email]';

        if ($this->getAttribute('container') === null) $errors[] = 'Missing compulsory field [container]';

        if ( ! is_array($this->getAttribute('container'))) $errors[] = 'Container attribute must be an array';

        // Set the display name if it's not set
        if ($this->getAttribute('display_name') === null)
        {
            $displayName = $this->getAttribute('firstname') . " " . $this->getAttribute('surname');

            $this->setAttribute('display_name', $displayName);
        }

        // Throw the first error in the array
        if (count($errors) > 0) throw new adLDAPException($errors[0]);

        return $this->attributes;
    }
}