<?php

namespace Adldap\Attributes;

class Guid
{
    /**
     * Determines if the specified GUID is valid.
     *
     * @param string $guid
     *
     * @return bool
     */
    public static function isValid($guid)
    {
        return (bool) preg_match(
            '/^([0-9a-fA-F]){8}(-([0-9a-fA-F]){4}){3}-([0-9a-fA-F]){12}$/',
            $guid
        );
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        //
    }
}
