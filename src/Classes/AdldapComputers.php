<?php

namespace Adldap\Classes;

use Adldap\Collections\AdldapComputerCollection;

/**
 * Ldap Computer Management.
 *
 * Class AdldapComputers
 */
class AdldapComputers extends AdldapBase
{
    /**
     * The computers object class name.
     *
     * @var string
     */
    public $objectClass = 'computer';

    /**
     * Returns all computers from the current connection.
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
        $search = $this->adldap->search()
            ->select($fields)
            ->where('objectClass', '=', $this->objectClass);

        if ($sorted) {
            $search->sortBy($sortBy, $sortByDirection);
        }

        return $search->get();
    }

    /**
     * Finds a computer using the current connection.
     *
     * @param string $computer
     * @param array  $fields
     *
     * @return array|bool
     */
    public function find($computer, $fields = [])
    {
        $results = $this->adldap->search()
            ->select($fields)
            ->where('objectClass', '=', $this->objectClass)
            ->where('cn', '=', $computer)
            ->first();

        if(count($results) > 0)
        {
            return $results;
        }

        return false;
    }

    /**
     * Get information about a specific computer. Returned in a raw array format from AD.
     *
     * @param string $computerName The name of the computer
     * @param array  $fields       Attributes to return
     *
     * @return array|bool
     */
    public function info($computerName, $fields = [])
    {
        return $this->find($computerName, $fields);
    }

    /**
     * Find information about the computers. Returned in a raw array format from AD.
     *
     * @param string $computerName The name of the computer
     * @param array  $fields       Array of parameters to query
     *
     * @return AdldapComputerCollection|bool
     * @depreciated
     */
    public function infoCollection($computerName, array $fields = [])
    {
        $info = $this->info($computerName, $fields);

        if ($info) {
            return new AdldapComputerCollection($info, $this->adldap);
        }

        return false;
    }

    /**
     * Check if a computer is in a group.
     *
     * @param string $computerName The name of the computer
     * @param string $group        The group to check
     * @param null   $recursive    Whether to check recursively
     *
     * @return bool
     */
    public function inGroup($computerName, $group, $recursive = null)
    {
        if ($recursive === null) {
            $recursive = $this->adldap->getRecursiveGroups();
        } // Use the default option if they haven't set it

        // Get a list of the groups
        $groups = $this->groups($computerName, ['memberof'], $recursive);

        // Return true if the specified group is in the group list
        if (in_array($group, $groups)) {
            return true;
        }

        return false;
    }

    /**
     * Get the groups a computer is in.
     *
     * @param string $computerName The name of the computer
     * @param null   $recursive    Whether to check recursively
     *
     * @return array|bool
     */
    public function groups($computerName, $recursive = null)
    {
        // Use the default option if they haven't set it
        if ($recursive === null) {
            $recursive = $this->adldap->getRecursiveGroups();
        }

        // Search the directory for their information
        $info = $this->info($computerName, ['memberof', 'primarygroupid']);

        // Presuming the entry returned is our guy (unique usernames)
        $groups = $this->adldap->utilities()->niceNames($info['memberof']);

        if ($recursive === true) {
            foreach ($groups as $id => $groupName) {
                $extraGroups = $this->adldap->group()->recursiveGroups($groupName);

                $groups = array_merge($groups, $extraGroups);
            }
        }

        return $groups;
    }
}
