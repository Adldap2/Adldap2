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
     * Stores the bool to tell the connection
     * whether or not to use SSL.
     *
     * To use SSL, your server must support LDAP over SSL.
     * http://adldap.sourceforge.net/wiki/doku.php?id=ldap_over_ssl
     *
     * @var bool
     */
    protected $useSSL = false;

    /**
     * Stores the bool to tell the connection
     * whether or not to use TLS.
     *
     * If you wish to use TLS you should ensure that $useSSL is set to false and vice-versa
     *
     * @var bool
     */
    protected $useTLS = false;

    /**
     * Stores the bool to tell the connection
     * whether or not to use SSO.
     *
     * To indicate to adLDAP to reuse password set by the browser through NTLM or Kerberos
     *
     * @var bool
     */
    protected $useSSO = false;

    /**
     * The current LDAP connection.
     *
     * @var resource
     */
    protected $connection;

    /**
     * Stores the bool whether or not
     * the current connection is bound.
     *
     * @var bool
     */
    protected $bound = false;

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
     * Returns true / false if the current
     * PHP install supports an SASL bound
     * LDAP connection.
     *
     * @return bool
     */
    public function isSaslSupported()
    {
        if ( ! function_exists('ldap_sasl_bind')) return false;

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
     * Returns true / false if the
     * current connection instance is
     * bound
     *
     * @return bool
     */
    public function isBound()
    {
        return $this->bound;
    }

    /**
     * Set's the current connection
     * to use SSL.
     *
     * @return $this
     */
    public function useSSL()
    {
        $this->useSSL = true;

        return $this;
    }

    /**
     * Set's the current connection
     * to use TLS.
     *
     * @return $this
     */
    public function useTLS()
    {
        $this->useTLS = true;

        return $this;
    }

    /**
     * Set's the current connection
     * to use SSO.
     *
     * @return $this
     */
    public function useSSO()
    {
        $this->useSSO = true;

        return $this;
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
        return @ldap_get_entries($this->getConnection(), $searchResults);
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
     * Starts the LDAP connection as TLS
     *
     * @return bool
     */
    public function startTLS()
    {
        return ldap_start_tls($this->getConnection());
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

        return $this->connection = ldap_connect($protocol . $hostname, $port);
    }

    /**
     * Performs a search on the current connection
     * with the specified distinguished name, filter
     * and fields.
     *
     * @param string $dn
     * @param string $filter
     * @param array $fields
     * @return resource
     */
    public function search($dn, $filter, array $fields)
    {
        return ldap_search($this->getConnection(), $dn, $filter, $fields);
    }

    /**
     * Reads an entry on the current LDAP connection.
     *
     * @param $dn
     * @param $filter
     * @param array $fields
     * @return resource
     */
    public function read($dn, $filter, array $fields)
    {
        return @ldap_read($this->getConnection(), $dn, $filter, $fields);
    }

    /**
     * Binds to the current LDAP connection. If SASL
     * is true, we'll set up a SASL bind instead.
     *
     * @param string $username
     * @param string $password
     * @param bool $sasl
     * @return bool
     */
    public function bind($username, $password, $sasl = false)
    {
        if($sasl)
        {
            return @ldap_sasl_bind($this->getConnection(), NULL, NULL, "GSSAPI");
        } else
        {
            return @ldap_bind($this->getConnection(), $username, $password);
        }
    }

    /**
     * Adds entries to the current LDAP directory.
     *
     * @param string $dn
     * @param array $entry
     * @return bool
     */
    public function add($dn, array $entry)
    {
        return @ldap_add($this->getConnection(), $dn, $entry);
    }

    /**
     * Modifies the specified LDAP entry
     *
     * @param string $dn
     * @param array $entry
     * @return bool
     */
    public function modify($dn, array $entry)
    {
        return @ldap_modify($this->getConnection(), $dn, $entry);
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