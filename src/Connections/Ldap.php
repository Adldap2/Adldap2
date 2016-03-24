<?php

namespace Adldap\Connections;

use Adldap\Exceptions\AdldapException;

class Ldap implements ConnectionInterface
{
    use LdapFunctionSupportTrait;

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
        if (!$this->isUsingSSL() && !$this->isUsingTLS()) {
            return false;
        }

        return true;
    }

    /**
     * Sets the suppressErrors property to true
     * so any recoverable errors thrown will be suppressed.
     *
     * @return Ldap
     */
    public function suppressErrors()
    {
        $this->suppressErrors = true;

        return $this;
    }

    /**
     * Sets the suppressErrors property to true
     * so any errors thrown will be shown.
     *
     * @return Ldap
     */
    public function showErrors()
    {
        $this->suppressErrors = false;

        return $this;
    }

    /**
     * Set's the current connection
     * to use SSL.
     *
     * @return Ldap
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
     * @return Ldap
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
     * @return Ldap
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
     *
     * @return array
     */
    public function getEntries($searchResults)
    {
        if ($this->suppressErrors) {
            return @ldap_get_entries($this->getConnection(), $searchResults);
        } else {
            return ldap_get_entries($this->getConnection(), $searchResults);
        }
    }

    /**
     * Returns the first entry from the specified
     * search results.
     *
     * @param $searchResults
     *
     * @return resource
     */
    public function getFirstEntry($searchResults)
    {
        if ($this->suppressErrors) {
            return @ldap_first_entry($this->getConnection(), $searchResults);
        }

        return ldap_first_entry($this->getConnection(), $searchResults);
    }

    /**
     * Returns the next entry from the
     * current connection.
     *
     * @param $entry
     *
     * @return resource
     */
    public function getNextEntry($entry)
    {
        if ($this->suppressErrors) {
            return @ldap_next_entry($this->getConnection(), $entry);
        }

        return ldap_next_entry($this->getConnection(), $entry);
    }

    /**
     * Retrieves the attributes from the specified ldap entry.
     *
     * @param $entry
     *
     * @return array
     */
    public function getAttributes($entry)
    {
        if ($this->suppressErrors) {
            return @ldap_get_attributes($this->getConnection(), $entry);
        }

        return ldap_get_attributes($this->getConnection(), $entry);
    }

    /**
     * Returns the count of the returned entries
     * from the specified search results.
     *
     * @param $searchResults
     *
     * @return int
     */
    public function countEntries($searchResults)
    {
        if ($this->suppressErrors) {
            return @ldap_count_entries($this->getConnection(), $searchResults);
        }

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
        if ($this->suppressErrors) {
            return @ldap_error($this->getConnection());
        }

        return ldap_error($this->getConnection());
    }

    /**
     * Get all binary values from the specified result entry.
     *
     * @param $entry
     * @param $attribute
     *
     * @return array
     */
    public function getValuesLen($entry, $attribute)
    {
        if ($this->suppressErrors) {
            return @ldap_get_values_len($this->getConnection(), $entry, $attribute);
        }

        return ldap_get_values_len($this->getConnection(), $entry, $attribute);
    }

    /**
     * Sets an option and value on the current
     * LDAP connection.
     *
     * @param int   $option
     * @param mixed $value
     *
     * @return bool
     */
    public function setOption($option, $value)
    {
        if ($this->suppressErrors) {
            return @ldap_set_option($this->getConnection(), $option, $value);
        }

        return ldap_set_option($this->getConnection(), $option, $value);
    }

    /**
     * Set a callback function to do re-binds on referral chasing.
     *
     * @param callable $callback
     *
     * @return bool
     */
    public function setRebindCallback(callable $callback)
    {
        if ($this->suppressErrors) {
            return @ldap_set_rebind_proc($this->getConnection(), $callback);
        }

        return ldap_set_rebind_proc($this->getConnection(), $callback);
    }

    /**
     * Starts the LDAP connection as TLS.
     *
     * @return bool
     */
    public function startTLS()
    {
        if ($this->suppressErrors) {
            return @ldap_start_tls($this->getConnection());
        }

        return ldap_start_tls($this->getConnection());
    }

    /**
     * Connects to the specified hostname
     * using the PHP ldap protocol.
     *
     * @param string $hostname
     * @param string $port
     *
     * @return resource
     */
    public function connect($hostname, $port = '389')
    {
        $protocol = $this::PROTOCOL;

        if ($this->isUsingSSL()) {
            $protocol = $this::PROTOCOL_SSL;
        }

        return $this->connection = ldap_connect($protocol.$hostname.':'.$port);
    }

    /**
     * Closes the current LDAP connection if it exists.
     *
     * @return bool
     */
    public function close()
    {
        $connection = $this->getConnection();

        if (is_resource($connection)) {
            ldap_close($connection);
        }

        return true;
    }

    /**
     * Performs a search on the current connection
     * with the specified distinguished name, filter
     * and fields.
     *
     * @param string $dn
     * @param string $filter
     * @param array  $fields
     *
     * @return resource
     */
    public function search($dn, $filter, array $fields)
    {
        if ($this->suppressErrors) {
            return @ldap_search($this->getConnection(), $dn, $filter, $fields);
        }

        return ldap_search($this->getConnection(), $dn, $filter, $fields);
    }

    /**
     * Performs a single level search on the current connection.
     *
     * @param string $dn
     * @param string $filter
     * @param array  $attributes
     *
     * @return mixed
     */
    public function listing($dn, $filter, array $attributes)
    {
        if ($this->suppressErrors) {
            return @ldap_list($this->getConnection(), $dn, $filter, $attributes);
        }

        return ldap_list($this->getConnection(), $dn, $filter, $attributes);
    }

    /**
     * Reads an entry on the current LDAP connection.
     *
     * @param $dn
     * @param $filter
     * @param array $fields
     *
     * @return resource
     */
    public function read($dn, $filter, array $fields)
    {
        if ($this->suppressErrors) {
            return @ldap_read($this->getConnection(), $dn, $filter, $fields);
        }

        return ldap_read($this->getConnection(), $dn, $filter, $fields);
    }

    /**
     * Sorts an AD search result by the specified attribute.
     *
     * @param resource $result
     * @param string   $attribute
     *
     * @return bool
     */
    public function sort($result, $attribute)
    {
        if ($this->suppressErrors) {
            return @ldap_sort($this->getConnection(), $result, $attribute);
        }

        return ldap_sort($this->getConnection(), $result, $attribute);
    }

    /**
     * Binds to the current LDAP connection. If SASL
     * is true, we'll set up a SASL bind instead.
     *
     * @param string $username
     * @param string $password
     * @param bool   $sasl
     *
     * @return bool
     */
    public function bind($username, $password, $sasl = false)
    {
        if ($this->isUsingTLS()) {
            $this->startTLS();
        }

        if ($sasl) {
            if ($this->suppressErrors) {
                return $this->bound = @ldap_sasl_bind($this->getConnection(), null, null, 'GSSAPI');
            }

            return $this->bound = ldap_sasl_bind($this->getConnection(), null, null, 'GSSAPI');
        } else {
            if ($this->suppressErrors) {
                return $this->bound = @ldap_bind($this->getConnection(), $username, $password);
            }

            return $this->bound = ldap_bind($this->getConnection(), $username, $password);
        }
    }

    /**
     * Adds entries to the current LDAP directory.
     *
     * @param string $dn
     * @param array  $entry
     *
     * @return bool
     */
    public function add($dn, array $entry)
    {
        if ($this->suppressErrors) {
            return @ldap_add($this->getConnection(), $dn, $entry);
        }

        return ldap_add($this->getConnection(), $dn, $entry);
    }

    /**
     * Deletes an entry on the current LDAP directory.
     *
     * @param string $dn
     *
     * @return bool
     */
    public function delete($dn)
    {
        if ($this->suppressErrors) {
            return @ldap_delete($this->getConnection(), $dn);
        }

        return ldap_delete($this->getConnection(), $dn);
    }

    /**
     * Modify the name of an LDAP entry.
     *
     * @param string $dn
     * @param string $newRdn
     * @param string $newParent
     * @param bool   $deleteOldRdn
     *
     * @return bool
     */
    public function rename($dn, $newRdn, $newParent, $deleteOldRdn = false)
    {
        if ($this->suppressErrors) {
            return @ldap_rename($this->getConnection(), $dn, $newRdn, $newParent, $deleteOldRdn);
        }

        return ldap_rename($this->getConnection(), $dn, $newRdn, $newParent, $deleteOldRdn);
    }

    /**
     * Modifies the specified LDAP entry.
     *
     * @param string $dn
     * @param array  $entry
     *
     * @return bool
     */
    public function modify($dn, array $entry)
    {
        if ($this->suppressErrors) {
            return @ldap_modify($this->getConnection(), $dn, $entry);
        }

        return ldap_modify($this->getConnection(), $dn, $entry);
    }

    /**
     * Batch modifies the specified LDAP entry.
     *
     * @param string $dn
     * @param array  $values
     *
     * @return bool
     */
    public function modifyBatch($dn, array $values)
    {
        if ($this->suppressErrors) {
            return @ldap_modify_batch($this->getConnection(), $dn, $values);
        }

        return ldap_modify_batch($this->getConnection(), $dn, $values);
    }

    /**
     * Add attribute values to current attributes.
     *
     * @param string $dn
     * @param array  $entry
     *
     * @return bool
     */
    public function modAdd($dn, array $entry)
    {
        if ($this->suppressErrors) {
            return @ldap_mod_add($this->getConnection(), $dn, $entry);
        }

        return ldap_mod_add($this->getConnection(), $dn, $entry);
    }

    /**
     * Replaces attribute values with new ones.
     *
     * @param $dn
     * @param array $entry
     *
     * @return bool
     */
    public function modReplace($dn, array $entry)
    {
        if ($this->suppressErrors) {
            return @ldap_mod_replace($this->getConnection(), $dn, $entry);
        }

        return ldap_mod_replace($this->getConnection(), $dn, $entry);
    }

    /**
     * Delete attribute values from current attributes.
     *
     * @param string $dn
     * @param array  $entry
     *
     * @return bool
     */
    public function modDelete($dn, array $entry)
    {
        if ($this->suppressErrors) {
            return @ldap_mod_del($this->getConnection(), $dn, $entry);
        }

        return ldap_mod_del($this->getConnection(), $dn, $entry);
    }

    /**
     * Send LDAP pagination control.
     *
     * @param int    $pageSize
     * @param bool   $isCritical
     * @param string $cookie
     *
     * @throws AdldapException
     *
     * @return bool
     */
    public function controlPagedResult($pageSize = 1000, $isCritical = false, $cookie = '')
    {
        if ($this->isPagingSupported()) {
            if ($this->suppressErrors) {
                return @ldap_control_paged_result($this->getConnection(), $pageSize, $isCritical, $cookie);
            }

            return ldap_control_paged_result($this->getConnection(), $pageSize, $isCritical, $cookie);
        }

        $message = 'LDAP Pagination is not supported on your current PHP installation.';

        throw new AdldapException($message);
    }

    /**
     * Retrieve a paginated result response.
     *
     * @param resource $result
     * @param string   $cookie
     *
     * @throws AdldapException
     *
     * @return bool
     */
    public function controlPagedResultResponse($result, &$cookie)
    {
        if ($this->isPagingSupported()) {
            if ($this->suppressErrors) {
                return @ldap_control_paged_result_response($this->getConnection(), $result, $cookie);
            }

            return ldap_control_paged_result_response($this->getConnection(), $result, $cookie);
        }

        $message = 'LDAP Pagination is not supported on your current PHP installation.';

        throw new AdldapException($message);
    }

    /**
     * Return the LDAP error number of the last LDAP command.
     *
     * @return int
     */
    public function errNo()
    {
        return ldap_errno($this->getConnection());
    }

    /**
     * Return the extended LDAP error code of the last LDAP command.
     *
     * @return int
     */
    public function getExtendedError()
    {
        return $this->getDiagnosticMessage();
    }

    /**
     * Return the extended LDAP error code of the last LDAP command.
     *
     * @return int
     */
    public function getExtendedErrorCode()
    {
        return $this->extractDiagnosticCode($this->getExtendedError());
    }

    /**
     * Convert LDAP error number into string error message.
     *
     * @param int $number
     *
     * @return string
     */
    public function err2Str($number)
    {
        return ldap_err2str($number);
    }

    /**
     * Return the diagnostic Message.
     *
     * @return string $diagnosticMessage
     */
    public function getDiagnosticMessage()
    {
        ldap_get_option($this->getConnection(), LDAP_OPT_ERROR_STRING, $diagnosticMessage);

        return $diagnosticMessage;
    }

    /**
     * Extract the diagnostic code from the message.
     *
     * @param string $message
     *
     * @return string $diagnosticCode
     */
    public function extractDiagnosticCode($message)
    {
        preg_match('/^([\da-fA-F]+):/', $message, $matches);

        if (!isset($matches[1])) {
            return false;
        }

        return $matches[1];
    }
}
