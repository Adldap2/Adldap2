<?php

namespace Adldap\Classes;

use Adldap\Models\User;
use Adldap\Schemas\ActiveDirectory;

class Contacts extends AbstractQueryable
{
    /**
     * The contacts object class name.
     *
     * @var string
     */
    public $objectClass = 'contact';

    /**
     * Returns a new User instance.
     *
     * @param array $attributes
     *
     * @return User
     */
    public function newInstance(array $attributes = [])
    {
        return (new User($attributes, $this->connection))
            ->setAttribute(ActiveDirectory::OBJECT_CLASS, [
                ActiveDirectory::TOP,
                ActiveDirectory::PERSON,
                ActiveDirectory::ORGANIZATIONAL_PERSON,
                ActiveDirectory::CONTACT,
            ]);
    }

    /**
     * Creates a new contact and returns the result.
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
