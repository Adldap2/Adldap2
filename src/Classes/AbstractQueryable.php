<?php

namespace Adldap\Classes;
use Adldap\Schemas\ActiveDirectory;

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
abstract class AbstractQueryable extends AbstractBase
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
            ->where(ActiveDirectory::OBJECT_CLASS, '=', $this->objectClass);

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
            ->where(ActiveDirectory::OBJECT_CLASS, '=', $this->objectClass)
            ->where(ActiveDirectory::ANR, '=', $name)
            ->first();

        if (count($results) > 0) {
            return $results;
        }

        return false;
    }
}
