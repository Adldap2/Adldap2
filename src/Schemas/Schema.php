<?php

namespace Adldap\Schemas;

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
        if (!self::$current instanceof SchemaInterface) {
            self::set(self::getDefault());
        }

        return self::$current;
    }

    /**
     * Sets the current LDAP attribute schema.
     *
     * @param SchemaInterface $schema
     */
    public static function set(SchemaInterface $schema)
    {
        self::$current = $schema;
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
