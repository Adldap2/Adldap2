<?php

namespace Adldap\Classes;

use Adldap\Models\User;
use Adldap\Schemas\ActiveDirectory;

class Contacts extends AbstractBase implements QueryableInterface, CreateableInterface
{
    /**
     * Finds a contact.
     *
     * @param string $name
     * @param array  $fields
     *
     * @return bool|\Adldap\Models\User
     */
    public function find($name, $fields = [])
    {
        return $this->search()->select($fields)->find($name);
    }

    /**
     * Returns all contacts.
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
     * Creates a new search limited to contacts only.
     *
     * @return Search
     */
    public function search()
    {
        return $this->getAdldap()
            ->search()
            ->where(ActiveDirectory::OBJECT_CLASS, '=', ActiveDirectory::CONTACT);
    }

    /**
     * Returns a new User instance.
     *
     * @param array $attributes
     *
     * @return User
     */
    public function newInstance(array $attributes = [])
    {
        return (new User($attributes, $this->getAdldap()))
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
