<?php

namespace Adldap\Classes;

use Adldap\Collections\AdldapComputerCollection;

/**
 * Ldap Computer Management
 *
 * Class AdldapComputers
 * @package Adldap\Classes
 */
class AdldapComputers extends AdldapBase
{
    /**
     * The default query attributes to use when querying
     * computer information.
     *
     * @var array
     */
    public $defaultQueryAttributes = array(
        "memberof",
        "cn",
        "displayname",
        "dnshostname",
        "distinguishedname",
        "objectcategory",
        "operatingsystem",
        "operatingsystemservicepack",
        "operatingsystemversion"
    );

    /**
     * Get information about a specific computer. Returned in a raw array format from AD
     *
     * @param string $computerName The name of the computer
     * @param array $fields Attributes to return
     * @return array|bool
     */
    public function info($computerName, array $fields = array())
    {
        $this->adldap->utilities()->validateNotNull('Computer Name', $computerName);

        $this->adldap->utilities()->validateLdapIsBound();

        $results = $this->adldap->search()
            ->where('objectclass', '=', 'computer')
            ->where('cn', '=', $computerName)
            ->get();
        
        return $results;
    }

    /**
     * Find information about the computers. Returned in a raw array format from AD
     *
     * @param string $computerName The name of the computer
     * @param array $fields Array of parameters to query
     * @return AdldapComputerCollection|bool
     */
    public function infoCollection($computerName, array $fields = array())
    {
        $info = $this->info($computerName, $fields);
        
        if ($info) return new AdldapComputerCollection($info, $this->adldap);

        return false;
    }

    /**
     * Check if a computer is in a group.
     *
     * @param string $computerName The name of the computer
     * @param string $group The group to check
     * @param null $recursive Whether to check recursively
     * @return bool
     */
    public function inGroup($computerName, $group, $recursive = NULL)
    {
        if ($recursive === NULL) $recursive = $this->adldap->getRecursiveGroups(); // Use the default option if they haven't set it

        // Get a list of the groups
        $groups = $this->groups($computerName, array("memberof"), $recursive);

        // Return true if the specified group is in the group list
        if (in_array($group, $groups)) return true;

        return false;
    }

    /**
     * Get the groups a computer is in.
     *
     * @param string $computerName The name of the computer
     * @param null $recursive Whether to check recursively
     * @return array|bool
     */
    public function groups($computerName, $recursive = NULL)
    {
        $this->adldap->utilities()->validateNotNull('Computer Name', $computerName);

        $this->adldap->utilities()->validateLdapIsBound();

        // Use the default option if they haven't set it
        if ($recursive === NULL) $recursive = $this->adldap->getRecursiveGroups();

        // Search the directory for their information
        $info = $this->info($computerName, array("memberof", "primarygroupid"));

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
}
