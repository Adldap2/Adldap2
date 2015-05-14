<?php

namespace Adldap\Classes;

use Adldap\Exceptions\AdldapException;
use Adldap\Exceptions\PasswordPolicyException;
use Adldap\Exceptions\WrongPasswordException;
use Adldap\Objects\AccountControl;
use Adldap\Objects\User;
use Adldap\Adldap;

/**
 * Ldap User management.
 *
 * Class AdldapUsers
 */
class AdldapUsers extends AbstractAdldapQueryable
{
    /**
     * Validate a user's login credentials.
     *
     * @param string $username      The users AD username
     * @param string $password      The users AD password
     * @param bool   $preventRebind
     *
     * @return bool
     */
    public function authenticate($username, $password, $preventRebind = false)
    {
        return $this->adldap->authenticate($username, $password, $preventRebind);
    }

    /**
     * Returns all users from the current connection.
     *
     * @param array  $fields
     * @param bool   $sorted
     * @param string $sortBy
     * @param string $sortByDirection
     *
     * @return array|bool
     *
     * @throws AdldapException
     */
    public function all($fields = [], $sorted = true, $sortBy = 'cn', $sortByDirection = 'asc')
    {
        $personCategory = $this->adldap->getPersonFilter('category');
        $person = $this->adldap->getPersonFilter('person');

        $search = $this->adldap->search()
            ->select($fields)
            ->where($personCategory, '=', $person)
            ->where('samaccounttype', '=', Adldap::ADLDAP_NORMAL_ACCOUNT);

        if ($sorted) {
            $search->sortBy($sortBy, $sortByDirection);
        }

        return $search->get();
    }

    /**
     * Finds a user with the specified username
     * in the connection connection.
     *
     * The username parameter can be any attribute on the user.
     * Such as a their name, their logon, their mail, etc.
     *
     * @param string $username
     * @param array  $fields
     *
     * @return array|bool
     */
    public function find($username, $fields = [])
    {
        $this->adldap->utilities()->validateNotNullOrEmpty('Username', $username);

        $personCategory = $this->adldap->getPersonFilter('category');
        $person = $this->adldap->getPersonFilter('person');

        return $this->adldap->search()
            ->select($fields)
            ->where($personCategory, '=', $person)
            ->where('samaccounttype', '=', Adldap::ADLDAP_NORMAL_ACCOUNT)
            ->where('anr', '=', $username)
            ->first();
    }

    /**
     * Create a user.
     *
     * If you specify a password here, this can only be performed over SSL.
     *
     * @param array $attributes The attributes to set to the user account
     *
     * @return bool|string
     *
     * @throws AdldapException
     */
    public function create(array $attributes)
    {
        $user = new User($attributes);

        if ($user->hasAttribute('password') && !$this->connection->canChangePasswords()) {
            throw new AdldapException('SSL must be configured on your web server and enabled in the class to set passwords.');
        }

        // Translate the schema
        $add = $this->adldap->ldapSchema($user->toCreateSchema());

        // Additional stuff only used for adding accounts
        $add['cn'][0] = $user->getAttribute('display_name');
        $add[$this->adldap->getUserIdKey()][0] = $user->getAttribute('username');
        $add['objectclass'][0] = 'top';
        $add['objectclass'][1] = 'person';
        $add['objectclass'][2] = 'organizationalPerson';
        $add['objectclass'][3] = 'user';

        // Set the account control attribute
        $controlOptions = ['NORMAL_ACCOUNT' => true];

        if (!$user->hasAttribute('enabled')) {
            $controlOptions['ACCOUNTDISABLE'] = true;
        }

        $add['userAccountControl'][0] = $this->accountControl($controlOptions);

        // Determine the container
        $attributes['container'] = array_reverse($user->getAttribute('container'));

        $container = 'OU='.implode(',OU=', $user->getAttribute('container'));

        $dn = 'CN='.$add['cn'][0].','.$container.','.$this->adldap->getBaseDn();

        // Add the entry
        return $this->connection->add($dn, $add);
    }

    /**
     * Determine a user's password expiry date.
     *
     * @param $username
     *
     * @return array|bool
     *
     * @throws AdldapException
     * @requires bcmod http://php.net/manual/en/function.bcmod.php
     */
    public function passwordExpiry($username)
    {
        $this->adldap->utilities()->validateBcmodExists();

        $user = $this->info($username, ['pwdlastset', 'useraccountcontrol']);

        if (is_array($user) && array_key_exists('pwdlastset', $user)) {
            $pwdLastSet = $user['pwdlastset'];

            $status = [
                'expires' => true,
                'has_expired' => false,
            ];

            // Check if the password expires
            if (array_key_exists('useraccountcontrol', $user) && $user['useraccountcontrol'] == '66048') {
                $status['expires'] = false;
            }

            // Check if the password is expired
            if ($pwdLastSet === '0') {
                $status['has_expired'] = true;
            }

            $result = $this->adldap->search()
                ->select(['maxPwdAge'])
                ->where('objectclass', '*')
                ->first();

            if ($result && $status['expires'] === true) {
                $maxPwdAge = $result['maxpwdage'];

                // See MSDN: http://msdn.microsoft.com/en-us/library/ms974598.aspx
                if (bcmod($maxPwdAge, 4294967296) === '0') {
                    return 'Domain does not expire passwords';
                }

                // Add maxpwdage and pwdlastset and we get password expiration time in Microsoft's
                // time units.  Because maxpwd age is negative we need to subtract it.
                $pwdExpire = bcsub($pwdLastSet, $maxPwdAge);

                // Convert MS's time to Unix time
                $unixTime = bcsub(bcdiv($pwdExpire, '10000000'), '11644473600');

                $status['expiry_timestamp'] = $unixTime;
                $status['expiry_formatted'] = date('Y-m-d H:i:s', $unixTime);
            }

            return $status;
        }

        return false;
    }

    /**
     * Modify a user.
     *
     * @param string $username   The username to query
     * @param array  $attributes The attributes to modify.  Note if you set the enabled attribute you must not specify any other attributes
     * @param bool   $isGUID     Is the username passed a GUID or a samAccountName
     *
     * @return bool|string
     *
     * @throws AdldapException
     */
    public function modify($username, $attributes, $isGUID = false)
    {
        $user = new User($attributes);

        /*
         * Set the username attribute manually so it's properly
         * validated using toModifySchema method
         */
        $user->setAttribute('username', $username);

        if ($user->getAttribute('password') && !$this->connection->canChangePasswords()) {
            throw new AdldapException('SSL/TLS must be configured on your webserver and enabled in the class to set passwords.');
        }

        // Find the dn of the user
        $userDn = $this->dn($username, $isGUID);

        if ($userDn === false) {
            return false;
        }

        // Translate the update to the LDAP schema
        $mod = $this->adldap->ldapSchema($user->toModifySchema());

        $enabled = $user->getAttribute('enabled');

        // Check to see if this is an enabled status update
        if (!$mod && !$enabled) {
            return false;
        }

        if ($enabled) {
            $controlOptions = ['NORMAL_ACCOUNT'];
        } else {
            $controlOptions = ['NORMAL_ACCOUNT', 'ACCOUNTDISABLE'];
        }

        $mod['userAccountControl'][0] = $this->accountControl($controlOptions);

        // Do the update
        return $this->connection->modify($userDn, $mod);
    }

    /**
     * Disable a user account.
     *
     * @param string $username The username to disable
     * @param bool   $isGUID   Is the username passed a GUID or a samAccountName
     *
     * @return bool|string
     *
     * @throws AdldapException
     */
    public function disable($username, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);

        $attributes = ['enabled' => 0];

        return $this->modify($username, $attributes, $isGUID);
    }

    /**
     * Enable a user account.
     *
     * @param string $username The username to enable
     * @param bool   $isGUID   Is the username passed a GUID or a samAccountName
     *
     * @return bool|string
     *
     * @throws AdldapException
     */
    public function enable($username, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);

        $attributes = ['enabled' => 1];

        return $this->modify($username, $attributes, $isGUID);
    }

    /**
     * Set the password of a user - This must be performed over SSL.
     *
     * @param string $username The username to modify
     * @param string $password The new password
     * @param bool   $isGUID   Is the username passed a GUID or a samAccountName
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function password($username, $password, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);
        $this->adldap->utilities()->validateNotNull('Password', $password);

        $this->adldap->utilities()->validateLdapIsBound();

        if (!$this->adldap->getUseSSL() && !$this->adldap->getUseTLS()) {
            $message = 'SSL must be configured on your webserver and enabled in the class to set passwords.';

            throw new AdldapException($message);
        }

        $userDn = $this->dn($username, $isGUID);

        if ($userDn === false) {
            return false;
        }

        $add = [];

        $add['unicodePwd'][0] = $this->encodePassword($password);

        $result = $this->connection->modReplace($userDn, $add);

        if ($result === false) {
            $err = $this->connection->errNo();

            if ($err) {
                $error = $this->connection->err2Str($err);

                $msg = 'Error '.$err.': '.$error.'.';

                if ($err == 53) {
                    $msg .= ' Your password might not match the password policy.';
                }

                throw new AdldapException($msg);
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Change the password of a user - This must be performed over SSL
     * Requires PHP 5.4 >= 5.4.26, PHP 5.5 >= 5.5.10 or PHP 5.6 >= 5.6.0.
     *
     * @param string $username    The username to modify
     * @param string $password    The new password
     * @param string $oldPassword The old password
     * @param bool   $isGUID      Is the username passed a GUID or a samAccountName
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function changePassword($username, $password, $oldPassword, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);
        $this->adldap->utilities()->validateNotNull('Password', $password);
        $this->adldap->utilities()->validateNotNull('Old Password', $oldPassword);

        $this->adldap->utilities()->validateLdapIsBound();

        if (!$this->adldap->getUseSSL() && !$this->adldap->getUseTLS()) {
            $message = 'SSL must be configured on your webserver and enabled in the class to set passwords.';

            throw new AdldapException($message);
        }

        if (!$this->connection->isBatchSupported()) {
            $message = 'Missing function support [ldap_modify_batch] http://php.net/manual/en/function.ldap-modify-batch.php';

            throw new AdldapException($message);
        }

        $userDn = $this->dn($username, $isGUID);

        if ($userDn === false) {
            return false;
        }

        $modification = [
            [
                'attrib' => 'unicodePwd',
                'modtype' => LDAP_MODIFY_BATCH_REMOVE,
                'values' => [$this->encodePassword($oldPassword)],
            ],
            [
                'attrib' => 'unicodePwd',
                'modtype' => LDAP_MODIFY_BATCH_ADD,
                'values' => [$this->encodePassword($password)],
            ],
        ];

        $result = $this->connection->modifyBatch($userDn, $modification);

        if ($result === false) {
            $error = $this->connection->getExtendedError();

            if ($error) {
                $errorCode = $this->connection->getExtendedErrorCode();

                $msg = 'Error: '.$error;

                if ($errorCode == '0000052D') {
                    $msg = "Error: $errorCode. Your new password might not match the password policy.";

                    throw new PasswordPolicyException($msg);
                } elseif ($errorCode == '00000056') {
                    $msg = "Error: $errorCode. Your old password might be wrong.";

                    throw new WrongPasswordException($msg);
                }

                throw new AdldapException($msg);
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Encode a password for transmission over LDAP.
     *
     * @param string $password The password to encode
     *
     * @return string
     */
    public function encodePassword($password)
    {
        $password = '"'.$password.'"';

        $encoded = '';

        $length = strlen($password);

        for ($i = 0; $i < $length; $i++) {
            $encoded .= "{$password{$i}
            }\000";
        }

        return $encoded;
    }

    /**
     * Converts a username (samAccountName) to a GUID.
     *
     * @param string $username The username to query
     *
     * @return bool|string
     */
    public function usernameToGuid($username)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);

        $this->adldap->utilities()->validateLdapIsBound();

        $filter = $this->adldap->getUserIdKey().'='.$username;

        $fields = ['objectGUID'];

        $results = $this->connection->search($this->adldap->getBaseDn(), $filter, $fields);

        $numEntries = $this->connection->countEntries($results);

        if ($numEntries > 0) {
            $entry = $this->connection->getFirstEntry($results);

            $guid = $this->connection->getValuesLen($entry, 'objectGUID');

            $strGUID = $this->adldap->utilities()->binaryToText($guid[0]);

            return $strGUID;
        }

        return false;
    }

    /**
     * Move a user account to a different OU.
     *
     * When specifying containers, it accepts containers in 1. parent 2. child order
     *
     * @param string $username  The username to move
     * @param string $container The container or containers to move the user to
     *
     * @return bool|string
     */
    public function move($username, $container)
    {
        $user = new User([
            'username' => $username,
            'container' => $container,
        ]);

        // Validate only the username and container attributes
        $user->validateRequired(['username', 'container']);

        $this->adldap->utilities()->validateLdapIsBound();

        $userInfo = $this->info($user->getAttribute('username'));

        $dn = $userInfo['dn'];

        $newRDn = 'cn='.$user->getAttribute('username');

        $container = array_reverse($container);

        $newContainer = 'ou='.implode(',ou=', $container);

        $newBaseDn = strtolower($newContainer).','.$this->adldap->getBaseDn();

        return $this->connection->rename($dn, $newRDn, $newBaseDn, true);
    }

    /**
     * Get the last logon time of any user as a Unix timestamp.
     *
     * @param string $username
     *
     * @return float|bool|string
     */
    public function getLastLogon($username)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);

        $userInfo = $this->info($username, ['lastlogontimestamp']);

        if (is_array($userInfo) && array_key_exists('lastlogontimestamp', $userInfo)) {
            return AdldapUtils::convertWindowsTimeToUnixTime($userInfo['lastlogontimestamp']);
        }

        return false;
    }

    /**
     * Account control options.
     *
     * @param array $options The options to convert to int
     *
     * @return int
     */
    protected function accountControl($options)
    {
        $accountControl = new AccountControl($options);

        return intval($accountControl->getAttribute('value'));
    }
}
