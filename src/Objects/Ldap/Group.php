<?php

namespace Adldap\Objects\Ldap;

use Adldap\Schemas\ActiveDirectory;
use Adldap\Objects\Traits\HasDescriptionTrait;
use Adldap\Objects\Traits\HasMemberOfTrait;

class Group extends Entry
{
    use HasDescriptionTrait;

    use HasMemberOfTrait;

    /**
     * Returns the user DNs of all users inside the group.
     *
     * https://msdn.microsoft.com/en-us/library/ms677097(v=vs.85).aspx
     *
     * @return array
     */
    public function getMembers()
    {
        return $this->getAttribute(ActiveDirectory::MEMBER);
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
        return $this->getAttribute(ActiveDirectory::GROUP_TYPE, 0);
    }
}
