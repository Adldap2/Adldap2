<?php

namespace adLDAP\Connections;

use adLDAP\Interfaces\ConnectionInterface;

/**
 * The LDAP Connection.
 *
 * Class LDAP
 * @package adLDAP\Connections
 */
class LDAP implements ConnectionInterface
{
    /**
     * The SSL LDAP protocol string
     *
     * @var string
     */
    const LDAP_PROTOCOL_SSL = 'ldaps://';

    /**
     * The non-SSL LDAP protocol string
     *
     * @var string
     */
    const LDAP_PROTOCOL = 'ldap://';

    /**
     * @var bool
     */
    public $useSSL = false;

    /**
     * @var bool
     */
    public $useTLS = false;

    /**
     * @var bool
     */
    public $useSSO = false;

    /**
     * The current LDAP connection.
     *
     * @var resource
     */
    protected $connection;

    /**
     * Returns true / false if the current
     * PHP install supports LDAP.
     *
     * @return bool
     */
    public function isSupported()
    {
        if ( ! function_exists('ldap_connect')) return false;

        return true;
    }

    /**
     * Returns true / false if the
     * current connection instance is using
     * SSL.
     *
     * @return bool
     */
    public function isUsingSSL()
    {
        return $this->useSSL;
    }

    /**
     * @return bool
     */
    public function isUsingTLS()
    {
        return $this->useTLS;
    }

    /**
     * @return bool
     */
    public function isUsingSSO()
    {
        return $this->useSSO;
    }

    /**
     * Returns the current LDAP connection.
     *
     * @return resource
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Returns the last error from
     * the current LDAP connection.
     *
     * @return string
     */
    public function getLastError()
    {
        return @ldap_error($this->getConnection());
    }

    /**
     * @param string $hostname
     * @param int $port
     * @return resource
     */
    public function connect($hostname, $port = 389)
    {
        $protocol = $this::LDAP_PROTOCOL;

        if($this->isUsingSSL()) $protocol = $this::LDAP_PROTOCOL_SSL;

        return ldap_connect($protocol . $hostname, $port);
    }

    /**
     * @param string $username
     * @param string $password
     * @param bool $sasl
     * @return bool
     */
    public function bind($username, $password, $sasl = false)
    {
        return ldap_bind($this->getConnection(), $username, $password);
    }

    /**
     * Closes the current LDAP connection if
     * it exists.
     *
     * @return bool
     */
    public function close()
    {
        $connection = $this->getConnection();

        if($connection) @ldap_close($connection);

        return true;
    }
}