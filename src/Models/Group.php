<?php

namespace Adldap\Models;

use InvalidArgumentException;
use Adldap\Utilities;
use Adldap\Objects\BatchModification;
use Adldap\Models\Traits\HasMemberOfTrait;
use Adldap\Models\Traits\HasDescriptionTrait;

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
        $members = $this->getMembersFromAttribute($this->schema->member());

        if(count($members) === 0){
            $members = $this->loadPaginatedMember();
        }

        return $this->newCollection($members);
    }

    /**
     * @param $attribute
     * @return array
     */
    protected function getMembersFromAttribute($attribute)
    {
        $members = [];

        $dns = $this->getAttribute($attribute);

        if (is_array($dns)) {
            unset($dns['count']);

            foreach ($dns as $dn) {
                $member = $this->query->newInstance()->findByDn($dn);

                if ($member instanceof Model) {
                    $members[] = $member;
                }
            }
        }

        return $members;
    }

    /**
     * Checks Attributes for range limited memberlist
     * @return array
     */
    public function loadPaginatedMember()
    {
        $members = null;

        $keys = array_keys($this->attributes);

        foreach($keys as $key) {
            if(strpos($key,'member;range')!==false) {
                $matches = [];

                preg_match_all(
                    '/member;range\=([0-9]{1,4})-([0-9*]{1,4})/',
                    $key,
                    $matches
                );

                if(count($matches) == 3) {
                    $to = $matches[2][0];

                    $members = $this->newCollection($this->getMembersFromAttribute($key));

                    if($to === '*')
                        break;

                    /** @var Group $group */
                    $group = $this->query->findByDn($this->getDn(),[$this->query->getSchema()->memberRange($to + 1, '*')]);

                    $members = $members->merge($group->getMembers());

                    break;
                }

            }
        }

        return $members === null ? [] : $members;
    }

    /**
     * Returns the group's member names only.
     *
     * @return array
     */
    public function getMemberNames()
    {
        $members = [];

        $dns = $this->getAttribute($this->schema->member());

        if (is_array($dns)) {
            foreach ($dns as $dn) {
                $exploded = Utilities::explodeDn($dn);

                if (array_key_exists(0, $exploded)) {
                    $members[] = $exploded[0];
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
        $this->setAttribute($this->schema->member(), $entries);

        return $this;
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
        $entry = ($entry instanceof Model ? $entry->getDn() : $entry);

        if (is_null($entry)) {
            throw new InvalidArgumentException(
                'Cannot add member to group. The members distinguished name cannot be null.'
            );
        }

        $this->addModification(new BatchModification(
            $this->schema->member(),
            LDAP_MODIFY_BATCH_ADD,
            [$entry]
        ));

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
        $entry = ($entry instanceof Model ? $entry->getDn() : $entry);

        if (is_null($entry)) {
            throw new InvalidArgumentException(
                'Cannot add member to group. The members distinguished name cannot be null.'
            );
        }

        $this->addModification(new BatchModification(
            $this->schema->member(),
            LDAP_MODIFY_BATCH_REMOVE,
            [$entry]
        ));

        return $this->save();
    }

    /**
     * Removes all members from the current group.
     *
     * @return bool
     */
    public function removeMembers()
    {
        $this->addModification(new BatchModification(
            $this->schema->member(),
            LDAP_MODIFY_BATCH_REMOVE_ALL
        ));

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
        return $this->getFirstAttribute($this->schema->groupType());
    }



}
