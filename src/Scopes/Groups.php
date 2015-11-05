<?php

namespace Adldap\Scopes;

use Adldap\Models\Entry;
use Adldap\Models\Group;
use Adldap\Models\User;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Schemas\Schema;

class Groups extends AbstractScope implements QueryableInterface, CreateableInterface
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
        return $this->search()->select($fields)->find($name);
    }

    /**
     * Returns all groups.
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
     * Creates a new search limited to contacts only.
     *
     * @return \Adldap\Query\Builder
     */
    public function search()
    {
        $schema = Schema::get();

        return $this->getManager()
            ->search()
            ->whereEquals($schema->objectCategory(), $schema->objectCategoryGroup());
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
        $schema = Schema::get();

        return (new Group($attributes, $this->search()))
            ->setAttribute($schema->objectClass(), [
                $schema->top(),
                $schema->objectCategoryGroup(),
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

        $user = $this->getManager()->users()->find($user);

        if ($group instanceof Group && $user instanceof User) {
            $result = $this
                ->getManager()
                ->search()
                ->getQueryBuilder()
                ->findBySid($group->getSid());

            if ($result instanceof Entry) {
                return $result->getDn();
            }
        }

        return false;
    }
}
