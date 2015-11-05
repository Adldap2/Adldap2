<?php

namespace Adldap\Scopes;

use Adldap\Models\Computer;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Schemas\Schema;

class Computers extends AbstractScope implements QueryableInterface, CreateableInterface
{
    /**
     * Finds a computer.
     *
     * @param string $name
     * @param array  $fields
     *
     * @return bool|\Adldap\Models\Computer
     */
    public function find($name, $fields = [])
    {
        return $this->search()->select($fields)->find($name);
    }

    /**
     * Returns all computers.
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
     * Creates a new search limited to computers only.
     *
     * @return \Adldap\Query\Builder
     */
    public function search()
    {
        $schema = Schema::get();

        return $this->getManager()
            ->search()
            ->whereEquals($schema->objectCategory(), $schema->objectCategoryComputer());
    }

    /**
     * Returns a new Computer instance.
     *
     * @param array $attributes
     *
     * @return Computer
     */
    public function newInstance(array $attributes = [])
    {
        $schema = Schema::get();

        return (new Computer($attributes, $this->search()))
            ->setAttribute($schema->objectClass(), [
                $schema->top(),
                $schema->person(),
                $schema->organizationalPerson(),
                $schema->user(),
                $schema->computer(),
            ]);
    }

    /**
     * Creates a new Computer and returns the result.
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
