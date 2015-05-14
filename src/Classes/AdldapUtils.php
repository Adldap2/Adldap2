<?php

namespace Adldap\Classes;

use Adldap\Exceptions\AdldapException;

/**
 * AdLDAP Utility Functions.
 *
 * Class AdldapUtils
 */
class AdldapUtils extends AbstractAdldapBase
{
    /**
     * Take an LDAP query and return the nice names, without all the LDAP prefixes (eg. CN, DN).
     *
     * @param array $groups
     *
     * @return array
     */
    public function niceNames($groups)
    {
        $groupNames = [];

        if (is_array($groups) && count($groups) > 0) {
            foreach ($groups as $group) {
                $explodedDn = $this->connection->explodeDn($group);

                // Assuming the zero key is the CN
                $groupNames[] = $explodedDn[0];
            }
        } elseif (is_string($groups)) {
            // If there's a single entry, groups will be a string
            $explodedDn = $this->connection->explodeDn($groups);

            $groupNames[] = $explodedDn[0];
        }

        return $groupNames;
    }

    /**
     * Escape characters for use in an ldap_create function.
     *
     * @param string $str
     *
     * @return string
     */
    public function escapeCharacters($str)
    {
        return str_replace(',', "\,", $str);
    }

    /**
     * Converts a string GUID to a hexdecimal value so it can be queried.
     *
     * @param string $strGUID A string representation of a GUID
     *
     * @return string
     */
    public function strGuidToHex($strGUID)
    {
        $strGUID = str_replace('-', '', $strGUID);

        $octet_str = '\\'.substr($strGUID, 6, 2);
        $octet_str .= '\\'.substr($strGUID, 4, 2);
        $octet_str .= '\\'.substr($strGUID, 2, 2);
        $octet_str .= '\\'.substr($strGUID, 0, 2);
        $octet_str .= '\\'.substr($strGUID, 10, 2);
        $octet_str .= '\\'.substr($strGUID, 8, 2);
        $octet_str .= '\\'.substr($strGUID, 14, 2);
        $octet_str .= '\\'.substr($strGUID, 12, 2);

        $length = (strlen($strGUID) - 2);

        for ($i = 16; $i <= $length; $i++) {
            if (($i % 2) == 0) {
                $octet_str .= '\\'.substr($strGUID, $i, 2);
            }
        }

        return $octet_str;
    }

    /**
     * Convert a binary SID to a text SID.
     *
     * @param string $binsid A Binary SID
     *
     * @return string
     */
    public function getTextSID($binsid)
    {
        $hex_sid = bin2hex($binsid);

        $rev = hexdec(substr($hex_sid, 0, 2));

        $subcount = hexdec(substr($hex_sid, 2, 2));

        $auth = hexdec(substr($hex_sid, 4, 12));

        $result = "$rev-$auth";

        $subauth = [];

        for ($x = 0;$x < $subcount; $x++) {
            $subauth[$x] = hexdec($this->littleEndian(substr($hex_sid, 16 + ($x * 8), 8)));

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
    public function littleEndian($hex)
    {
        $result = '';

        for ($x = strlen($hex) - 2; $x >= 0; $x = $x - 2) {
            $result .= substr($hex, $x, 2);
        }

        return $result;
    }

    /**
     * Converts a binary attribute to a string.
     *
     * @param string $bin A binary LDAP attribute
     *
     * @return string
     */
    public function binaryToText($bin)
    {
        $hex_guid = bin2hex($bin);

        $hex_guid_to_guid_str = '';

        for ($k = 1; $k <= 4; ++$k) {
            $hex_guid_to_guid_str .= substr($hex_guid, 8 - 2 * $k, 2);
        }

        $hex_guid_to_guid_str .= '-';

        for ($k = 1; $k <= 2; ++$k) {
            $hex_guid_to_guid_str .= substr($hex_guid, 12 - 2 * $k, 2);
        }

        $hex_guid_to_guid_str .= '-';

        for ($k = 1; $k <= 2; ++$k) {
            $hex_guid_to_guid_str .= substr($hex_guid, 16 - 2 * $k, 2);
        }

        $hex_guid_to_guid_str .= '-'.substr($hex_guid, 16, 4);

        $hex_guid_to_guid_str .= '-'.substr($hex_guid, 20);

        return strtoupper($hex_guid_to_guid_str);
    }

    /**
     * Converts a binary GUID to a string GUID.
     *
     * @param string $binaryGuid The binary GUID attribute to convert
     *
     * @return string
     */
    public function decodeGuid($binaryGuid)
    {
        $this->validateNotNull('Binary GUID', $binaryGuid);

        $strGUID = $this->binaryToText($binaryGuid);

        return $strGUID;
    }

    /**
     * Convert a boolean value to a string.
     * You should never need to call this yourself.
     *
     * @param bool $bool Boolean value
     *
     * @return string
     */
    public function boolToStr($bool)
    {
        return ($bool) ? 'TRUE' : 'FALSE';
    }

    /**
     * Convert 8bit characters e.g. accented characters to UTF8 encoded characters.
     *
     * @param $item
     * @param $key
     */
    public function encode8Bit(&$item, $key)
    {
        $encode = false;

        if (is_string($item)) {
            $length = strlen($item);

            for ($i = 0; $i < $length; $i++) {
                if (ord($item[$i]) >> 7) {
                    $encode = true;
                }
            }
        }

        if ($encode === true && $key != 'password') {
            $item = utf8_encode($item);
        }
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
     * Convert DN string to array.
     *
     * @param $dnStr
     * @param bool $excludeBaseDn exclude base DN from results
     *
     * @return array
     */
    public function dnStrToArr($dnStr, $excludeBaseDn = true, $includeAttributes = false)
    {
        if ($excludeBaseDn) {
            return ldap_explode_dn($dnStr, ($includeAttributes ? 0 : 1));
        } else {
            return ldap_explode_dn($this->adldap->getBaseDn().$dnStr, ($includeAttributes ? 0 : 1));
        }
    }

    /**
     * Validates if the function Bcmod exists.
     * Throws an exception otherwise.
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function validateBcmodExists()
    {
        if (!function_exists('bcmod')) {
            $message = 'Missing function support [bcmod] http://php.net/manual/en/function.bcmod.php';

            throw new AdldapException($message);
        }

        return true;
    }

    /**
     * Validates that the current LDAP connection is bound. This
     * will throw an AdldapException otherwise.
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function validateLdapIsBound()
    {
        if ($this->adldap->getLdapBind()) {
            return true;
        }

        $message = 'No LDAP connection is currently bound.';

        throw new AdldapException($message);
    }

    /**
     * Validates that the inserted value is not null or empty. This
     * will throw an AdldapException otherwise.
     *
     * @param string $parameter
     * @param string $value
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function validateNotNullOrEmpty($parameter, $value)
    {
        $this->validateNotNull($parameter, $value);

        $this->validateNotEmpty($parameter, $value);

        return true;
    }

    /**
     * Validates that the inserted value of the specified parameter
     * is not null. This will throw an AdldapException otherwise.
     *
     * @param string $parameter
     * @param mixed  $value
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function validateNotNull($parameter, $value)
    {
        if ($value !== null) {
            return true;
        }

        $message = sprintf('Parameter: %s cannot be null.', $parameter);

        throw new AdldapException($message);
    }

    /**
     * Validates that the inserted value of the specified parameter
     * is not empty. This will throw an AdldapException otherwise.
     *
     * @param string $parameter
     * @param mixed  $value
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function validateNotEmpty($parameter, $value)
    {
        if (!empty($value)) {
            return true;
        }

        $message = sprintf('Parameter: %s cannot be empty.', $parameter);

        throw new AdldapException($message);
    }
}
