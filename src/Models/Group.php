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
     * @return \Illuminate\Support\Collection
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
     * Retrieves group members by the specified model
     * attribute using their distinguished name.
     *
     * @param $attribute
     *
     * @return array
     */
    protected function getMembersFromAttribute($attribute)
    {
        $members = [];

        $dns = $this->getAttribute($attribute) ?: [];

        unset($dns['count']);

        foreach ($dns as $dn) {
            $member = $this->query->newInstance()->findByDn($dn);

            if ($member instanceof Model) {
                $members[] = $member;
            }
        }

        return $members;
    }

    /**
     * Checks attributes for range limited member list.
     *
     * @return array
     */
    public function loadPaginatedMember()
    {
        $members = [];

        $keys = array_keys($this->attributes);

        // We need to filter out the model attributes so
        // we only retrieve the member range.
        $attributes = array_values(array_filter($keys, function ($key) {
            return strpos($key,'member;range') !== false;
        }));

        // We'll grab the member range key so we can run a
        // regex on it to determine the range.
        $key = reset($attributes);

        preg_match_all(
            '/member;range\=([0-9]{1,4})-([0-9*]{1,4})/',
            $key,
            $matches
        );

        if ($key && count($matches) == 3) {
            $to = $matches[2][0];

            $members = $this->getMembersFromAttribute($key);

            // If the query already included all member results (indicated
            // by the '*'), then we can return here. Otherwise we need
            // to continue on and retrieve the rest.
            if($to === '*') {
                return $members;
            }

            $group = $this->query->newInstance()->findByDn(
                $this->getDn(),
                [$this->query->getSchema()->memberRange($to + 1, '*')]
            );

            $members = array_merge($members, $group->getMembers()->toArray());
        }

        return $members;
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
     * @return $this
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
