<?php

namespace Adldap\Classes;

use Adldap\Models\Entry;
use Adldap\Models\Group;
use Adldap\Models\User;
use Adldap\Schemas\ActiveDirectory;

class Groups extends AbstractBase implements QueryableInterface, CreateableInterface
{
    /**
     * Finds a group.
     *
     * @param string $name
     * @param array  $fields
     *
     * @return bool|\Adldap\Models\Group
     */
    public function find($name, $fields = [])
    {
        return $this->search()->findBy(ActiveDirectory::COMMON_NAME, $name, $fields);
    }

    /**
     * Returns all groups.
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
            ->whereEquals(ActiveDirectory::OBJECT_CATEGORY, ActiveDirectory::OBJECT_CATEGORY_GROUP);
    }

    /**
     * Creates a new Group instance.
     *
     * @param array $attributes
     *
     * @return Group
     */
    public function newInstance(array $attributes = [])
    {
        return (new Group($attributes, $this->search()))
            ->setAttribute(ActiveDirectory::OBJECT_CLASS, [
                ActiveDirectory::TOP,
                ActiveDirectory::OBJECT_CATEGORY_GROUP,
            ]);
    }

    /**
     * Creates and saves a new Group instance, then returns the result.
     *
     * @param array $attributes
     *
     * @return bool
     */
    public function create(array $attributes = [])
    {
        return $this->newInstance($attributes)->save();
    }

    /**
     * Coping with AD not returning the primary group.
     *
     * http://support.microsoft.com/?kbid=321360.
     *
     * @param string $group The name of the group
     * @param string $user  The username of the user
     *
     * @return bool
     */
    public function getPrimaryGroup($group, $user)
    {
        $group = $this->find($group);

        $user = $this->getAdldap()->users()->find($user);

        if ($group instanceof Group && $user instanceof User) {
            $sid = Utilities::binarySidToText($group->getSid());

            $result = $this->getAdldap()->search()
                    ->where(ActiveDirectory::OBJECT_SID, '=', $sid)
                    ->first();

            if ($result instanceof Entry) {
                return $result->getDn();
            }
        }

        return false;
    }
}
