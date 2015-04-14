<?php

namespace adLDAP\classes;

/**
 * Ldap Computer Management
 *
 * PHP LDAP CLASS FOR MANIPULATING ACTIVE DIRECTORY
 * Version 5.0.0
 *
 * PHP Version 5 with SSL and LDAP support
 *
 * Written by Scott Barnett, Richard Hyland
 *   email: scott@wiggumworld.com, adldap@richardhyland.com
 *   http://github.com/adldap/adLDAP
 *
 * Copyright (c) 2006-2014 Scott Barnett, Richard Hyland
 *
 * We'd appreciate any improvements or additions to be submitted back
 * to benefit the entire community :)
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * @category ToolsAndUtilities
 * @package adLDAP
 * @subpackage Computers
 * @author Scott Barnett, Richard Hyland
 * @copyright (c) 2006-2014 Scott Barnett, Richard Hyland
 * @license http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPLv2.1
 * @version 5.0.0
 * @link http://github.com/adldap/adLDAP
 *
 * Class adLDAPComputers
 * @package adLDAP\classes
 */
class adLDAPComputers extends adLDAPBase
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

        $filter = "(&(objectClass=computer)(cn=" . $computerName . "))";

        if (count($fields) === 0)
        {
            $fields = $this->defaultQueryAttributes;
        }

        $results = $this->connection->search($this->adldap->getBaseDn(), $filter, $fields);

        $entries = $this->connection->getEntries($results);
        
        return $entries;
    }

    /**
     * Find information about the computers. Returned in a raw array format from AD
     *
     * @param string $computerName The name of the computer
     * @param array $fields Array of parameters to query
     * @return \adLDAP\collections\adLDAPComputerCollection|bool
     */
    public function infoCollection($computerName, array $fields = array())
    {
        $info = $this->info($computerName, $fields);
        
        if ($info !== false)
        {
            $collection = new \adLDAP\collections\adLDAPComputerCollection($info, $this->adldap);

            return $collection;
        }

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
