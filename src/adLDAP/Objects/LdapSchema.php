<?php

namespace adLDAP\Objects;

/**
 * Class LdapSchema
 * @package adLDAP\Objects
 */
class LdapSchema extends AbstractObject
{
    /**
     * When setting attributes, we need to assign them
     * in their own int(0) key due to LDAP parsing.
     *
     * @param int|string $key
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key][0] = $value;

        return $this;
    }
}