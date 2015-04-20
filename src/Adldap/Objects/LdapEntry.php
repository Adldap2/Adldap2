<?php

namespace Adldap\Objects;

use Adldap\Interfaces\ConnectionInterface;

/**
 * Class LdapEntry
 * @package Adldap\Objects
 */
class LdapEntry extends AbstractObject
{
    /**
     * The current LDAP connection.
     *
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * Constructor.
     *
     * @param array $attributes
     * @param ConnectionInterface $connection
     */
    public function __construct(array $attributes = array(), ConnectionInterface $connection)
    {
        // Set the connection
        $this->connection = $connection;

        // Construct the entry
        $this->applyAttributes($attributes);
    }

    /**
     * Applies the proper object attributes if they exist
     * inside the specified attributes array.
     *
     * @param array $attributes
     */
    public function applyAttributes($attributes)
    {
        // Apply generic attributes
        $this->applyGenericAttributes($attributes);

        // Apply user attributes
        $this->applyUserAttributes($attributes);

        // Apply computer attributes
        $this->applyComputerAttributes($attributes);
    }

    /**
     * Applies the LDAP entry's generic attributes.
     *
     * @param array $attributes
     */
    public function applyGenericAttributes(array $attributes = array())
    {
        // Does the entry contain a common name?
        if(array_key_exists('cn', $attributes))
        {
            $this->setAttribute('cn', $attributes['cn'][0]);
        }

        // Does the entry contain a description?
        if(array_key_exists('description', $attributes))
        {
            $this->setAttribute('description', $attributes['description'][0]);
        }

        // Does the entry contain a distinguished name?
        if(array_key_exists('dn', $attributes) || array_key_exists('distinguishedname', $attributes))
        {
            $dn = (array_key_exists('dn', $attributes)) ? $attributes['dn'] : $attributes['distinguishedname'][0];

            // We'll assign the string distinguished name
            $this->setAttribute('dn', $dn);

            // As well as parse it into a array
            $this->setAttribute('dn_array', $this->connection->explodeDn($dn, true));
        }

        // Does the entry contain an object category?
        if(array_key_exists('objectcategory', $attributes))
        {
            $this->setAttribute('objectcategory', $attributes['objectcategory'][0]);
        }

        if(array_key_exists('info', $attributes))
        {
            $this->setAttribute('info', $attributes['info'][0]);
        }
    }

    /**
     * Applies the entry's user attributes.
     *
     * @param array $attributes
     */
    public function applyUserAttributes(array $attributes = array())
    {
        // Does the entry contain a display name?
        if(array_key_exists('displayname', $attributes))
        {
            $this->setAttribute('displayname', $attributes['displayname'][0]);
        }

        if(array_key_exists('displayNamePrintable', $attributes))
        {
            $this->setAttribute('displayNamePrintable', $attributes['displayNamePrintable'][0]);
        }

        // Does the entry contain a logon name?
        if(array_key_exists('samaccountname', $attributes))
        {
            $this->setAttribute('samaccountname', $attributes['samaccountname'][0]);
        }

        // Check for another logon name attribute
        if(array_key_exists('userPrincipalName', $attributes))
        {
            $this->setAttribute('userPrincipalName', $attributes['userPrincipalName'][0]);
        }

        if(array_key_exists('employeeID', $attributes))
        {
            $this->setAttribute('employeeID', $attributes['employeeID'][0]);
        }

        if(array_key_exists('mail', $attributes))
        {
            $this->setAttribute('mail', $attributes['mail'][0]);
        }

        if(array_key_exists('initials', $attributes))
        {
            $this->setAttribute('initials', $attributes['initials'][0]);
        }

        if(array_key_exists('company', $attributes))
        {
            $this->setAttribute('company', $attributes['company'][0]);
        }

        if(array_key_exists('department', $attributes))
        {
            $this->setAttribute('department', $attributes['department'][0]);
        }
    }

    /**
     * Applies the entry's computer attributes.
     *
     * @param array $attributes
     */
    public function applyComputerAttributes(array $attributes = array())
    {
        // Does the entry contain an operating system?
        if(array_key_exists('operatingsystem', $attributes))
        {
            $this->setAttribute('operatingsystem', $attributes['operatingsystem'][0]);
        }

        // Does the entry contain an operating system version?
        if(array_key_exists('operatingsystemversion', $attributes))
        {
            $this->setAttribute('operatingsystemversion', $attributes['operatingsystemversion'][0]);
        }

        // Does the entry contain an operating system service pack?
        if(array_key_exists('operatingsystemservicepack', $attributes))
        {
            $this->setAttribute('operatingsystemservicepack', $attributes['operatingsystemservicepack'][0]);
        }
    }
}
