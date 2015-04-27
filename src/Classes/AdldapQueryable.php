<?php

namespace Adldap\Classes;

/**
 * The AdldapQueryable class. This class provides
 * some standard methods to queryable classes that
 * extend this class.
 *
 * Class AdldapQueryable
 * @package Adldap\Classes
 */
class AdldapQueryable extends AdldapBase
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
     * @param array $fields
     * @param bool $sorted
     * @param string $sortBy
     * @param string $sortByDirection
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
     * and the specified common name.
     *
     * @param string $name
     * @param array $fields
     * @return array|bool
     */
    public function find($name, $fields = [])
    {
        $results = $this->adldap->search()
            ->select($fields)
            ->where('objectClass', '=', $this->objectClass)
            ->where('anr', '=', $name)
            ->first();

        if(count($results) > 0) {
            return $results;
        }

        return false;
    }

    /**
     * Returns the DN of the current object class.
     *
     * @param string $name
     * @return bool
     */
    public function dn($name)
    {
        $info = $this->find($name);

        if(is_array($info) && array_key_exists('dn', $info)) {
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
        $this->adldap->utilities()->validateNotNull('Distinguished Name [dn]', $dn);

        return $this->connection->delete($dn);
    }

    /**
     * Get information about a specific computer. Returned in a raw array format from AD.
     *
     * @param string $computerName The name of the computer
     * @param array  $fields       Attributes to return
     *
     * @return array|bool
     */
    public function info($name, $fields = [])
    {
        return $this->find($name, $fields);
    }
}
