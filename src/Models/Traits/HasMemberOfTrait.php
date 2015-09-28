<?php

namespace Adldap\Models\Traits;

use Adldap\Classes\Utilities;
use Adldap\Models\Group;
use Adldap\Schemas\ActiveDirectory;

trait HasMemberOfTrait
{
    /**
     * {@inheritdoc}
     */
    public function getGroups($fields = [])
    {
        return $this->getMemberOf($fields);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupNames()
    {
        return $this->getMemberOfNames();
    }

    /**
     * {@inheritdoc}
     */
    public function setGroups(array $groups)
    {
        return $this->setMemberOf($groups);
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
     * Sets the models's group DN's the entry is a member of.
     *
     * @param array $groups
     *
     * @return \Adldap\Models\Entry
     */
    public function setMemberOf(array $groups)
    {
        return $this->setAttribute(ActiveDirectory::MEMBER_OF, $groups);
    }

    /**
     * Returns true / false if the current model
     * is in the specified group.
     *
     * @param string|Group $group
     *
     * @return bool
     */
    public function inGroup($group)
    {
        $groups = $this->getGroups();

        if ($group instanceof Group) {
            if (in_array($group, $groups)) {
                return true;
            }
        } elseif (is_string($group)) {
            foreach ($groups as $model) {
                if ($group == $model->getName()) {
                    return true;
                }
            }
        }

        return false;
    }
}
