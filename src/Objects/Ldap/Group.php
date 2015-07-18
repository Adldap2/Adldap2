<?php

namespace Adldap\Objects\Ldap;

use Adldap\Objects\Traits\HasDescriptionTrait;

class Group extends Entry
{
    use HasDescriptionTrait;

    /**
     * Returns the user DNs of all users inside the group.
     *
     * https://msdn.microsoft.com/en-us/library/ms677097(v=vs.85).aspx
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
     * https://msdn.microsoft.com/en-us/library/ms677099(v=vs.85).aspx
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
     * https://msdn.microsoft.com/en-us/library/ms675935(v=vs.85).aspx
     *
     * @return string
     */
    public function getGroupType()
    {
        return $this->getAttribute('grouptype', 0);
    }
}
