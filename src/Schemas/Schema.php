<?php

namespace Adldap\Schemas;

class Schema
{
    /**
     * The current schema.
     *
     * @var SchemaInterface
     */
    protected static $current;

    /**
     * Returns the current schema.
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
     * @param SchemaInterface $schema
     */
    public static function set(SchemaInterface $schema)
    {
        self::$current = $schema;
    }

    /**
     * @return SchemaInterface
     */
    public static function getDefault()
    {
        return new ActiveDirectory();
    }
}
