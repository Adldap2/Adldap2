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
        return $this->search()->findBy(ActiveDirectory::COMMON_NAME, $name, $fields);
    }

    /**
     * Returns all contacts.
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
     * Creates a new search limited to contacts only.
     *
     * @return \Adldap\Query\Builder
     */
    public function search()
    {
        return $this->getAdldap()
            ->search()
            ->whereEquals(ActiveDirectory::OBJECT_CLASS, ActiveDirectory::CONTACT);
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
        return (new User($attributes, $this->search()))
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
