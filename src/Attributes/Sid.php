<?php

namespace Adldap\Attributes;

class Sid
{
    /**
     * The string SID value.
     *
     * @var string
     */
    protected $value;

    /**
     * Determines if the specified SID is valid.
     *
     * @param string $sid
     *
     * @return bool
     */
    public static function isValid($sid)
    {
        return (bool) preg_match(
            '/^S-\d(-\d{1,10}){1,16}$/i',
            $sid
        );
    }

    /**
     * Constructor.
     *
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        if (static::isValid($value)) {
            $this->value = $value;
        } else if ($value = $this->convertFromBinary($value)) {
            $this->value = $value;
        } else {
            throw new \InvalidArgumentException("Invalid Binary / String SID.");
        }
    }

    /**
     * Returns the string value of the SID.
     *
     * @return string
     */
    public function getString()
    {
        return $this->value;
    }

    /**
     * Returns the binary variant of the SID.
     *
     * @return string
     */
    public function getBinary()
    {
        $sid = explode('-', ltrim($this->value, 'S-'));

        $level = (int) array_shift($sid);

        $authority = (int) array_shift($sid);

        $subAuthorities = array_map('intval', $sid);

        $params = array_merge(
            ['C2xxNV*', $level, count($subAuthorities), $authority],
            $subAuthorities
        );

        return call_user_func_array('pack', $params);
    }

    /**
     * Converts a string SID to binary.
     *
     * @param $string
     *
     * @return string
     */
    public function convertFromString($string)
    {
        return pack('C2xxNV*', $string);
    }

    /**
     * Convert a binary SID to a string SID.
     *
     * @author Chad Sikorra
     *
     * @link https://github.com/ChadSikorra
     * @link https://stackoverflow.com/questions/39533560/php-ldap-get-user-sid
     *
     * @param string $binary The Binary SID
     *
     * @return string|null
     */
    protected function convertFromBinary($binary)
    {
        // Revision - 8bit unsigned int (C1)
        // Count - 8bit unsigned int (C1)
        // 2 null bytes
        // ID - 32bit unsigned long, big-endian order
        $sid = @unpack('C1rev/C1count/x2/N1id', $binary);

        $subAuthorities = [];

        if (!isset($sid['id']) || !isset($sid['rev'])) {
            return;
        }

        $revisionLevel = $sid['rev'];

        $identifierAuthority = $sid['id'];

        $subs = isset($sid['count']) ? $sid['count'] : 0;

        // The sub-authorities depend on the count, so only get as
        // many as the count, regardless of data beyond it.
        for ($i = 0; $i < $subs; $i++) {
            // Each sub-auth is a 32bit unsigned long, little-endian order
            $subAuthorities[] = unpack(
                'V1sub',
                hex2bin(substr(bin2hex($binary), 16 + ($i * 8), 8))
            )['sub'];
        }

        // Tack on the 'S-' and glue it all together...
        return 'S-'.$revisionLevel.'-'.$identifierAuthority.implode(
            preg_filter('/^/', '-', $subAuthorities)
        );
    }
}
