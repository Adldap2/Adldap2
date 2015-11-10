<?php

namespace Adldap\Classes;

use Adldap\Models\Computer;
use Adldap\Schemas\ActiveDirectory;

class Computers extends AbstractBase implements QueryableInterface, CreateableInterface
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
        return $this->search()->findBy(ActiveDirectory::COMMON_NAME, $name, $fields);
    }

    /**
     * Returns all computers.
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
     * Creates a new search limited to computers only.
     *
     * @return \Adldap\Query\Builder
     */
    public function search()
    {
        return $this->getAdldap()
            ->search()
            ->whereEquals(ActiveDirectory::OBJECT_CATEGORY, ActiveDirectory::COMPUTER);
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
        return (new Computer($attributes, $this->search()))
            ->setAttribute(ActiveDirectory::OBJECT_CLASS, [
                ActiveDirectory::TOP,
                ActiveDirectory::PERSON,
                ActiveDirectory::ORGANIZATIONAL_PERSON,
                ActiveDirectory::USER,
                ActiveDirectory::COMPUTER,
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
