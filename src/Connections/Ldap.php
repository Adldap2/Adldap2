<?php

namespace Adldap\Connections;

use Adldap\Contracts\Connections\ConnectionInterface;
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
     * {@inheritdoc}
     */
    public function isUsingSSL()
    {
        return $this->useSSL;
    }

    /**
     * {@inheritdoc}
     */
    public function isUsingTLS()
    {
        return $this->useTLS;
    }

    /**
     * {@inheritdoc}
     */
    public function isUsingSSO()
    {
        return $this->useSSO;
    }

    /**
     * {@inheritdoc}
     */
    public function isBound()
    {
        return $this->bound;
    }

    /**
     * {@inheritdoc}
     */
    public function canChangePasswords()
    {
        if (!$this->isUsingSSL() && !$this->isUsingTLS()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function suppressErrors()
    {
        $this->suppressErrors = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function showErrors()
    {
        $this->suppressErrors = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function useSSL()
    {
        $this->useSSL = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function useTLS()
    {
        $this->useTLS = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function useSSO()
    {
        $this->useSSO = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getFirstEntry($searchResults)
    {
        if ($this->suppressErrors) {
            return @ldap_first_entry($this->getConnection(), $searchResults);
        }

        return ldap_first_entry($this->getConnection(), $searchResults);
    }

    /**
     * {@inheritdoc}
     */
    public function getNextEntry($entry)
    {
        if ($this->suppressErrors) {
            return @ldap_next_entry($this->getConnection(), $entry);
        }

        return ldap_next_entry($this->getConnection(), $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($entry)
    {
        if ($this->suppressErrors) {
            return @ldap_get_attributes($this->getConnection(), $entry);
        }

        return ldap_get_attributes($this->getConnection(), $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function countEntries($searchResults)
    {
        if ($this->suppressErrors) {
            return @ldap_count_entries($this->getConnection(), $searchResults);
        }

        return ldap_count_entries($this->getConnection(), $searchResults);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastError()
    {
        if ($this->suppressErrors) {
            return @ldap_error($this->getConnection());
        }

        return ldap_error($this->getConnection());
    }

    /**
     * {@inheritdoc}
     */
    public function getValuesLen($entry, $attribute)
    {
        if ($this->suppressErrors) {
            return @ldap_get_values_len($this->getConnection(), $entry, $attribute);
        }

        return ldap_get_values_len($this->getConnection(), $entry, $attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($option, $value)
    {
        if ($this->suppressErrors) {
            return @ldap_set_option($this->getConnection(), $option, $value);
        }

        return ldap_set_option($this->getConnection(), $option, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setRebindCallback(callable $callback)
    {
        if ($this->suppressErrors) {
            return @ldap_set_rebind_proc($this->getConnection(), $callback);
        }

        return ldap_set_rebind_proc($this->getConnection(), $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function startTLS()
    {
        if ($this->suppressErrors) {
            return @ldap_start_tls($this->getConnection());
        }

        return ldap_start_tls($this->getConnection());
    }

    /**
     * {@inheritdoc}
     */
    public function connect($hostname = [], $port = '389')
    {
        $protocol = $this::PROTOCOL;

        if ($this->isUsingSSL()) {
            $protocol = $this::PROTOCOL_SSL;
        }

        if (is_array($hostname)) {
            $hostname = $protocol.implode(' '.$protocol, $hostname);
        }

        $this->connection = ldap_connect($hostname, $port);

        // If the connection was successful, we'll set bound to true.
        if ($this->connection) {
            $this->bound = true;
        }

        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $connection = $this->getConnection();

        if ($connection) {
            ldap_close($connection);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function search($dn, $filter, array $fields)
    {
        if ($this->suppressErrors) {
            return @ldap_search($this->getConnection(), $dn, $filter, $fields);
        }

        return ldap_search($this->getConnection(), $dn, $filter, $fields);
    }

    /**
     * {@inheritdoc}
     */
    public function listing($dn, $filter, array $attributes)
    {
        if ($this->suppressErrors) {
            return @ldap_list($this->getConnection(), $dn, $filter, $attributes);
        }

        return ldap_list($this->getConnection(), $dn, $filter, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function read($dn, $filter, array $fields)
    {
        if ($this->suppressErrors) {
            return @ldap_read($this->getConnection(), $dn, $filter, $fields);
        }

        return ldap_read($this->getConnection(), $dn, $filter, $fields);
    }

    /**
     * {@inheritdoc}
     */
    public function sort($result, $attribute)
    {
        if ($this->suppressErrors) {
            return @ldap_sort($this->getConnection(), $result, $attribute);
        }

        return ldap_sort($this->getConnection(), $result, $attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function bind($username, $password, $sasl = false)
    {
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
     * {@inheritdoc}
     */
    public function add($dn, array $entry)
    {
        if ($this->suppressErrors) {
            return @ldap_add($this->getConnection(), $dn, $entry);
        }

        return ldap_add($this->getConnection(), $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($dn)
    {
        if ($this->suppressErrors) {
            return @ldap_delete($this->getConnection(), $dn);
        }

        return ldap_delete($this->getConnection(), $dn);
    }

    /**
     * {@inheritdoc}
     */
    public function rename($dn, $newRdn, $newParent, $deleteOldRdn = false)
    {
        if ($this->suppressErrors) {
            return @ldap_rename($this->getConnection(), $dn, $newRdn, $newParent, $deleteOldRdn);
        }

        return ldap_rename($this->getConnection(), $dn, $newRdn, $newParent, $deleteOldRdn);
    }

    /**
     * {@inheritdoc}
     */
    public function modify($dn, array $entry)
    {
        if ($this->suppressErrors) {
            return @ldap_modify($this->getConnection(), $dn, $entry);
        }

        return ldap_modify($this->getConnection(), $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyBatch($dn, array $values)
    {
        if ($this->suppressErrors) {
            return @ldap_modify_batch($this->getConnection(), $dn, $values);
        }

        return ldap_modify_batch($this->getConnection(), $dn, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function modAdd($dn, array $entry)
    {
        if ($this->suppressErrors) {
            return @ldap_mod_add($this->getConnection(), $dn, $entry);
        }

        return ldap_mod_add($this->getConnection(), $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function modReplace($dn, array $entry)
    {
        if ($this->suppressErrors) {
            return @ldap_mod_replace($this->getConnection(), $dn, $entry);
        }

        return ldap_mod_replace($this->getConnection(), $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function modDelete($dn, array $entry)
    {
        if ($this->suppressErrors) {
            return @ldap_mod_del($this->getConnection(), $dn, $entry);
        }

        return ldap_mod_del($this->getConnection(), $dn, $entry);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function errNo()
    {
        return ldap_errno($this->getConnection());
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedError()
    {
        return $this->getDiagnosticMessage();
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedErrorCode()
    {
        return $this->extractDiagnosticCode($this->getExtendedError());
    }

    /**
     * {@inheritdoc}
     */
    public function err2Str($number)
    {
        return ldap_err2str($number);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiagnosticMessage()
    {
        ldap_get_option($this->getConnection(), LDAP_OPT_ERROR_STRING, $diagnosticMessage);

        return $diagnosticMessage;
    }

    /**
     * {@inheritdoc}
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
