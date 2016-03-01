<?php

namespace Adldap\Models;

use Adldap\Models\Traits\HasDescriptionTrait;
use Adldap\Models\Traits\HasMemberOfTrait;
use Adldap\Objects\BatchModification;
use Adldap\Schemas\ActiveDirectory;

class Group extends Entry
{
    use HasDescriptionTrait, HasMemberOfTrait;

    /**
     * Returns all users apart of the current group.
     *
     * https://msdn.microsoft.com/en-us/library/ms677097(v=vs.85).aspx
     *
     * @return array
     */
    public function getMembers()
    {
        $members = [];

        $dns = $this->getAttribute(ActiveDirectory::MEMBER);

        if (is_array($dns)) {
            unset($dns['count']);

            foreach ($dns as $dn) {
                $query = $this->query->newInstance();

                $member = $query->findByDn($dn);

                if ($member instanceof AbstractModel) {
                    $members[] = $member;
                }
            }
        }

        return $members;
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
        $this->setAttribute(ActiveDirectory::MEMBER, $entries);

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
        if ($entry instanceof Entry) {
            $entry = $entry->getDn();
        }

        $modification = new BatchModification();
        $modification->setAttribute(ActiveDirectory::MEMBER);
        $modification->setType(LDAP_MODIFY_BATCH_ADD);
        $modification->setValues([$entry]);

        $this->addModification($modification);

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
        if ($entry instanceof Entry) {
            $entry = $entry->getDn();
        }

        $modification = new BatchModification();
        $modification->setAttribute(ActiveDirectory::MEMBER);
        $modification->setType(LDAP_MODIFY_BATCH_REMOVE);
        $modification->setValues([$entry]);

        $this->addModification($modification);

        return $this->save();
    }

    /**
     * Removes all members from the current group.
     *
     * @return bool
     */
    public function removeMembers()
    {
        $modification = new BatchModification();
        $modification->setAttribute(ActiveDirectory::MEMBER);
        $modification->setType(LDAP_MODIFY_BATCH_REMOVE_ALL);

        $this->addModification($modification);

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
