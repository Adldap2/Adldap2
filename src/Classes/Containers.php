<?php

namespace Adldap\Classes;

use Adldap\Models\Container;
use Adldap\Schemas\ActiveDirectory;

class Containers extends AbstractBase implements QueryableInterface, CreateableInterface
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
     * @param string $sortByDirection
     *
     * @return array|bool
     */
    public function all($fields = [], $sorted = true, $sortBy = 'name', $sortByDirection = 'asc')
    {
        $search = $this->search();

        if ($sorted) {
            $search->sortBy($sortBy, $sortByDirection);
        }

        return $search->get();
    }

    /**
     * Creates a new search limited to containers only.
     *
     * @return Search
     */
    public function search()
    {
        return $this->getAdldap()
            ->search()
            ->where(ActiveDirectory::OBJECT_CATEGORY, '=', ActiveDirectory::OBJECT_CATEGORY_CONTAINER);
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
        return (new Container($attributes, $this->getAdldap()))
            ->setAttribute(ActiveDirectory::OBJECT_CLASS, ActiveDirectory::ORGANIZATIONAL_UNIT_LONG);
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
