<?php

namespace Adldap\Schemas;

use Adldap\Contracts\Schemas\SchemaInterface;

class Schema
{
    /**
     * The current LDAP attribute schema.
     *
     * @var SchemaInterface
     */
    protected static $current;

    /**
     * Returns the current LDAP attribute schema.
     *
     * @return SchemaInterface
     */
    public static function get()
    {
        if (!static::$current instanceof SchemaInterface) {
            static::set(static::getDefault());
        }

        return static::$current;
    }

    /**
     * Sets the current LDAP attribute schema.
     *
     * @param SchemaInterface $schema
     */
    public static function set(SchemaInterface $schema)
    {
        static::$current = $schema;
    }

    /**
     * Returns a new instance of the default schema.
     *
     * @return SchemaInterface
     */
    public static function getDefault()
    {
        return new ActiveDirectory();
    }
}
