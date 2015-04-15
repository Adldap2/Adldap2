<?php

namespace Adldap\Connections;

use Exception;
use Adldap\Interfaces\ConnectionInterface;

/**
 * The LDAP Connection.
 *
 * Class LDAP
 * @package Adldap\Connections
 */
class Ldap implements ConnectionInterface
{
    /**
     * The SSL LDAP protocol string
     *
     * @var string
     */
    const PROTOCOL_SSL = 'ldaps://';

    /**
     * The non-SSL LDAP protocol string
     *
     * @var string
     */
    const PROTOCOL = 'ldap://';

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
     * To indicate to Adldap to reuse password set by the browser through NTLM or Kerberos
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
     * Stores the bool whether or not
     * to suppress errors when calling
     * LDAP methods.
     *
     * @var bool
     */
    protected $suppressErrors = true;

    /**
     * Magic method to suppress LDAP errors if the
     * suppressErrors attribute is true.
     *
     * @param string $method
     * @param array $parameters
     * @return bool|mixed
     */
    public function __call($method, $parameters)
    {
        if($this->suppressErrors)
        {
            try
            {
                // Try to call the specified method
                return call_user_func_array(array($this, $method), $parameters);
            } catch(Exception $e)
            {
                // Always return false on failure
                return false;
            }
        } else
        {
            // Call the method without covering it by a try / catch
            return call_user_func_array(array($this, $method), $parameters);
        }
    }

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
     * bound.
     *
     * @return bool
     */
    public function isBound()
    {
        return $this->bound;
    }

    /**
     * Returns true / false if the current
     * LDAP connection has the ability to
     * change passwords.
     *
     * @return bool
     */
    public function canChangePasswords()
    {
        if ( ! $this->isUsingSSL() && ! $this->isUsingTLS()) return false;

        return true;
    }

    /**
     * Sets the suppressErrors property to true
     * so any recoverable errors thrown will be suppressed.
     *
     * @return void
     */
    public function suppressErrors()
    {
        $this->suppressErrors = true;
    }

    /**
     * Sets the suppressErrors property to true
     * so any errors thrown will be shown.
     *
     * @return void
     */
    public function showErrors()
    {
        $this->suppressErrors = false;
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
        return ldap_get_entries($this->getConnection(), $searchResults);
    }

    /**
     * Returns the first entry from the specified
     * search results.
     *
     * @param $searchResults
     * @return resource
     */
    public function getFirstEntry($searchResults)
    {
        return ldap_first_entry($this->getConnection(), $searchResults);
    }

    /**
     * Returns the count of the returned entries
     * from the specified search results.
     *
     * @param $searchResults
     * @return int
     */
    public function countEntries($searchResults)
    {
        return ldap_count_entries($this->getConnection(), $searchResults);
    }

    /**
     * Returns the last error from
     * the current LDAP connection.
     *
     * @return string
     */
    public function getLastError()
    {
        return ldap_error($this->getConnection());
    }

    /**
     * Get all binary values from the specified result entry
     *
     * @param $entry
     * @param $attribute
     * @return array
     */
    public function getValuesLen($entry, $attribute)
    {
        return ldap_get_values_len($this->getConnection(), $entry, $attribute);
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
     * Connects to the specified hostname
     * using the PHP ldap protocol.
     *
     * @param string $hostname
     * @param string $port
     * @return resource
     */
    public function connect($hostname, $port = '389')
    {
        $protocol = $this::PROTOCOL;

        if($this->isUsingSSL()) $protocol = $this::PROTOCOL_SSL;

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
     * Performs a single level search on the current connection.
     *
     * @param string $dn
     * @param string $filter
     * @param array $attributes
     * @return mixed
     */
    public function listing($dn, $filter, array $attributes)
    {
        return ldap_list($this->getConnection(), $dn, $filter, $attributes);
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
        return ldap_read($this->getConnection(), $dn, $filter, $fields);
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
            return $this->bound = ldap_sasl_bind($this->getConnection(), NULL, NULL, "GSSAPI");
        } else
        {
            return $this->bound = ldap_bind($this->getConnection(), $username, $password);
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
        return ldap_add($this->getConnection(), $dn, $entry);
    }

    /**
     * Deletes an entry on the current LDAP directory.
     *
     * @param string $dn
     * @return bool
     */
    public function delete($dn)
    {
        return ldap_delete($this->getConnection(), $dn);
    }

    /**
     * Modify the name of an LDAP entry.
     *
     * @param string $dn
     * @param string $newRdn
     * @param string $newParent
     * @param bool $deleteOldRdn
     * @return bool
     */
    public function rename($dn, $newRdn, $newParent, $deleteOldRdn = false)
    {
        return ldap_rename($this->getConnection(), $dn, $newRdn, $newParent, $deleteOldRdn);
    }

    /**
     * Modifies the specified LDAP entry.
     *
     * @param string $dn
     * @param array $entry
     * @return bool
     */
    public function modify($dn, array $entry)
    {
        return ldap_modify($this->getConnection(), $dn, $entry);
    }

    /**
     * Add attribute values to current attributes.
     *
     * @param string $dn
     * @param array $entry
     * @return bool
     */
    public function modAdd($dn, array $entry)
    {
        return ldap_mod_add($this->getConnection(), $dn, $entry);
    }

    /**
     * Replaces attribute values with new ones.
     *
     * @param $dn
     * @param array $entry
     * @return bool
     */
    public function modReplace($dn, array $entry)
    {
        return ldap_mod_replace($this->getConnection(), $dn, $entry);
    }

    /**
     * Delete attribute values from current attributes.
     *
     * @param string $dn
     * @param array $entry
     * @return bool
     */
    public function modDelete($dn, array $entry)
    {
        return ldap_mod_del($this->getConnection(), $dn, $entry);
    }

    /**
     * Return the LDAP error number of the last LDAP command
     *
     * @return int
     */
    public function errNo()
    {
        return ldap_errno($this->getConnection());
    }

    /**
     * Convert LDAP error number into string error message
     *
     * @param int $number
     * @return string
     */
    public function err2Str($number)
    {
        return ldap_err2str($number);
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

        if($connection) ldap_close($connection);

        return true;
    }
}
