<?php

namespace Adldap\Models\Attributes;

class MBString
{
    /**
     * Get the integer value of a specific character.
     *
     * @param $string
     * @return int
     */
    public static function ord($string)
    {
        if (self::isMbstringLoaded()) {
            $result = unpack('N', mb_convert_encoding($string, 'UCS-4BE', 'UTF-8'));
            if (is_array($result) === true) {
                return $result[1];
            }
        }

        return ord($string);
    }

    /**
     * Get the character for a specific integer value.
     *
     * @param $int
     * @return string
     */
    public static function chr($int)
    {
        if (self::isMbstringLoaded()) {
            return mb_convert_encoding(pack('n', $int), 'UTF-8', 'UTF-16BE');
        }

        return chr($int);
    }

    /**
     * Split a string into its individual characters and return it as an array.
     *
     * @param string $value
     * @return string[]
     */
    public static function str_split($value)
    {
        return preg_split('/(?<!^)(?!$)/u', $value);
    }

    /**
     * Simple check for the mbstring extension.
     *
     * @return bool
     */
    protected static function isMbstringLoaded()
    {
        return extension_loaded('mbstring');
    }
}