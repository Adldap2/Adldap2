<?php

namespace Adldap\Classes;

use Adldap\Exceptions\AdldapException;
use Adldap\Collections\AdldapUserCollection;
use Adldap\Objects\AccountControl;
use Adldap\Objects\User;
use Adldap\Adldap;

/**
 * Ldap User management
 *
 * Class AdldapUsers
 * @package Adldap\classes
 */
class AdldapUsers extends AdldapBase
{
    /**
     * Validate a user's login credentials
     *
     * @param string $username The users AD username
     * @param string $password The users AD password
     * @param bool $preventRebind
     * @return bool
     */
    public function authenticate($username, $password, $preventRebind = false)
    {
        return $this->adldap->authenticate($username, $password, $preventRebind);
    }

    /**
     * Returns all users from the current connection.
     *
     * @param array $fields
     * @param bool $sorted
     * @param string $sortBy
     * @param string $sortByDirection
     * @return array|bool
     * @throws AdldapException
     */
    public function all($fields = array(), $sorted = true, $sortBy = 'cn', $sortByDirection = 'asc')
    {
        $personCategory = $this->adldap->getPersonFilter('category');
        $person = $this->adldap->getPersonFilter('person');

        $search = $this->adldap->search()
            ->select($fields)
            ->where($personCategory, '=', $person)
            ->where('samaccounttype', '=', Adldap::ADLDAP_NORMAL_ACCOUNT);

        if($sorted) $search->sortBy($sortBy, $sortByDirection);

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
     * @param array $fields
     * @return array|bool
     */
    public function find($username, $fields = array())
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
     * Retrieves a user from the current connection.
     *
     * @param string $username
     * @param array $fields
     * @return array|bool
     */
    public function info($username, $fields = array())
    {
        return $this->find($username, $fields);
    }

    /**
     * Retrieve the user's distinguished name based on their username
     *
     * @param string $username The username
     * @return string|bool
     */
    public function dn($username)
    {
        $user = $this->find($username, array('distinguishedname'));

        if(is_array($user) && array_key_exists('dn', $user))
        {
            return $user['dn'];
        }
        
        return false;
    }

    /**
     * Create a user.
     *
     * If you specify a password here, this can only be performed over SSL.
     *
     * @param array $attributes The attributes to set to the user account
     * @return bool|string
     * @throws AdldapException
     */
    public function create(array $attributes)
    {
        $user = new User($attributes);

        if ($user->hasAttribute('password') && ! $this->connection->canChangePasswords())
        {
            throw new AdldapException('SSL must be configured on your web server and enabled in the class to set passwords.');
        }

        // Translate the schema
        $add = $this->adldap->ldapSchema($user->toCreateSchema());
        
        // Additional stuff only used for adding accounts
        $add["cn"][0] = $user->getAttribute("display_name");
        $add[$this->adldap->getUserIdKey()][0] = $user->getAttribute("username");
        $add["objectclass"][0] = "top";
        $add["objectclass"][1] = "person";
        $add["objectclass"][2] = "organizationalPerson";
        $add["objectclass"][3] = "user";

        // Set the account control attribute
        $controlOptions = array("NORMAL_ACCOUNT");

        if ( ! $user->hasAttribute("enabled")) $controlOptions[] = "ACCOUNTDISABLE";

        $add["userAccountControl"][0] = $this->accountControl($controlOptions);
        
        // Determine the container
        $attributes["container"] = array_reverse($user->getAttribute("container"));

        $container = "OU=" . implode(", OU=", $user->getAttribute("container"));

        $dn = "CN=" . $add["cn"][0] . ", " . $container . "," . $this->adldap->getBaseDn();

        // Add the entry
        return $this->connection->add($dn, $add);
    }

    /**
     * Delete a user account
     *
     * @param string $username The username to delete
     * @return bool
     */
    public function delete($username)
    {
        $dn = $this->dn($username);

        if($dn) return $this->adldap->folder()->delete($dn);

        return false;
    }

    /**
     * Account control options.
     *
     * @param array $options The options to convert to int
     * @return int
     */
    protected function accountControl($options)
    {
        $accountControl = new AccountControl($options);

        return intval($accountControl->getAttribute('value'));
    }

    /**
     * Retrieves the groups that the specified user is apart of
     *
     * @param string $username The username of the user to query
     * @param null $recursive Recursive list of groups
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return array|bool
     */
    public function groups($username, $recursive = NULL, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);

        // Use the default option if they haven't set it
        if ($recursive === NULL) $recursive = $this->adldap->getRecursiveGroups();
        
        // Search the directory for their information
        $info = $this->info($username, array("memberof", "primarygroupid"), $isGUID);

        // Presuming the entry returned is our guy (unique usernames)
        $groups = $this->adldap->utilities()->niceNames($info[0]["memberof"]);

        if ($recursive === true)
        {
            foreach ($groups as $id => $groupName)
            {
                $extraGroups = $this->adldap->group()->recursiveGroups($groupName);

                $groups = array_merge($groups, $extraGroups);
            }
        }

        return $groups;
    }

    /**
     * Find information about the users. Returned in a raw array format from AD
     *
     * @param string $username The username to query
     * @param array $fields Array of parameters to query
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return AdldapUserCollection|bool
     */
    public function infoCollection($username, array $fields = array(), $isGUID = false)
    {
        $info = $this->info($username, $fields, $isGUID);
        
        if ($info) return new AdldapUserCollection($info, $this->adldap);

        return false;
    }

    /**
     * Determine if the specified user is in the specified group
     *
     * @param string $username The username to query
     * @param string $group The name of the group to check against
     * @param null $recursive Check groups recursively
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return bool
     */
    public function inGroup($username, $group, $recursive = NULL, $isGUID = false)
    {
        // Use the default option if they haven't set it
        if ($recursive === NULL) $recursive = $this->adldap->getRecursiveGroups();
        
        // Get a list of the groups
        $groups = $this->groups($username, $recursive, $isGUID);
        
        // Return true if the specified group is in the group list
        if (in_array($group, $groups)) return true;

        return false;
    }

    /**
     * Determine a user's password expiry date
     *
     * @param $username
     * @param bool $isGUID
     * @return array|bool|string
     * @throws AdldapException
     * @requires bcmod http://php.net/manual/en/function.bcmod.php
     */
    public function passwordExpiry($username, $isGUID = false)
    {
        if ( ! function_exists('bcmod'))
        {
            $message = "Missing function support [bcmod] http://php.net/manual/en/function.bcmod.php";

            throw new AdldapException($message);
        }

        $userInfo = $this->info($username, array("pwdlastset", "useraccountcontrol"), $isGUID);

        $pwdLastSet = $userInfo[0]['pwdlastset'][0];

        $status = array();

        if ($userInfo[0]['useraccountcontrol'][0] == '66048')
        {
            return "Does not expire";
        }

        if ($pwdLastSet === '0')
        {
            return "Password has expired";
        }

        // Password expiry in AD can be calculated from TWO values:
        //   - User's own pwdLastSet attribute: stores the last time the password was changed
        //   - Domain's maxPwdAge attribute: how long passwords last in the domain
        //
        // Although Microsoft chose to use a different base and unit for time measurements.
        // This function will convert them to Unix timestamps
        $filter = 'objectclass=*';

        $results = $this->connection->read($this->adldap->getBaseDn(), $filter, array('maxPwdAge'));

        if ( ! $results) return false;

        $info = $this->connection->getEntries($results);

        $maxPwdAge = $info[0]['maxpwdage'][0];

        // See MSDN: http://msdn.microsoft.com/en-us/library/ms974598.aspx
        //
        // pwdLastSet contains the number of 100 nanosecond intervals since January 1, 1601 (UTC),
        // stored in a 64 bit integer.
        //
        // The number of seconds between this date and Unix epoch is 11644473600.
        //
        // maxPwdAge is stored as a large integer that represents the number of 100 nanosecond
        // intervals from the time the password was set before the password expires.
        //
        // We also need to scale this to seconds but also this value is a _negative_ quantity!
        //
        // If the low 32 bits of maxPwdAge are equal to 0 passwords do not expire
        //
        // Unfortunately the maths involved are too big for PHP integers, so I've had to require
        // BCMath functions to work with arbitrary precision numbers.
        if (bcmod($maxPwdAge, 4294967296) === '0')
        {
            return "Domain does not expire passwords";
        }

        // Add maxpwdage and pwdlastset and we get password expiration time in Microsoft's
        // time units.  Because maxpwd age is negative we need to subtract it.
        $pwdExpire = bcsub($pwdLastSet, $maxPwdAge);

        // Convert MS's time to Unix time
        $status['expiryts'] = bcsub(bcdiv($pwdExpire, '10000000'), '11644473600');
        $status['expiryformat'] = date('Y-m-d H:i:s', bcsub(bcdiv($pwdExpire, '10000000'), '11644473600'));

        return $status;
    }

    /**
     * Modify a user
     *
     * @param string $username The username to query
     * @param array $attributes The attributes to modify.  Note if you set the enabled attribute you must not specify any other attributes
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return bool|string
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

        if ($user->getAttribute('password') && ! $this->connection->canChangePasswords())
        {
            throw new AdldapException('SSL/TLS must be configured on your webserver and enabled in the class to set passwords.');
        }

        // Find the dn of the user
        $userDn = $this->dn($username, $isGUID);

        if ($userDn === false) return false;
        
        // Translate the update to the LDAP schema                
        $mod = $this->adldap->ldapSchema($user->toModifySchema());

        $enabled = $user->getAttribute('enabled');

        // Check to see if this is an enabled status update
        if ( ! $mod && ! $enabled) return false;

        if ($enabled)
        {
            $controlOptions = array("NORMAL_ACCOUNT");
        } else
        {
            $controlOptions = array("NORMAL_ACCOUNT", "ACCOUNTDISABLE");
        }

        $mod["userAccountControl"][0] = $this->accountControl($controlOptions);

        // Do the update
        return $this->connection->modify($userDn, $mod);
    }

    /**
     * Disable a user account.
     *
     * @param string $username The username to disable
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return bool|string
     * @throws AdldapException
     */
    public function disable($username, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);

        $attributes = array("enabled" => 0);

        return $this->modify($username, $attributes, $isGUID);
    }

    /**
     * Enable a user account.
     *
     * @param string $username The username to enable
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return bool|string
     * @throws AdldapException
     */
    public function enable($username, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);

        $attributes = array("enabled" => 1);

        return $this->modify($username, $attributes, $isGUID);
    }

    /**
     * Set the password of a user - This must be performed over SSL
     *
     * @param string $username The username to modify
     * @param string $password The new password
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return bool
     * @throws AdldapException
     */
    public function password($username, $password, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);
        $this->adldap->utilities()->validateNotNull('Password', $password);

        $this->adldap->utilities()->validateLdapIsBound();

        if ( ! $this->adldap->getUseSSL() && ! $this->adldap->getUseTLS())
        {
            $message = 'SSL must be configured on your webserver and enabled in the class to set passwords.';

            throw new AdldapException($message);
        }
        
        $userDn = $this->dn($username, $isGUID);

        if ($userDn === false) return false;
                
        $add = array();

        $add["unicodePwd"][0] = $this->encodePassword($password);

        $result = $this->connection->modReplace($userDn, $add);

        if ($result === false)
        {
            $err = $this->connection->errNo();

            if ($err)
            {
                $error = $this->connection->err2Str($err);

                $msg = 'Error ' . $err . ': ' . $error . '.';

                if($err == 53)
                {
                    $msg .= ' Your password might not match the password policy.';
                }

                throw new AdldapException($msg);
            }
            else
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Encode a password for transmission over LDAP
     *
     * @param string $password The password to encode
     * @return string
     */
    public function encodePassword($password)
    {
        $password = "\"" . $password . "\"";

        $encoded = "";

        $length = strlen($password);

        for ($i = 0; $i < $length; $i++) $encoded .= "{$password{$i}}\000";

        return $encoded;
    }

    /**
     * Converts a username (samAccountName) to a GUID
     *
     * @param string $username The username to query
     * @return bool|string
     */
    public function usernameToGuid($username)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);

        $this->adldap->utilities()->validateLdapIsBound();

        $filter = $this->adldap->getUserIdKey() . "=" . $username;

        $fields = array("objectGUID");

        $results = $this->connection->search($this->adldap->getBaseDn(), $filter, $fields);

        $numEntries = $this->connection->countEntries($results);

        if ($numEntries > 0)
        {
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
     * @param string $username The username to move
     * @param string $container The container or containers to move the user to
     * @return bool|string
     */
    public function move($username, $container)
    {
        $user = new User(array(
            'username' => $username,
            'container' => $container,
        ));

        // Validate only the username and container attributes
        $user->validateRequired(array('username', 'container'));

        $this->adldap->utilities()->validateLdapIsBound();
        
        $userInfo = $this->info($user->getAttribute('username'), array("*"));

        $dn = $userInfo[0]['distinguishedname'][0];

        $newRDn = "cn=" . $user->getAttribute('username');

        $container = array_reverse($container);

        $newContainer = "ou=" . implode(",ou=", $container);

        $newBaseDn = strtolower($newContainer) . "," . $this->adldap->getBaseDn();

        return $this->connection->rename($dn, $newRDn, $newBaseDn, true);
    }

    /**
     * Get the last logon time of any user as a Unix timestamp
     *
     * @param string $username
     * @return float|bool|string
     */
    public function getLastLogon($username)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);

        $this->adldap->utilities()->validateLdapIsBound();

        $userInfo = $this->info($username, array("lastLogonTimestamp"));

        return AdldapUtils::convertWindowsTimeToUnixTime($userInfo[0]['lastLogonTimestamp'][0]);
    }

    /**
     * Parses the returned entries by LDAP into
     * a nicer array.
     *
     * @param array $entries
     * @param array $fields
     * @return mixed
     */
    protected function parseInfoEntries($entries, $fields)
    {
        if (isset($entries[0]) && array_key_exists('count', $entries[0]))
        {
            if ($entries[0]['count'] >= 1)
            {
                if (in_array("memberof", $fields))
                {
                    // AD does not return the primary group in the ldap query, we may need to fudge it
                    if ($this->adldap->getRealPrimaryGroup() && isset($entries[0]["primarygroupid"][0]) && isset($entries[0]["objectsid"][0]))
                    {
                        $entries[0]["memberof"][] = $this->adldap->group()->getPrimaryGroup($entries[0]["primarygroupid"][0], $entries[0]["objectsid"][0]);
                    } else
                    {
                        $entries[0]["memberof"][] = "CN=Domain Users,CN=Users," . $this->adldap->getBaseDn();
                    }

                    if ( ! isset($entries[0]["memberof"]["count"]))
                    {
                        $entries[0]["memberof"]["count"] = 0;
                    }

                    $entries[0]["memberof"]["count"]++;
                }
            }

            return $entries;
        }

        return false;
    }
}
