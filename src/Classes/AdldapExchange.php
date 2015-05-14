<?php

namespace Adldap\Classes;

use Adldap\Exceptions\AdldapException;
use Adldap\Objects\Mailbox;

/**
 * Ldap Microsoft Exchange Management.
 *
 * Class AdldapExchange
 */
class AdldapExchange extends AbstractAdldapQueryable
{
    /**
     * The exchange servers object category.
     *
     * @var string
     */
    public $serverObjectCategory = 'msExchExchangeServer';

    /**
     * The exchange servers storage group object category.
     *
     * @var string
     */
    public $storageGroupObjectCategory = 'msExchStorageGroup';

    /**
     * Returns all exchange servers.
     *
     * @param array  $fields
     * @param bool   $sorted
     * @param string $sortBy
     * @param string $sortByDirection
     *
     * @return array|bool
     */
    public function all($fields = [], $sorted = true, $sortBy = 'cn', $sortByDirection = 'asc')
    {
        $namingContext = $this->getConfigurationNamingContext();

        if ($namingContext) {
            $search = $this->adldap->search()
                ->setDn($namingContext)
                ->select($fields)
                ->where('objectCategory', '=', $this->serverObjectCategory);

            if ($sorted) {
                $search->sortBy($sortBy, $sortByDirection);
            }

            return $search->get();
        }

        return false;
    }

    /**
     * Finds an exchange server.
     *
     * @param string $name
     * @param array  $fields
     *
     * @return array|bool
     */
    public function find($name, $fields = [])
    {
        $namingContext = $this->getConfigurationNamingContext();

        if ($namingContext) {
            return $this->adldap->search()
                ->setDn($namingContext)
                ->select($fields)
                ->where('objectCategory', '=', $this->serverObjectCategory)
                ->where('anr', '=', $name)
                ->first();
        }

        return false;
    }

    /**
     * Create an Exchange account.
     *
     * @param string $username     The username of the user to add the Exchange account to
     * @param array  $storageGroup The mailbox, Exchange Storage Group, for the user account, this must be a full CN
     * @param string $emailAddress The primary email address to add to this user
     * @param null   $mailNickname The mail nick name. If mail nickname is blank, the username will be used
     * @param bool   $useDefaults  Indicates whether the store should use the default quota, rather than the per-mailbox quota.
     * @param null   $baseDn       Specify an alternative base_dn for the Exchange storage group
     * @param bool   $isGUID       Is the username passed a GUID or a samAccountName
     *
     * @return bool|string
     *
     * @throws AdldapException
     */
    public function createMailbox($username, $storageGroup, $emailAddress, $mailNickname = null, $useDefaults = true, $baseDn = null, $isGUID = false)
    {
        $mailbox = new Mailbox([
            'username' => $username,
            'storageGroup' => $storageGroup,
            'emailAddress' => $emailAddress,
            'mailNickname' => $mailNickname,
            'baseDn' => ($baseDn ? $baseDn : $this->adldap->getBaseDn()),
            'mdbUseDefaults' => $this->adldap->utilities()->boolToStr($useDefaults),
        ]);

        // Validate the mailbox fields
        $mailbox->validateRequired();

        // Set the container attribute by imploding the storage group array
        $mailbox->setAttribute('container', 'CN='.implode(',CN=', $storageGroup));

        // Set the mail nickname to the username if it isn't provided
        if ($mailbox->{'mailNickname'} === null) {
            $mailbox->setAttribute('mailNickname', $mailbox->{'username'});
        }

        // Perform the creation and return the result
        return $this->adldap->user()->modify($username, $mailbox->toLdapArray(), $isGUID);
    }

    /**
     * Add an X400 address to Exchange.
     * See http://tools.ietf.org/html/rfc1685 for more information.
     * An X400 Address looks similar to this X400:c=US;a= ;p=Domain;o=Organization;s=Doe;g=John;.
     *
     * @param string $username  The username of the user to add the X400 to to
     * @param string $country   Country
     * @param string $admd      Administration Management Domain
     * @param string $pdmd      Private Management Domain (often your AD domain)
     * @param string $org       Organization
     * @param string $surname   Surname
     * @param string $givenName Given name
     * @param bool   $isGUID    Is the username passed a GUID or a samAccountName
     *
     * @return bool|string
     */
    public function addX400($username, $country, $admd, $pdmd, $org, $surname, $givenName, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);

        $proxyValue = 'X400:';

        // Find the dn of the user
        $user = $this->adldap->user()->info($username, ['cn', 'proxyaddresses'], $isGUID);

        if ($user[0]['dn'] === null) {
            return false;
        }

        $userDn = $user[0]['dn'];

        // We do not have to demote an email address from the default so we can just add the new proxy address
        $attributes['exchange_proxyaddress'] = $proxyValue.'c='.$country.';a='.$admd.';p='.$pdmd.';o='.$org.';s='.$surname.';g='.$givenName.';';

        // Translate the update to the LDAP schema
        $add = $this->adldap->ldapSchema($attributes);

        if (!$add) {
            return false;
        }

        /*
         * Perform the update, take out the '@' to see any errors,
         * usually this error might occur because the address already
         * exists in the list of proxyAddresses
         */
        return $this->connection->add($userDn, $add);
    }

    /**
     * @param string $username     The username of the user to add the Exchange account to
     * @param string $emailAddress The email address to add to this user
     * @param bool   $default      Make this email address the default address, this is a bit more intensive as we have to demote any existing default addresses
     * @param bool   $isGUID       Is the username passed a GUID or a samAccountName
     *
     * @return bool|string
     */
    public function addAddress($username, $emailAddress, $default = false, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);
        $this->adldap->utilities()->validateNotNull('Email Address', $emailAddress);

        $proxyValue = 'smtp:';

        if ($default === true) {
            $proxyValue = 'SMTP:';
        }

        // Find the dn of the user
        $user = $this->adldap->user()->info($username, ['cn', 'proxyaddresses'], $isGUID);

        if ($user[0]['dn'] === null) {
            return false;
        }

        $userDn = $user[0]['dn'];

        // We need to scan existing proxy addresses and demote the default one
        if (is_array($user[0]['proxyaddresses']) && $default === true) {
            $modAddresses = [];

            for ($i = 0; $i < $user[0]['proxyaddresses']['count']; $i++) {
                if (strpos($user[0]['proxyaddresses'][$i], 'SMTP:') !== false) {
                    $user[0]['proxyaddresses'][$i] = str_replace('SMTP:', 'smtp:', $user[0]['proxyaddresses'][$i]);
                }

                if ($user[0]['proxyaddresses'][$i] != '') {
                    $modAddresses['proxyAddresses'][$i] = $user[0]['proxyaddresses'][$i];
                }
            }

            $modAddresses['proxyAddresses'][($user[0]['proxyaddresses']['count'] - 1)] = 'SMTP:'.$emailAddress;

            $result = $this->connection->modReplace($userDn, $modAddresses);
        } else {
            // We do not have to demote an email address from the default so we can just add the new proxy address
            $attributes['exchange_proxyaddress'] = $proxyValue.$emailAddress;

            // Translate the update to the LDAP schema
            $add = $this->adldap->ldapSchema($attributes);

            if (!$add) {
                return false;
            }

            /*
             * Perform the update, take out the '@' to see any errors,
             * usually this error might occur because the address already
             * exists in the list of proxyAddresses
             */
            $result = $this->connection->modAdd($userDn, $add);
        }

        return $result;
    }

    /**
     * Remove an address to Exchange.
     * If you remove a default address the account will no longer have a default,
     * we recommend changing the default address first.
     *
     * @param string $username     The username of the user to add the Exchange account to
     * @param string $emailAddress The email address to add to this user
     * @param bool   $isGUID       Is the username passed a GUID or a samAccountName
     *
     * @return bool|string
     */
    public function deleteAddress($username, $emailAddress, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);
        $this->adldap->utilities()->validateNotNull('Email Address', $emailAddress);

        // Find the dn of the user
        $user = $this->adldap->user()->info($username, ['cn', 'proxyaddresses'], $isGUID);

        if ($user[0]['dn'] === null) {
            return false;
        }

        $userDn = $user[0]['dn'];

        if (is_array($user[0]['proxyaddresses'])) {
            $mod = [];

            for ($i = 0; $i < $user[0]['proxyaddresses']['count']; $i++) {
                if (strpos($user[0]['proxyaddresses'][$i], 'SMTP:') !== false && $user[0]['proxyaddresses'][$i] == 'SMTP:'.$emailAddress) {
                    $mod['proxyAddresses'][0] = 'SMTP:'.$emailAddress;
                } elseif (strpos($user[0]['proxyaddresses'][$i], 'smtp:') !== false && $user[0]['proxyaddresses'][$i] == 'smtp:'.$emailAddress) {
                    $mod['proxyAddresses'][0] = 'smtp:'.$emailAddress;
                }
            }

            return $this->connection->modDelete($userDn, $mod);
        }

        return false;
    }

    /**
     * Change the default address.
     *
     * @param string $username     The username of the user to add the Exchange account to
     * @param string $emailAddress The email address to make default
     * @param bool   $isGUID       Is the username passed a GUID or a samAccountName
     *
     * @return bool|string
     */
    public function primaryAddress($username, $emailAddress, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);
        $this->adldap->utilities()->validateNotNull('Email Address', $emailAddress);

        // Find the dn of the user
        $user = $this->adldap->user()->info($username, ['cn', 'proxyaddresses'], $isGUID);

        if ($user[0]['dn'] === null) {
            return false;
        }

        $userDn = $user[0]['dn'];

        if (is_array($user[0]['proxyaddresses'])) {
            $modAddresses = [];

            for ($i = 0; $i < $user[0]['proxyaddresses']['count']; $i++) {
                if (strpos($user[0]['proxyaddresses'][$i], 'SMTP:') !== false) {
                    $user[0]['proxyaddresses'][$i] = str_replace('SMTP:', 'smtp:', $user[0]['proxyaddresses'][$i]);
                }

                if ($user[0]['proxyaddresses'][$i] == 'smtp:'.$emailAddress) {
                    $user[0]['proxyaddresses'][$i] = str_replace('smtp:', 'SMTP:', $user[0]['proxyaddresses'][$i]);
                }

                if ($user[0]['proxyaddresses'][$i] != '') {
                    $modAddresses['proxyAddresses'][$i] = $user[0]['proxyaddresses'][$i];
                }
            }

            return $this->connection->modReplace($userDn, $modAddresses);
        }

        return false;
    }

    /**
     * Mail enable a contact
     * Allows email to be sent to them through Exchange.
     *
     * @param string $distinguishedName The contact to mail enable
     * @param string $emailAddress      The email address to allow emails to be sent through
     * @param string $mailNickname      The mailnickname for the contact in Exchange.  If NULL this will be set to the display name
     *
     * @return bool
     */
    public function contactMailEnable($distinguishedName, $emailAddress, $mailNickname = null)
    {
        $this->adldap->utilities()->validateNotNull('Distinguished Name [dn]', $distinguishedName);
        $this->adldap->utilities()->validateNotNull('Email Address', $emailAddress);

        if ($mailNickname !== null) {
            // Find the dn of the user
            $user = $this->adldap->contact()->info($distinguishedName, ['cn', 'displayname']);

            if ($user[0]['displayname'] === null) {
                return false;
            }

            $mailNickname = $user[0]['displayname'][0];
        }

        $attributes = ['email' => $emailAddress,'contact_email' => 'SMTP:'.$emailAddress,'exchange_proxyaddress' => 'SMTP:'.$emailAddress,'exchange_mailnickname' => $mailNickname];

        // Translate the update to the LDAP schema
        $mod = $this->adldap->ldapSchema($attributes);

        // Check to see if this is an enabled status update
        if (!$mod) {
            return false;
        }

        // Do the update
        return $this->connection->modify($distinguishedName, $mod);
    }

    /**
     * Returns a list of Exchange Servers in the ConfigurationNamingContext of the domain.
     *
     * @param array $fields
     *
     * @return array|bool
     */
    public function servers($fields = [])
    {
        return $this->all($fields);
    }

    /**
     * Returns a list of Storage Groups in Exchange for a given mail server.
     *
     * @param string $exchangeServer The full DN of an Exchange server.  You can use exchange_servers() to find the DN for your server
     * @param array  $attributes     An array of the AD attributes you wish to return
     * @param null   $recursive      If enabled this will automatically query the databases within a storage group
     *
     * @return bool|array
     */
    public function storageGroups($exchangeServer, $attributes = ['cn', 'distinguishedname'], $recursive = null)
    {
        $this->adldap->utilities()->validateNotNull('Exchange Server', $exchangeServer);

        $this->adldap->utilities()->validateLdapIsBound();

        if ($recursive === null) {
            $recursive = $this->adldap->getRecursiveGroups();
        }

        $filter = "(&(objectCategory=$this->storageGroupObjectCategory))";

        $results = $this->connection->search($exchangeServer, $filter, $attributes);

        if ($results) {
            $entries = $this->connection->getEntries($results);

            if ($recursive === true) {
                for ($i = 0; $i < $entries['count']; $i++) {
                    $entries[$i]['msexchprivatemdb'] = $this->storageDatabases($entries[$i]['distinguishedname'][0]);
                }
            }

            return $entries;
        }

        return false;
    }

    /**
     * Returns a list of Databases within any given storage group in Exchange for a given mail server.
     *
     * @param string $storageGroup The full DN of an Storage Group.  You can use exchange_storage_groups() to find the DN
     * @param array  $attributes   An array of the AD attributes you wish to return
     *
     * @return array|bool|string
     */
    public function storageDatabases($storageGroup, $attributes = ['cn', 'distinguishedname', 'displayname'])
    {
        $this->adldap->utilities()->validateNotNull('Storage Group', $storageGroup);

        $this->adldap->utilities()->validateLdapIsBound();

        $filter = '(&(objectCategory=msExchPrivateMDB))';

        $results = $this->connection->search($storageGroup, $filter, $attributes);

        $entries = $this->connection->getEntries($results);

        return $entries;
    }

    /**
     * Returns the current configuration naming context
     * of the current domain.
     *
     * @return string|bool
     */
    private function getConfigurationNamingContext()
    {
        $result = $this->adldap->getRootDse(['configurationnamingcontext']);

        if (is_array($result) && array_key_exists('configurationnamingcontext', $result)) {
            return $result['configurationnamingcontext'];
        }

        return false;
    }
}
