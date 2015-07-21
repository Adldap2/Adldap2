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
     * Sets the groups members using an array of user DNs.
     *
     * @param array $users
     *
     * @return bool
     */
    public function setMembers(array $users)
    {
        $this->setModification(ActiveDirectory::MEMBER, LDAP_MODIFY_BATCH_REPLACE, $users);

        return $this->save();
    }

    /**
     * Adds a user to the current group.
     *
     * @param string|User $user
     *
     * @return bool
     */
    public function addMember($user)
    {
        if($user instanceof User) {
            $user = $user->getDn();
        }

        $this->setModification(ActiveDirectory::MEMBER, LDAP_MODIFY_BATCH_ADD, $user);

        return $this->save();
    }

    /**
     * Removes a user from the current group.
     *
     * @param string|User $user
     *
     * @return bool
     */
    public function removeMember($user)
    {
        if($user instanceof User) {
            $user = $user->getDn();
        }

        $this->setModification(ActiveDirectory::MEMBER, LDAP_MODIFY_BATCH_REMOVE, $user);

        return $this->save();
    }

    /**
     * Removes all members from the current group.
     *
     * @return bool
     */
    public function removeMembers()
    {
        $this->setModification(ActiveDirectory::MEMBER, LDAP_MODIFY_BATCH_REMOVE_ALL, []);

        return $this->save();
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
