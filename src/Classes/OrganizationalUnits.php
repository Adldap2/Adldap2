<?php

namespace Adldap\Classes;

use Adldap\Schemas\ActiveDirectory;

class OrganizationalUnits extends AbstractBase implements QueryableInterface
{
    /**
     * Finds an organizational unit.
     *
     * @param string $name
     * @param array  $fields
     *
     * @return bool|\Adldap\Models\OrganizationalUnit
     */
    public function find($name, $fields = [])
    {
        return $this->search()->select($fields)->find($name);
    }

    /**
     * Returns all organizational units.
     *
     * @param array     $fields
     * @param bool|true $sorted
     * @param string    $sortBy
     *
     * @return array|bool
     */
    public function all($fields = [], $sorted = true, $sortBy = 'cn')
    {
        $search = $this->search()->select($fields);

        if ($sorted) {
            $search->sortBy($sortBy);
        }

        return $search->get();
    }

    /**
     * Returns a new search limited to organizational units.
     *
     * @return \Adldap\Query\Builder
     */
    public function search()
    {
        return $this->getAdldap()
            ->search()
            ->whereEquals(ActiveDirectory::OBJECT_CATEGORY, ActiveDirectory::ORGANIZATIONAL_UNIT_LONG);
    }
}
