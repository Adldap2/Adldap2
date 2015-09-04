<?php

namespace Adldap\Models\Traits;

use Adldap\Models\Group;
use Adldap\Schemas\ActiveDirectory;

trait HasMemberOfTrait
{
    /**
     * {@inheritdoc}
     */
    public function getGroups()
    {
        return $this->getMemberOf();
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
     * @return array
     */
    public function getMemberOf()
    {
        $groups = [];

        $dns = $this->getAttribute(ActiveDirectory::MEMBER_OF);

        if (is_array($dns)) {
            unset($dns['count']);

            foreach ($dns as $key => $dn) {
                $query = $this->query->newInstance();

                $groups[] = $query->search()->findByDn($dn);
            }
        }

        return $groups;
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
