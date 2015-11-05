<?php

namespace Adldap\Scopes;

use Adldap\Models\OrganizationalUnit;
use Adldap\Schemas\Schema;
use Adldap\Schemas\ActiveDirectory;

class OrganizationalUnits extends AbstractScope implements QueryableInterface, CreateableInterface
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
     * @param string    $sortDirection
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|bool
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
        $schema = Schema::get();

        return $this->getManager()
            ->search()
            ->whereEquals($schema->objectCategory(), $schema->organizationalUnit());
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
        $schema = Schema::get();

        return (new OrganizationalUnit($attributes, $this->search()))
            ->setAttribute($schema->objectClass(), [
                $schema->top(),
                $schema->organizationalUnit(),
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
