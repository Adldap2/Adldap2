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
     * Holds the bool to tell the connection
     * whether or not to use SSL
     *
     * @var bool
     */
    public $useSSL = false;

    /**
     * Holds the bool to tell the connection
     * whether or not to use TLS
     *
     * @var bool
     */
    public $useTLS = false;

    /**
     * Holds the bool to tell the connection
     * whether or not to use SSO
     *
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
     * Returns true / false if the
     * current connection instance is using
     * TLS.
     *
     * @return bool
     */
    public function isUsingTLS()
    {
        return $this->useTLS;
    }

    /**
     * Returns true / false if the
     * current connection instance is using
     * SSO.
     *
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
     * Retrieves and returns the results of an
     * LDAP search into an array format.
     *
     * @param $searchResults
     * @return array
     */
    public function getEntries($searchResults)
    {
        return ldap_get_entries($this->getConnection(), $searchResults);
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
     * Sets an option and value on the current
     * LDAP connection.
     *
     * @param int $option
     * @param mixed $value
     * @return bool
     */
    public function setOption($option, $value)
    {
        return ldap_set_option($this->getConnection(), $option, $value);
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