<?php

namespace Adldap\Classes;

use Adldap\Adldap;
use Adldap\Schemas\ActiveDirectory;

class Containers extends AbstractQueryable
{
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
    public function all($fields = [], $sorted = true, $sortBy = 'name', $sortByDirection = 'asc')
    {
        $search = $this->adldap->search()
            ->select($fields)
            ->where(ActiveDirectory::OBJECT_CATEGORY, '=', ActiveDirectory::OBJECT_CATEGORY_CONTAINER)
            ->where(ActiveDirectory::DISTINGUISHED_NAME, '!', $this->adldap->search()->getBaseDn());

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
     * @param array  $fields
     *
     * @return array|bool
     */
    public function find($name, $fields = [])
    {
        $results = $this->adldap->search()
            ->select($fields)
            ->where(ActiveDirectory::ORGANIZATIONAL_UNIT, '=', $name)
            ->first();

        if (count($results) > 0) {
            return $results;
        }

        return false;
    }

    /**
     * Create an organizational unit.
     *
     * @param array $attributes Default attributes of the ou
     *
     * @return bool|string
     */
    public function create(array $attributes)
    {
        $folder = new Folder($attributes);

        $folder->validateRequired();

        $folder->setAttribute('container', array_reverse($folder->getAttribute('container')));

        $add = [];

        $add['objectClass'] = 'organizationalUnit';
        $add['OU'] = $folder->getAttribute('ou_name');

        $containers = 'OU='.implode(',OU=', $folder->getAttribute('container'));

        $dn = 'OU='.$add['OU'].', '.$containers.$this->adldap->getBaseDn();

        return $this->connection->add($dn, $add);
    }
}
