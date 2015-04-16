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
        $this->connection = $connection;

        $this->constructEntry($attributes);
    }

    /**
     * Assigns the proper object attributes if they exist
     * inside the specified attributes array.
     *
     * @param array $attributes
     */
    public function constructEntry($attributes)
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

        // Does the entry contain a display name?
        if(array_key_exists('displayname', $attributes))
        {
            $this->setAttribute('displayname', $attributes['displayname'][0]);
        }

        // Does the entry contain a logon name?
        if(array_key_exists('samaccountname', $attributes))
        {
            $this->setAttribute('samaccountname', $attributes['samaccountname'][0]);
        }

        // Does the entry contain a distinguished name?
        if(array_key_exists('distinguishedname', $attributes))
        {
            $dn = $attributes['distinguishedname'][0];

            // We'll assign the string distinguished name
            $this->setAttribute('dn', $dn);

            // As well as parse it into a array
            $this->setAttribute('dn_array', $this->connection->explodeDn($dn, true));
        }
    }
}
