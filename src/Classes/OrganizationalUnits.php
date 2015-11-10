<?php

namespace Adldap\Classes;

use Adldap\Models\OrganizationalUnit;
use Adldap\Schemas\ActiveDirectory;

class OrganizationalUnits extends AbstractBase implements QueryableInterface, CreateableInterface
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
        return $this->search()->findBy(ActiveDirectory::ORGANIZATIONAL_UNIT_SHORT, $name, $fields);
    }

    /**
     * Returns all organizational units.
     *
     * @param array     $fields
     * @param bool|true $sorted
     * @param string    $sortBy
     * @param string    $sortDirection
     *
     * @return array|bool
     */
    public function all($fields = [], $sorted = true, $sortBy = ActiveDirectory::COMMON_NAME, $sortDirection = 'asc')
    {
        $search = $this->search()->select($fields);

        if ($sorted) {
            $search->sortBy($sortBy, $sortDirection);
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

    /**
     * Creates a new Organizational Unit instance.
     *
     * @param array $attributes
     *
     * @return OrganizationalUnit
     */
    public function newInstance(array $attributes = [])
    {
        return (new OrganizationalUnit($attributes, $this->search()))
            ->setAttribute(ActiveDirectory::OBJECT_CLASS, [
                ActiveDirectory::TOP,
                ActiveDirectory::ORGANIZATIONAL_UNIT_LONG,
            ]);
    }

    /**
     * Creates and saves a new Organizational Unit instance, then returns the result.
     *
     * @param array $attributes
     *
     * @return bool
     */
    public function create(array $attributes = [])
    {
        return $this->newInstance($attributes)->save();
    }
}
