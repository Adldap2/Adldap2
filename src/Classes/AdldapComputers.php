<?php

namespace Adldap\Classes;

use Adldap\Collections\AdldapComputerCollection;

/**
 * Ldap Computer Management.
 *
 * Class AdldapComputers
 */
class AdldapComputers extends AdldapQueryable
{
    /**
     * The computers object class name.
     *
     * @var string
     */
    public $objectClass = 'computer';

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
     * Get the groups a computer is in.
     *
     * @param string $computerName The name of the computer
     * @param null   $recursive    Whether to check recursively
     *
     * @return array|bool
     */
    public function groups($computerName, $recursive = null)
    {
        if ($recursive === null) {
            $recursive = $this->adldap->getRecursiveGroups();
        }

        $info = $this->find($computerName);

        if(is_array($info) && array_key_exists('memberof', $info)) {
            $groups = $this->adldap->utilities()->niceNames($info['memberof']);

            if ($recursive === true) {
                foreach ($groups as $id => $groupName) {
                    $extraGroups = $this->adldap->group()->recursiveGroups($groupName);

                    $groups = array_merge($groups, $extraGroups);
                }
            }

            return $groups;
        }

        return false;
    }
}
