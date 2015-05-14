<?php

namespace Adldap\Classes;

/**
 * Ldap Computer Management.
 *
 * Class AdldapComputers
 */
class AdldapComputers extends AbstractAdldapQueryable
{
    /**
     * The computers object class name.
     *
     * @var string
     */
    public $objectClass = 'computer';

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

        if (is_array($info) && array_key_exists('memberof', $info)) {
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
