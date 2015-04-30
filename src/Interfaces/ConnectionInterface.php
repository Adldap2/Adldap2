<?php

namespace Adldap\Interfaces;

/**
 * The Connection interface used for making
 * connections. Implementing this interface
 * on connection classes helps unit and functional
 * testing classes that require a connection.
 *
 * Interface ConnectionInterface
 */
interface ConnectionInterface
{
    /**
     * The SSL LDAP protocol string.
     *
     * @var string
     */
    const PROTOCOL_SSL = 'ldaps://';

    /**
     * The non-SSL LDAP protocol string.
     *
     * @var string
     */
    const PROTOCOL = 'ldap://';

    /**
     * The LDAP SSL Port number.
     *
     * @var string
     */
    const PORT_SSL = '636';

    /**
     * The non SSL LDAP port number.
     *
     * @var string
     */
    const PORT = '389';

    /**
     * Returns true / false if the
     * current connection is supported
     * on the current PHP install.
     *
     * @return bool
     */
    public function isSupported();

    /**
     * Returns true / false if the
     * current connection supports
     * SASL for single sign on
     * capability.
     *
     * @return bool
     */
    public function isSaslSupported();

    /**
     * Returns true / false if the
     * current connection pagination.
     *
     * @return bool
     */
    public function isPagingSupported();

    /**
     * Returns true / false if the
     * current connection supports batch
     * modification.
     *
     * @return bool
     */
    public function isBatchSupported();

    /**
     * Returns true / false if the
     * current connection instance is using
     * SSL.
     *
     * @return bool
     */
    public function isUsingSSL();

    /**
     * Returns true / false if the
     * current connection instance is using
     * TLS.
     *
     * @return bool
     */
    public function isUsingTLS();

    /**
     * Returns true / false if the current
     * connection instance is using single
     * sign on.
     *
     * @return bool
     */
    public function isUsingSSO();

    /**
     * Returns true / false if the current
     * connection is able to modify passwords.
     *
     * @return bool
     */
    public function canChangePasswords();

    /**
     * Sets the suppressErrors property to true
     * so any recoverable errors thrown will be suppressed.
     *
     * @return $this
     */
    public function suppressErrors();

    /**
     * Sets the suppressErrors property to true
     * so any errors thrown will be shown.
     *
     * @return $this
     */
    public function showErrors();

    /**
     * Returns true / false if the current
     * connection is bound.
     *
     * @return bool
     */
    public function isBound();

    /**
     * Sets the current connection to use TLS.
     *
     * @return $this
     */
    public function useTLS();

    /**
     * Sets the current connection to use SSL.
     *
     * @return $this
     */
    public function useSSL();

    /**
     * Sets the current connection to use SSO.
     *
     * @return $this
     */
    public function useSSO();

    /**
     * Get the current connection.
     *
     * @return mixed
     */
    public function getConnection();

    /**
     * Retrieve the entries from a search result.
     *
     * @param $searchResult
     *
     * @return mixed
     */
    public function getEntries($searchResult);

    /**
     * Returns the number of entries from a search
     * result.
     *
     * @param $searchResult
     *
     * @return int
     */
    public function countEntries($searchResult);

    /**
     * Retrieves the first entry from a search result.
     *
     * @param $searchResult
     *
     * @return mixed
     */
    public function getFirstEntry($searchResult);

    /**
     * Retrieve the last error on the current
     * connection.
     *
     * @return string
     */
    public function getLastError();

    /**
     * Get all binary values from the specified result entry.
     *
     * @param $entry
     * @param $attribute
     *
     * @return array
     */
    public function getValuesLen($entry, $attribute);

    /**
     * Sets an option on the current connection.
     *
     * @param int   $option
     * @param mixed $value
     *
     * @return mixed
     */
    public function setOption($option, $value);

    /**
     * Connects to the specified hostname using the
     * specified port.
     *
     * @param string $hostname
     * @param int    $port
     *
     * @return mixed
     */
    public function connect($hostname, $port = 389);

    /**
     * Starts a connection using TLS.
     *
     * @return mixed
     */
    public function startTLS();

    /**
     * Binds to the current connection using
     * the specified username and password. If sasl
     * is true, the current connection is bound using
     * SASL.
     *
     * @param string $username
     * @param string $password
     * @param bool   $sasl
     *
     * @return mixed
     */
    public function bind($username, $password, $sasl = false);

    /**
     * Closes the current connection.
     *
     * @return mixed
     */
    public function close();

    /**
     * @param string $dn
     * @param string $filter
     * @param array  $fields
     *
     * @return mixed
     */
    public function search($dn, $filter, array $fields);

    /**
     * Reads an entry on the current connection.
     *
     * @param string $dn
     * @param $filter
     * @param array  $fields
     *
     * @return mixed
     */
    public function read($dn, $filter, array $fields);

    /**
     * Performs a single level search on the current connection.
     *
     * @param string $dn
     * @param string $filter
     * @param array  $attributes
     *
     * @return mixed
     */
    public function listing($dn, $filter, array $attributes);

    /**
     * Adds an entry to the current connection.
     *
     * @param string $dn
     * @param array  $entry
     *
     * @return bool
     */
    public function add($dn, array $entry);

    /**
     * Deletes an entry on the current connection.
     *
     * @param string $dn
     *
     * @return bool
     */
    public function delete($dn);

    /**
     * Modify the name of an entry on the current
     * connection.
     *
     * @param string $dn
     * @param string $newRdn
     * @param string $newParent
     * @param bool   $deleteOldRdn
     *
     * @return mixed
     */
    public function rename($dn, $newRdn, $newParent, $deleteOldRdn = false);

    /**
     * Modifies an existing entry on the
     * current connection.
     *
     * @param string $dn
     * @param array  $entry
     *
     * @return mixed
     */
    public function modify($dn, array $entry);

    /**
     * Batch modifies an existing entry on the
     * current connection.
     *
     * @param string $dn
     * @param array  $entry
     *
     * @return mixed
     */
    public function modifyBatch($dn, array $entry);

    /**
     * Add attribute values to current attributes.
     *
     * @param string $dn
     * @param array  $entry
     *
     * @return mixed
     */
    public function modAdd($dn, array $entry);

    /**
     * Replaces attribute values with new ones.
     *
     * @param string $dn
     * @param array  $entry
     *
     * @return mixed
     */
    public function modReplace($dn, array $entry);

    /**
     * Delete attribute values from current attributes.
     *
     * @param string $dn
     * @param array  $entry
     *
     * @return mixed
     */
    public function modDelete($dn, array $entry);

    /**
     * Returns an escaped string for use in an LDAP filter.
     *
     * @param string $value
     * @param string $ignore
     *
     * @return string
     */
    public function escape($value, $ignore = '');

    /**
     * Un-escapes a hexadecimal string into its original
     * string representation.
     *
     * @thanks https://github.com/ldaptools/ldaptools
     *
     * @param string $value
     *
     * @return mixed
     */
    public function unescape($value);

    /**
     * Send LDAP pagination control.
     *
     * @param int    $pageSize
     * @param bool   $isCritical
     * @param string $cookie
     *
     * @return mixed
     */
    public function controlPagedResult($pageSize = 1000, $isCritical = false, $cookie = '');

    /**
     * Retrieve a paginated result response.
     *
     * @param $result
     * @param string $cookie
     *
     * @return mixed
     */
    public function controlPagedResultResponse($result, &$cookie);

    /**
     * Returns the error number of the last command
     * executed on the current connection.
     *
     * @return mixed
     */
    public function errNo();

    /**
     * Returns the extended error string of the last command.
     *
     * @return mixed
     */
    public function getExtendedError();

    /**
     * Returns the extended error code of the last command.
     *
     * @return mixed
     */
    public function getExtendedErrorCode();

    /**
     * Explodes a distinguished name into an array.
     *
     * @param string $dn                      The distinguished name
     * @param bool   $removeAttributePrefixes
     *
     * @return mixed
     */
    public function explodeDn($dn, $removeAttributePrefixes = true);

    /**
     * Returns the error string of the specified
     * error number.
     *
     * @param int $number
     *
     * @return mixed
     */
    public function err2Str($number);
}
