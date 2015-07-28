<?php

namespace Adldap\Classes;

use Adldap\Models\Computer;
use Adldap\Schemas\ActiveDirectory;

class Computers extends AbstractQueryable
{
    /**
     * The computers object class name.
     *
     * @var string
     */
    public $objectClass = 'computer';

    /**
     * Returns a new Computer instance.
     *
     * @param array $attributes
     *
     * @return Computer
     */
    public function newInstance(array $attributes = [])
    {
        return (new Computer($attributes, $this->connection))
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
