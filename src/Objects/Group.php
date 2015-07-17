<?php

namespace Adldap\Objects;

use Adldap\Objects\Ldap\Entry;

class Group extends Entry
{
    /**
     * Returns the groups description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getAttribute('description', 0);
    }

    /**
     * Returns the user DNs of all users inside the group.
     *
     * @return array
     */
    public function getMembers()
    {
        return $this->getAttribute('member');
    }

    /**
     * Returns the parent groups DN.
     *
     * @return string
     */
    public function getMemberOf()
    {
        return $this->getAttribute('memberof', 0);
    }

    /**
     * Returns the group type integer.
     *
     * @return string
     */
    public function getGroupType()
    {
        return $this->getAttribute('grouptype', 0);
    }
}
