<?php

namespace Adldap\Classes;

class Utilities
{
    /**
     * Converts a DN string into an array.
     *
     * @param string $dn
     * @param bool   $removeAttributePrefixes
     *
     * @return array
     */
    public static function explodeDn($dn, $removeAttributePrefixes = true)
    {
        return ldap_explode_dn($dn, ($removeAttributePrefixes ? 1 : 0));
    }

    /**
     * Returns true / false if the current
     * PHP install supports escaping values.
     *
     * @return bool
     */
    public static function isEscapingSupported()
    {
        return function_exists('ldap_escape');
    }

    /**
     * Returns an escaped string for use in an LDAP filter.
     *
     * @param string $value
     * @param string $ignore
     * @param $flags
     *
     * @return string
     */
    public static function escape($value, $ignore = '', $flags = 0)
    {
        if (!self::isEscapingSupported()) {
            return self::escapeManual($value, $ignore, $flags);
        }

        return ldap_escape($value, $ignore, $flags);
    }

    /**
     * Escapes the inserted value for LDAP.
     *
     * @param string $value
     * @param string $ignore
     * @param int    $flags
     *
     * @return string
     */
    protected static function escapeManual($value, $ignore = '', $flags = 0)
    {
        // If a flag was supplied, we'll send the value off
        // to be escaped using the PHP flag values
        // and return the result.
        if ($flags) {
            return self::escapeManualWithFlags($value, $ignore, $flags);
        }

        // Convert ignore string into an array
        $ignores = self::ignoreStrToArray($ignore);

        // Convert the value to a hex string
        $hex = bin2hex($value);

        // Separate the string, with the hex length of 2, and
        // place a backslash on the end of each section
        $value = chunk_split($hex, 2, '\\');

        // We'll append a backslash at the front of the string
        // and remove the ending backslash of the string
        $value = '\\'.substr($value, 0, -1);

        // Go through each character to ignore
        foreach ($ignores as $charToIgnore) {
            // Convert the character to ignore to a hex
            $hexed = bin2hex($charToIgnore);

            // Replace the hexed variant with the original character
            $value = str_replace('\\'.$hexed, $charToIgnore, $value);
        }

        // Finally we can return the escaped value
        return $value;
    }

    /**
     * Escapes the inserted value with flags. Supplying either 1
     * or 2 into the flags parameter will escape only certain values.
     *
     *
     * @param string $value
     * @param string $ignore
     * @param int    $flags
     *
     * @return bool|mixed
     */
    protected static function escapeManualWithFlags($value, $ignore = '', $flags = 0)
    {
        // Convert ignore string into an array
        $ignores = self::ignoreStrToArray($ignore);

        // The escape characters for search filters
        $escapeFilter = ['\\', '*', '(', ')'];

        // The escape characters for distinguished names
        $escapeDn = ['\\', ',', '=', '+', '<', '>', ';', '"', '#'];

        switch ($flags) {
            case 1:
                // Int 1 equals to LDAP_ESCAPE_FILTER
                $escapes = $escapeFilter;
                break;
            case 2:
                // Int 2 equals to LDAP_ESCAPE_DN
                $escapes = $escapeDn;
                break;
            case 3:
                // If both LDAP_ESCAPE_FILTER and LDAP_ESCAPE_DN are used
                $escapes = array_merge($escapeFilter, $escapeDn);
                break;
            default:
                return false;
        }

        foreach ($escapes as $escape) {
            // Make sure the escaped value isn't being ignored
            if (!in_array($escape, $ignores)) {
                $hexed = chunk_split(bin2hex($escape), 2, '\\');

                $hexed = '\\'.substr($hexed, 0, -1);

                $value = str_replace($escape, $hexed, $value);
            }
        }

        return $value;
    }

    /**
     * Un-escapes a hexadecimal string into
     * its original string representation.
     *
     * @param string $value
     *
     * @return string
     */
    public static function unescape($value)
    {
        $callback = function ($matches) {
            return chr(hexdec($matches[1]));
        };

        return preg_replace_callback('/\\\([0-9A-Fa-f]{2})/', $callback, $value);
    }

    /**
     * Convert a binary SID to a text SID.
     *
     * @param string $binsid A Binary SID
     *
     * @return string
     */
    public static function binarySidToText($binsid)
    {
        $hex_sid = bin2hex($binsid);

        $rev = hexdec(substr($hex_sid, 0, 2));

        $subcount = hexdec(substr($hex_sid, 2, 2));

        $auth = hexdec(substr($hex_sid, 4, 12));

        $result = "$rev-$auth";

        $subauth = [];

        for ($x = 0; $x < $subcount; $x++) {
            $subauth[$x] = hexdec(self::littleEndian(substr($hex_sid, 16 + ($x * 8), 8)));

            $result .= '-'.$subauth[$x];
        }

        // Cheat by tacking on the S-
        return 'S-'.$result;
    }

    /**
     * Converts a little-endian hex number to one that hexdec() can convert.
     *
     * @param string $hex A hex code
     *
     * @return string
     */
    public static function littleEndian($hex)
    {
        $result = '';

        for ($x = strlen($hex) - 2; $x >= 0; $x = $x - 2) {
            $result .= substr($hex, $x, 2);
        }

        return $result;
    }

    /**
     * Encode a password for transmission over LDAP.
     *
     * @param string $password The password to encode
     *
     * @return string
     */
    public static function encodePassword($password)
    {
        return iconv('UTF-8', 'UTF-16LE', '"'.$password.'"');
    }

    /**
     * Round a Windows timestamp down to seconds and remove
     * the seconds between 1601-01-01 and 1970-01-01.
     *
     * @param float $windowsTime
     *
     * @return float
     */
    public static function convertWindowsTimeToUnixTime($windowsTime)
    {
        return round($windowsTime / 10000000) - 11644473600;
    }

    /**
     * Convert a Unix timestamp to Windows timestamp.
     *
     * @param float $unixTime
     *
     * @return float
     */
    public static function convertUnixTimeToWindowsTime($unixTime)
    {
        return ($unixTime + 11644473600) * 10000000;
    }

    /**
     * Converts an ignore string into an array.
     *
     * @param string $ignore
     *
     * @return array
     */
    private static function ignoreStrToArray($ignore)
    {
        $ignore = trim($ignore);

        if (!empty($ignore)) {
            return str_split($ignore);
        }

        return [];
    }
}
