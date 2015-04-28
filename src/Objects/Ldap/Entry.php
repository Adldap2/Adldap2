<?php

namespace Adldap\Objects\Ldap;

use Adldap\Interfaces\ConnectionInterface;
use Adldap\Objects\AbstractObject;

/**
 * Class LdapEntry.
 */
class Entry extends AbstractObject
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
     * @param array               $attributes
     * @param ConnectionInterface $connection
     */
    public function __construct(array $attributes = [], ConnectionInterface $connection)
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
    private function applyAttributes($attributes)
    {
        if (array_key_exists('count', $attributes) && $attributes['count'] > 0) {
            $keys = array_keys($attributes);

            foreach ($keys as $key) {
                if (is_array($attributes[$key]) && array_key_exists(0, $attributes[$key])) {
                    // If the entry has multiple attributes, we'll make sure we loop through each one
                    if (array_key_exists('count', $attributes[$key]) && $attributes[$key]['count'] > 1) {
                        $data = [];

                        for ($i = 0; $i <= $attributes[$key]['count']; $i++) {
                            $data[] = $attributes[$key][$i];
                        }

                        $this->setAttribute($key, array_filter($data));
                    } else {
                        // Looks like only one attribute exists, let's set it
                        $this->setAttribute($key, $attributes[$key][0]);
                    }
                }
            }

            $this->applyExtraAttributes();
        }
    }

    /**
     * Applies extra attributes to the returned array.
     */
    private function applyExtraAttributes()
    {
        // Convert distinguished name string into an array
        if ($this->hasAttribute('distinguishedname')) {
            $dn = $this->getAttribute('distinguishedname');

            $this->setAttribute('dn', $dn);

            $this->setAttribute('dn_array', $this->connection->explodeDn($dn, true));
        }

        // Convert the object category string into an array
        if ($this->hasAttribute('objectcategory')) {
            $oc = $this->getAttribute('objectcategory');

            $this->setAttribute('oc', $oc);

            $this->setAttribute('oc_array', $this->connection->explodeDn($oc, true));
        }
    }
}
