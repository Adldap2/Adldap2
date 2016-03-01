<?php

namespace Adldap\Models\Traits;

use Adldap\Classes\Utilities;
use Adldap\Models\AbstractModel;
use Adldap\Models\Group;
use Adldap\Schemas\ActiveDirectory;

trait HasMemberOfTrait
{
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
            $query = $this->query->newInstance();

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
            $query = $this->query->newInstance();

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
     * {@inheritdoc}
     */
    public function getGroups($fields = [], $recursive = false)
    {
        $groups = $this->getMemberOf($fields);

        if ($recursive === true) {
            foreach ($groups as $group) {
                $groups = array_merge($groups, $group->getGroups($fields, $recursive));
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
     * @return array
     */
    public function getMemberOf($fields = [])
    {
        $groups = [];

        $dns = $this->getAttribute(ActiveDirectory::MEMBER_OF);

        if (is_array($dns)) {
            foreach ($dns as $key => $dn) {
                $query = $this->query->newInstance();

                $groups[] = $query
                    ->select($fields)
                    ->findByDn($dn);
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

        $dns = $this->getAttribute(ActiveDirectory::MEMBER_OF);

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

        if ($group instanceof Group) {
            if (in_array($group, $groups)) {
                return true;
            }
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
