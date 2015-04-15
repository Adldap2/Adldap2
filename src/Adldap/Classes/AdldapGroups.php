<?php

namespace Adldap\Classes;

use Adldap\Collections\AdldapGroupCollection;
use Adldap\Objects\Group;
use Adldap\Adldap;

/**
 * Ldap Group management
 *
 * Class AdldapGroups
 * @package Adldap\classes
 */
class AdldapGroups extends AdldapBase
{
    /**
     * The default query fields to use when
     * request group information.
     *
     * @var array
     */
    public $defaultQueryFields = array(
        "member",
        "memberof",
        "cn",
        "description",
        "distinguishedname",
        "objectcategory",
        "samaccountname"
    );

    /**
     * Add a group to a group
     *
     * @param string $parent The parent group name
     * @param string $child The child group name
     * @return bool
     */
    public function addGroup($parent,$child)
    {
        // Find the parent group's dn
        $parentGroup = $this->info($parent, array("cn"));

        if ($parentGroup[0]["dn"] === NULL) return false;

        $parentDn = $parentGroup[0]["dn"];

        // Find the child group's dn
        $childGroup = $this->info($child, array("cn"));

        if ($childGroup[0]["dn"] === NULL) return false;

        $childDn = $childGroup[0]["dn"];

        $add = array();
        $add["member"] = $childDn;

        return $this->connection->modAdd($parentDn, $add);
    }

    /**
     * Add a user to a group
     *
     * @param string $group The group to add the user to
     * @param string $user The user to add to the group
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return bool
     */
    public function addUser($group, $user, $isGUID = false)
    {
        // Adding a user is a bit fiddly, we need to get the full DN of the user
        // and add it using the full DN of the group

        // Find the user's dn
        $userDn = $this->adldap->user()->dn($user, $isGUID);

        if ($userDn === false) return false;

        // Find the group's dn
        $groupInfo = $this->info($group, array("cn"));

        if ($groupInfo[0]["dn"] === NULL) return false;

        $groupDn = $groupInfo[0]["dn"];

        $add = array();
        $add["member"] = $userDn;

        return $this->connection->modAdd($groupDn, $add);
    }

    /**
     * Add a contact to a group
     *
     * @param string $group The group to add the contact to
     * @param string $contactDn The DN of the contact to add
     * @return bool
     */
    public function addContact($group, $contactDn)
    {
        // To add a contact we take the contact's DN
        // and add it using the full DN of the group

        // Find the group's dn
        $groupInfo = $this->info($group, array("cn"));

        if ($groupInfo[0]["dn"] === NULL) return false;

        $groupDn = $groupInfo[0]["dn"];

        $add = array();
        $add["member"] = $contactDn;

        return $this->connection->modAdd($groupDn, $add);
    }

    /**
     * Create a group
     *
     * @param array $attributes Default attributes of the group
     * @return bool|string
     */
    public function create(array $attributes)
    {
        $group = new Group($attributes);

        $group->validateRequired();

        $group->setAttribute('container', array_reverse($group->getAttribute('container')));

        $add = array();

        $add["cn"] = $group->getAttribute("group_name");
        $add["samaccountname"] = $group->getAttribute("group_name");
        $add["objectClass"] = "Group";
        $add["description"] = $group->getAttribute("description");

        $container = "OU=" . implode(",OU=", $group->getAttribute("container"));

        $dn = "CN=" . $add["cn"] . ", " . $container . "," . $this->adldap->getBaseDn();

        return $this->connection->add($dn, $add);
    }

    /**
     * Delete a group account
     *
     * @param string $group The group to delete (please be careful here!)
     * @return bool|string
     */
    public function delete($group)
    {
        $this->adldap->utilities()->validateNotNull('Group', $group);

        $this->adldap->utilities()->validateLdapIsBound();

        $groupInfo = $this->info($group, array("*"));

        $dn = $groupInfo[0]['distinguishedname'][0];

        return $this->adldap->folder()->delete($dn);
    }

    /**
     * Rename a group
     *
     * @param string $group The group to rename
     * @param string $newName The new name to give the group
     * @param array $container
     * @return bool
     */
    public function rename($group, $newName, $container)
    {
        $info = $this->info($group);

        if ($info[0]["dn"] === NULL)
        {
            return false;
        } else
        {
            $groupDN = $info[0]["dn"];
        }

        $newRDN = 'CN='.$newName;

        // Determine the container
        $container = array_reverse($container);
        $container = "OU=" . implode(", OU=", $container);

        // Do the update
        $dn = $container.', '.$this->adldap->getBaseDn();

        return $this->connection->rename($groupDN, $newRDN, $dn, true);
    }

    /**
     * Remove a group from a group
     *
     * @param string $parent The parent group name
     * @param string $child The child group name
     * @return bool
     */
    public function removeGroup($parent , $child)
    {
        // Find the parent dn
        $parentGroup = $this->info($parent, array("cn"));

        if ($parentGroup[0]["dn"] === NULL) return false;

        $parentDn = $parentGroup[0]["dn"];

        // Find the child dn
        $childGroup = $this->info($child, array("cn"));

        if ($childGroup[0]["dn"] === NULL) return false;

        $childDn = $childGroup[0]["dn"];

        $del = array();
        $del["member"] = $childDn;

        return $this->connection->modDelete($parentDn, $del);
    }

    /**
     * Remove a user from a group
     *
     * @param string $group The group to remove a user from
     * @param string $user The AD user to remove from the group
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return bool
     */
    public function removeUser($group, $user, $isGUID = false)
    {
        // Find the parent dn
        $groupInfo = $this->info($group, array("cn"));

        if ($groupInfo[0]["dn"] === NULL) return false;

        $groupDn = $groupInfo[0]["dn"];

        // Find the users dn
        $userDn = $this->adldap->user()->dn($user, $isGUID);

        if ($userDn === false)return false;

        $del = array();
        $del["member"] = $userDn;

        return $this->connection->modDelete($groupDn, $del);
    }

    /**
     * Remove a contact from a group
     *
     * @param string $group The group to remove a user from
     * @param string $contactDn The DN of a contact to remove from the group
     * @return bool
     */
    public function removeContact($group, $contactDn)
    {
        // Find the parent dn
        $groupInfo = $this->info($group, array("cn"));

        if ($groupInfo[0]["dn"] === NULL) return false;

        $groupDn = $groupInfo[0]["dn"];

        $del = array();
        $del["member"] = $contactDn;

        return $this->connection->modDelete($groupDn, $del);
    }

    /**
     * Return a list of groups in a group
     *
     * @param string $group The group to query
     * @param null $recursive Recursively get groups
     * @return array|bool
     */
    public function inGroup($group, $recursive = NULL)
    {
        $this->adldap->utilities()->validateLdapIsBound();

        // Use the default option if they haven't set it
        if ($recursive === NULL) $recursive = $this->adldap->getRecursiveGroups();

        // Search the directory for the members of a group
        $info = $this->info($group, array("member","cn"));

        $groups = $info[0]["member"];

        if ( ! is_array($groups)) return false;

        $groupArray = array();

        for ($i = 0; $i < $groups["count"]; $i++)
        {
            $filter = "(&(objectCategory=group)(distinguishedName=" . $this->adldap->utilities()->ldapSlashes($groups[$i]) . "))";

            $fields = array("samaccountname", "distinguishedname", "objectClass");

            $results = $this->connection->search($this->adldap->getBaseDn(), $filter, $fields);

            $entries = $this->connection->getEntries($results);

            // not a person, look for a group
            if ($entries['count'] == 0 && $recursive === true)
            {
                $filter = "(&(objectCategory=group)(distinguishedName=" . $this->adldap->utilities()->ldapSlashes($groups[$i]) . "))";

                $fields = array("distinguishedname");

                $results = $this->connection->search($this->adldap->getBaseDn(), $filter, $fields);

                $entries = $this->connection->getEntries($results);

                if ( ! isset($entries[0]['distinguishedname'][0])) continue;

                $subGroups = $this->inGroup($entries[0]['distinguishedname'][0], $recursive);

                if (is_array($subGroups))
                {
                    $groupArray = array_merge($groupArray, $subGroups);
                    $groupArray = array_unique($groupArray);
                }

                continue;
            }

             $groupArray[] = $entries[0]['distinguishedname'][0];
        }

        return $groupArray;
    }

    /**
     * Return a list of members in a group
     *
     * @param string $group The group to query
     * @param null $recursive Recursively get group members
     * @return array|bool
     */
    public function members($group, $recursive = NULL)
    {
        $this->adldap->utilities()->validateLdapIsBound();

        if ($recursive === NULL) $recursive = $this->adldap->getRecursiveGroups(); // Use the default option if they haven't set it

        // Search the directory for the members of a group
        $info = $this->info($group, array("member","cn"));

        if (isset($info[0]["member"]))
        {
            $users = $info[0]["member"];

            if ( ! is_array($users)) return false;
        } else
        {
            return false;
        }

        $userArray = array();

        for ($i = 0; $i < $users["count"]; $i++)
        {
            $filter = "(&(objectCategory=person)(distinguishedName=" . $this->adldap->utilities()->ldapSlashes($users[$i]) . "))";

            $fields = array("samaccountname", "distinguishedname", "objectClass");

            $results = $this->connection->search($this->adldap->getBaseDn(), $filter, $fields);

            $entries = $this->connection->getEntries($results);

            // not a person, look for a group
            if ($entries['count'] == 0 && $recursive === true)
            {
                $filter = "(&(objectCategory=group)(distinguishedName=" . $this->adldap->utilities()->ldapSlashes($users[$i]) . "))";

                $fields = array("samaccountname");

                $results = $this->connection->search($this->adldap->getBaseDn(), $filter, $fields);

                $entries = $this->connection->getEntries($results);

                if ( ! isset($entries[0]['samaccountname'][0])) continue;

                $subUsers = $this->members($entries[0]['samaccountname'][0], $recursive);

                if (is_array($subUsers))
                {
                    $userArray = array_merge($userArray, $subUsers);
                    $userArray = array_unique($userArray);
                }

                continue;

            } else if ($entries['count'] == 0) continue;

            if (( ! isset($entries[0]['samaccountname'][0]) || $entries[0]['samaccountname'][0] === NULL) && $entries[0]['distinguishedname'][0] !== NULL)
            {
                $userArray[] = $entries[0]['distinguishedname'][0];
            } else if ($entries[0]['samaccountname'][0] !== NULL)
            {
                $userArray[] = $entries[0]['samaccountname'][0];
            }
        }

        return $userArray;
    }

    /**
     * Group Information. Returns an array of raw information about a group.
     * The group name is case sensitive
     *
     * @param string $groupName The group name to retrieve info about
     * @param array $fields Fields to retrieve
     * @param bool $isGUID Is the groupName passed a GUID or a name
     * @return array|bool
     */
    public function info($groupName, array $fields = array(), $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Group Name', $groupName);

        $this->adldap->utilities()->validateLdapIsBound();

        // We'll assign the default query fields if none are given
        if (count($fields) === 0) $fields = $this->defaultQueryFields;

        if ($isGUID === true)
        {
            $filter = "objectguid=" . $this->adldap->utilities()->strGuidToHex($groupName);

        } else
        {
            if (stristr($groupName, '+')) $groupName = stripslashes($groupName);

            $filter = "name=" . $this->adldap->utilities()->ldapSlashes($groupName);
        }

        $filter = "(&(objectCategory=group)(name=$filter))";

        $results = $this->connection->search($this->adldap->getBaseDn(), $filter, $fields);

        $entries = $this->connection->getEntries($results);

        // Windows 2003: Returns up to 1500 values (Windows 2000 only 1000 is not supported).
        if (isset($entries[0]['member;range=0-1499']) && $entries[0]['member;range=0-1499']['count'] == 1500)
        {
            $entries[0]['member']['count'] = "0";

            $rangestep = 1499;     // Step site
            $rangelow  = 0;        // Initial low range
            $rangehigh = $rangelow + $rangestep;     // Initial high range

            // Do until array_keys($members[0])[0] ends with a '*', e. g. member;range=1499-*. It indicates end of the range
            do
            {
                $fields = array("member;range=" . $rangelow . "-" . $rangehigh);

                $results = $this->connection->search($this->adldap->getBaseDn(), $filter, $fields);

                $members = $this->connection->getEntries($results);

                $memberrange = array_keys($members[0]);

                $membercount = $members[0][$memberrange[0]]['count'];

                // Copy range entries to member
                for ($i = 0; $i <= $membercount -1; $i++)
                {
                    $entries[0]['member'][] = $members[0][$memberrange[0]][$i];
                }

                $entries[0]['member']['count'] += $membercount;

                $rangelow  += $rangestep +1;
                $rangehigh += $rangestep +1;

            } while (substr($memberrange[0], -1) != '*');
        }

        return $entries;
    }

    /**
     * Group Information. Returns a collection.
     *
     * The group name is case sensitive.
     *
     * @param string $groupName The group name to retrieve info about
     * @param null $fields Fields to retrieve
     * @param bool $isGUID Is the groupName passed a GUID or a name
     * @return \Adldap\collections\AdldapGroupCollection|bool
     */
    public function infoCollection($groupName, $fields = NULL, $isGUID = false)
    {
        $info = $this->info($groupName, $fields, $isGUID);

        if ($info) return new AdldapGroupCollection($info, $this->adldap);

        return false;
    }

    /**
     * Return a complete list of "groups in groups"
     *
     * @param string $group The group to get the list from
     * @return array|bool
     */
    public function recursiveGroups($group)
    {
        $this->adldap->utilities()->validateNotNull('Group', $group);

        $stack = array();
        $processed = array();
        $retGroups = array();

        array_push($stack, $group); // Initial Group to Start with

        while (count($stack) > 0)
        {
            $parent = array_pop($stack);

            array_push($processed, $parent);

            $info = $this->info($parent, array("memberof"));

            if (isset($info[0]["memberof"]) && is_array($info[0]["memberof"]))
            {
                $groups = $info[0]["memberof"];

                if ($groups)
                {
                    $groupNames = $this->adldap->utilities()->niceNames($groups);

                    $retGroups = array_merge($retGroups, $groupNames); //final groups to return

                    foreach ($groupNames as $id => $groupName)
                    {
                        if ( ! in_array($groupName, $processed))
                        {
                            array_push($stack, $groupName);
                        }
                    }
                }
            }
        }

        return $retGroups;
    }

    /**
     * Returns a complete list of the groups in AD based on a SAM Account Type
     *
     * @param int $sAMAaccountType The account type to return
     * @param bool $includeDescription Whether to return a description
     * @param string $search Search parameters
     * @param bool $sorted Whether to sort the results
     * @return array|bool
     */
    public function search($sAMAaccountType = Adldap::ADLDAP_SECURITY_GLOBAL_GROUP, $includeDescription = false, $search = "*", $sorted = true)
    {
        $this->adldap->utilities()->validateLdapIsBound();

        $filter = '(&(objectCategory=group)';

        if ($sAMAaccountType !== null)
        {
            $filter .= '(samaccounttype='. $sAMAaccountType .')';
        }

        $filter .= '(cn=' . $search . '))';

        // Perform the search and grab all their details
        $fields = array("samaccountname", "description");

        $results = $this->connection->search($this->adldap->getBaseDn(), $filter, $fields);

        $entries = $this->connection->getEntries($results);

        $groupsArray = array();

        for ($i = 0; $i < $entries["count"]; $i++)
        {
            if ($includeDescription && ! empty($entries[$i]["description"][0]))
            {
                $groupsArray[$entries[$i]["samaccountname"][0]] = $entries[$i]["description"][0];
            }
            else if ($includeDescription)
            {
                $groupsArray[$entries[$i]["samaccountname"][0]] = $entries[$i]["samaccountname"][0];
            }
            else
            {
                array_push($groupsArray, $entries[$i]["samaccountname"][0]);
            }
        }

        if ($sorted) asort($groupsArray);

        return $groupsArray;
    }

    /**
     * Obtain the group's distinguished name based on their group ID
     *
     * @param string $groupName
     * @return string|bool
     */
    public function dn($groupName)
    {
        $group = $this->info($groupName, array("cn"));

        if ($group[0]["dn"] === NULL) return false;

        $groupDn = $group[0]["dn"];

        return $groupDn;
    }

    /**
     * Returns a complete list of all groups in AD
     *
     * @param bool $includeDescription Whether to return a description
     * @param string $search Search parameters
     * @param bool $sorted Whether to sort the results
     * @return array|bool
     */
    public function all($includeDescription = false, $search = "*", $sorted = true)
    {
        return $this->search(null, $includeDescription, $search, $sorted);
    }

    /**
     * Returns a complete list of security groups in AD
     *
     * @param bool $includeDescription Whether to return a description
     * @param string $search Search parameters
     * @param bool $sorted Whether to sort the results
     * @return array|bool
     */
    public function allSecurity($includeDescription = false, $search = "*", $sorted = true)
    {
        return $this->search(Adldap::ADLDAP_SECURITY_GLOBAL_GROUP, $includeDescription, $search, $sorted);
    }

    /**
     * Returns a complete list of distribution lists in AD
     *
     * @param bool $includeDescription Whether to return a description
     * @param string $search Search parameters
     * @param bool $sorted Whether to sort the results
     * @return array|bool
     */
    public function allDistribution($includeDescription = false, $search = "*", $sorted = true)
    {
        return $this->search(Adldap::ADLDAP_DISTRIBUTION_GROUP, $includeDescription, $search, $sorted);
    }

    /**
     * Coping with AD not returning the primary group
     * http://support.microsoft.com/?kbid=321360
     *
     * This is a re-write based on code submitted by Bruce which prevents the
     * need to search each security group to find the true primary group
     *
     * @param string $groupId Group ID
     * @param string  $userId User's Object SID
     * @return bool
     */
    public function getPrimaryGroup($groupId, $userId)
    {
        $this->adldap->utilities()->validateNotNull('Group ID', $groupId);
        $this->adldap->utilities()->validateNotNull('User ID', $userId);

        $groupId = substr_replace($userId, pack('V', $groupId), strlen($userId) - 4,4);

        $filter = '(objectsid=' . $this->adldap->utilities()->getTextSID($groupId).')';

        $fields = array("samaccountname","distinguishedname");

        $results = $this->connection->search($this->adldap->getBaseDn(), $filter, $fields);

        $entries = $this->connection->getEntries($results);

        if (isset($entries[0]['distinguishedname'][0]))
        {
            return $entries[0]['distinguishedname'][0];
        }

        return false;
    }

    /**
     * Coping with AD not returning the primary group
     * http://support.microsoft.com/?kbid=321360
     *
     * For some reason it's not possible to search on primarygrouptoken=XXX
     * If someone can show otherwise, I'd like to know about it :)
     * this way is resource intensive and generally a pain in the @#%^
     *
     * @deprecated deprecated since version 3.1, see get get_primary_group
     * @param string $groupId Group ID
     * @return bool|string
     */
    public function cn($groupId)
    {
        $this->adldap->utilities()->validateNotNull('Group ID', $groupId);

        $r = '';

        $filter = "(&(objectCategory=group)(samaccounttype=" . Adldap::ADLDAP_SECURITY_GLOBAL_GROUP . "))";

        $fields = array("primarygrouptoken", "samaccountname", "distinguishedname");

        $results = $this->connection->search($this->adldap->getBaseDn(), $filter, $fields);

        $entries = $this->connection->getEntries($results);

        for ($i = 0; $i < $entries["count"]; $i++)
        {
            if ($entries[$i]["primarygrouptoken"][0] == $groupId)
            {
                $r = $entries[$i]["distinguishedname"][0];
                $i = $entries["count"];
            }
        }

        return $r;
    }
}
