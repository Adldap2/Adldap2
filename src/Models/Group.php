<?php

namespace Adldap\Models;

use Adldap\Schemas\ActiveDirectory;
use Adldap\Models\Traits\HasDescriptionTrait;
use Adldap\Models\Traits\HasMemberOfTrait;

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
     * @param array $entries
     *
     * @return bool
     */
    public function setMembers(array $entries)
    {
        $this->setModification(ActiveDirectory::MEMBER, LDAP_MODIFY_BATCH_REPLACE, $entries);

        return $this->save();
    }

    /**
     * Adds an entry to the current group.
     *
     * @param string|Entry $entry
     *
     * @return bool
     */
    public function addMember($entry)
    {
        if($entry instanceof Entry) {
            $entry = $entry->getDn();
        }

        $this->setModification(ActiveDirectory::MEMBER, LDAP_MODIFY_BATCH_ADD, $entry);

        return $this->save();
    }

    /**
     * Removes an entry from the current group.
     *
     * @param string|Entry $entry
     *
     * @return bool
     */
    public function removeMember($entry)
    {
        if($entry instanceof Entry) {
            $entry = $entry->getDn();
        }

        $this->setModification(ActiveDirectory::MEMBER, LDAP_MODIFY_BATCH_REMOVE, $entry);

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
