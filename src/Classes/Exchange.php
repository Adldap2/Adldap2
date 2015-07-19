<?php

namespace Adldap\Classes;

use Adldap\Exceptions\AdldapException;
use Adldap\Objects\Mailbox;

class Exchange extends AbstractQueryable
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
