<?php

namespace Adldap\Scopes;

use Adldap\Models\Container;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Schemas\Schema;

class Containers extends AbstractScope implements QueryableInterface, CreateableInterface
{
    /**
     * Finds a container.
     *
     * @param string $name
     * @param array  $fields
     *
     * @return array|bool
     */
    public function find($name, $fields = [])
    {
        return $this->search()->select($fields)->find($name);
    }

    /**
     * Returns all containers.
     *
     * @param array  $fields
     * @param bool   $sorted
     * @param string $sortBy
     * @param string $sortDirection
     *
     * @return array|bool
     */
    public function all($fields = [], $sorted = true, $sortBy = ActiveDirectory::NAME, $sortDirection = 'asc')
    {
        $search = $this->search();

        if ($sorted) {
            $search->sortBy($sortBy, $sortDirection);
        }

        return $search->get();
    }

    /**
     * Creates a new search limited to containers only.
     *
     * @return \Adldap\Query\Builder
     */
    public function search()
    {
        $schema = Schema::get();

        return $this->getManager()
            ->search()
            ->whereEquals($schema->objectCategory(), $schema->objectCategoryContainer());
    }

    /**
     * Returns a new instance of a Container.
     *
     * @param array $attributes
     *
     * @return Container
     */
    public function newInstance(array $attributes = [])
    {
        $schema = Schema::get();

        return (new Container($attributes, $this->search()))
            ->setAttribute($schema->objectClass(), $schema->organizationalUnit());
    }

    /**
     * Creates a new Container and returns the result.
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
