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
     * Applies the proper object attributes if they exist.
     *
     * @param array $attributes
     */
    public function applyAttributes($attributes)
    {
        if (array_key_exists('count', $attributes) && $attributes['count'] > 0)
        {
            $keys = array_keys($attributes);

            foreach ($keys as $key)
            {
                if (is_array($attributes[$key]) && array_key_exists(0, $attributes[$key]))
                {
                    $this->setAttribute($key, $attributes[$key][0]);
                }
            }

            // Convert distinguished name string into an array
            if ($this->hasAttribute('distinguishedname'))
            {
                $dn = $this->getAttribute('distinguishedname');

                $this->setAttribute('dn', $dn);
                $this->setAttribute('dn_array', $this->connection->explodeDn($dn, true));
            }

            // Convert the object category string into an array
            if ($this->hasAttribute('objectcategory'))
            {
                $oc = $this->getAttribute('objectcategory');

                $this->setAttribute('oc', $oc);
                $this->setAttribute('oc_array', $this->connection->explodeDn($oc, true));
            }
        }
    }
}
