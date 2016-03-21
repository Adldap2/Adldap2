<?php

namespace Adldap\Models\Traits;

use Adldap\Models\AbstractModel;
use Adldap\Models\Group;
use Adldap\Utilities;

trait HasMemberOfTrait
{
    use ModelTrait;

    /**
     * Adds the current model to the specified group.
     *
     * @param string|Group $group
     *
     * @return bool
     */
    public function addGroup($group)
    {
        if (is_string($group)) {
            // If the group is a string, we'll assume the dev is passing
            // in a DN string of the group. We'll try to locate it.
            $query = $this->getQuery()->newInstance();

            $group = $query->findByDn($group);
        }

        if ($group instanceof Group) {
            // If the group is Group model instance, we can
            // add the current models DN to the group.
            return $group->addMember($this->getDn());
        }

        return false;
    }

    /**
     * Removes the current model from the specified group.
     *
     * @param string|Group $group
     *
     * @return bool
     */
    public function removeGroup($group)
    {
        if (is_string($group)) {
            // If the group is a string, we'll assume the dev is passing
            // in a DN string of the group. We'll try to locate it.
            $query = $this->getQuery()->newInstance();

            $group = $query->findByDn($group);
        }

        if ($group instanceof Group) {
            // If the group is Group model instance, we can
            // remove the current models DN from the group.
            return $group->removeMember($this->getDn());
        }

        return false;
    }

    /**
     * Returns the models groups.
     *
     * @param array $fields
     * @param bool  $recursive
     *
     * @return \Illuminate\Support\Collection
     */
    public function getGroups($fields = [], $recursive = false)
    {
        $groups = $this->getMemberOf($fields);

        if ($recursive === true) {
            foreach ($groups as $group) {
                $groups = $groups->merge($group->getGroups($fields, $recursive));
            }
        }

        return $groups;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupNames()
    {
        return $this->getMemberOfNames();
    }

    /**
     * Returns an array of groups the model is a member of.
     *
     * https://msdn.microsoft.com/en-us/library/ms677099(v=vs.85).aspx
     *
     * @param array $fields
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMemberOf($fields = [])
    {
        $groups = $this->getQuery()->newCollection();

        $dns = $this->getAttribute($this->getSchema()->memberOf());

        $query = $this->getQuery()->newInstance();

        if (is_array($dns)) {
            foreach ($dns as $key => $dn) {
                $group = $query->select($fields)->findByDn($dn);

                if ($group instanceof AbstractModel) {
                    $groups->push($group);
                }
            }
        }

        return $groups;
    }

    /**
     * Returns the models memberOf names only.
     *
     * @return array
     */
    public function getMemberOfNames()
    {
        $names = [];

        $dns = $this->getAttribute($this->getSchema()->memberOf());

        if (is_array($dns)) {
            foreach ($dns as $dn) {
                $exploded = Utilities::explodeDn($dn);

                if (array_key_exists(0, $exploded)) {
                    $names[] = $exploded[0];
                }
            }
        }

        return $names;
    }

    /**
     * Returns true / false if the current model
     * is in the specified group.
     *
     * @param string|Group $group
     * @param bool         $recursive
     *
     * @return bool
     */
    public function inGroup($group, $recursive = false)
    {
        $groups = $this->getGroups([], $recursive);

        if ($group instanceof Group && $groups->contains($group)) {
            return true;
        } elseif (is_string($group)) {
            foreach ($groups as $model) {
                if ($model instanceof AbstractModel && $group == $model->getName()) {
                    return true;
                }
            }
        }

        return false;
    }
}
