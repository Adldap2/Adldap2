<?php

namespace Adldap\Classes;

/**
 * The AdldapQueryable class. This class provides
 * some standard methods to queryable classes that
 * extend this class.
 *
 * A 'queryable' class means that any Class that extends
 * this class must query and return information from
 * LDAP based on it's object class property.
 *
 * Class AdldapQueryable
 */
abstract class AbstractAdldapQueryable extends AbstractAdldapBase
{
    /**
     * The LDAP objects class name.
     *
     * @var string
     */
    public $objectClass = '';

    /**
     * Returns all entries with the current object class.
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
     * Finds a single entry using the objects current class
     * and the specified common name. If fields are specified,
     * then only those fields are returned in the result array.
     *
     * @param string $name
     * @param array  $fields
     *
     * @return array|bool
     */
    public function find($name, $fields = [])
    {
        $results = $this->adldap->search()
            ->select($fields)
            ->where('objectClass', '=', $this->objectClass)
            ->where('anr', '=', $name)
            ->first();

        if (count($results) > 0) {
            return $results;
        }

        return false;
    }

    /**
     * Returns the DN of the current object class.
     *
     * @param string $name
     *
     * @return string|bool
     */
    public function dn($name)
    {
        $info = $this->find($name);

        if (is_array($info) && array_key_exists('dn', $info)) {
            return $info['dn'];
        }

        return false;
    }

    /**
     * Delete a distinguished name from Active Directory.
     *
     * @param string $dn The distinguished name to delete
     *
     * @return bool
     */
    public function delete($dn)
    {
        $this->adldap->utilities()->validateNotNullOrEmpty('Distinguished Name [dn]', $dn);

        return $this->connection->delete($dn);
    }

    /**
     * Alias for the find() method.
     *
     * @param string $name   The name of the computer
     * @param array  $fields Attributes to return
     *
     * @return array|bool
     */
    public function info($name, $fields = [])
    {
        return $this->find($name, $fields);
    }

    /**
     * Returns true / false if the specified group exists
     * on the found LDAP entry.
     *
     * @param string $name
     * @param string $group
     * @param null   $recursive
     *
     * @return bool
     */
    public function inGroup($name, $group, $recursive = null)
    {
        if ($recursive === null) {
            $recursive = $this->adldap->getRecursiveGroups();
        }

        // Get a list of the groups
        $groups = $this->groups($name, $recursive);

        // Return true if the specified group is in the group list
        if (is_array($groups) && in_array($group, $groups)) {
            return true;
        }

        return false;
    }

    /**
     * Finds the LDAP entry with the specified name, and returns
     * the groups that it is a member of.
     *
     * @param string $name
     * @param null   $recursive
     *
     * @return array|bool
     */
    public function groups($name, $recursive = null)
    {
        if ($recursive === null) {
            $recursive = $this->adldap->getRecursiveGroups();
        }

        $info = $this->find($name);

        if (is_array($info) && array_key_exists('memberof', $info)) {
            $groups = $this->adldap->utilities()->niceNames($info['memberof']);

            if ($recursive === true) {
                foreach ($groups as $id => $groupName) {
                    $extraGroups = $this->adldap->group()->recursiveGroups($groupName);

                    $groups = array_merge($groups, $extraGroups);
                }
            }

            /*
             * We'll return a filtered array and
             * make sure every entry is unique
             */
            return array_unique($groups);
        }

        return false;
    }
}
