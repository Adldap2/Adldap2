<?php

namespace Adldap\Models\Traits;

use Adldap\Utilities;
use Adldap\Models\User;
use Adldap\Models\Group;
use Illuminate\Support\Collection;

trait HasMemberOf
{
    /**
     * Adds the current model to the specified group.
     *
     * @param string|Group $group
     *
     * @return bool
     */
    public function addGroup($group)
    {
        if (is_string($group)) {
            // If the group is a string, we'll assume the dev is passing
            // in a DN string of the group. We'll try to locate it.
            $group = $this->query->newInstance()->findByDn($group);
        }

        if ($group instanceof Group) {
            // If the group is Group model instance, we can
            // add the current models DN to the group.
            return $group->addMember($this->getDn());
        }

        return false;
    }

    /**
     * Removes the current model from the specified group.
     *
     * @param string|Group $group
     *
     * @return bool
     */
    public function removeGroup($group)
    {
        if (is_string($group)) {
            // If the group is a string, we'll assume the dev is passing
            // in a DN string of the group. We'll try to locate it.
            $group = $this->query->newInstance()->findByDn($group);
        }

        if ($group instanceof Group) {
            // If the group is Group model instance, we can
            // remove the current models DN from the group.
            return $group->removeMember($this->getDn());
        }

        return false;
    }

    /**
     * Returns the models groups that it is apart of.
     *
     * If a recursive option is given, groups of groups
     * are retrieved and then merged with
     * the resulting collection.
     *
     * https://msdn.microsoft.com/en-us/library/ms677099(v=vs.85).aspx
     *
     * @param array $fields
     * @param bool  $recursive
     * @param array $visited
     *
     * @return \Illuminate\Support\Collection
     */
    public function getGroups(array $fields = [], $recursive = false, array $visited = [])
    {
        if (!empty($fields) && !in_array($this->schema->memberOf(), $fields)) {
            // We want to make sure that we always select the memberof
            // field in case developers want recursive members.
            $fields = array_merge($fields, [$this->schema->memberOf()]);
        }

        // Get the members of the current group.
        $dns = $this->getAttribute($this->schema->memberOf());

        // Normalize returned distinguished names.
        $dns = is_array($dns) ? $dns : [];

        /** @var \Adldap\Query\Builder $query */
        $query = $this->query->newInstance();

        /** @var Collection $groups */
        $groups = $query->newCollection($dns)->map(function ($dn) use ($query, $fields) {
            return $query->select($fields)->findByDn($dn);
        })->filter(function ($group) {
            return $group instanceof Group;
        });

        // We need to check if we're working with a User model. Only users
        // contain a primary group. If we are, we'll merge the users
        // primary group into the resulting collection.
        if ($this instanceof User && $primary = $this->getPrimaryGroup()) {
            $groups->push($primary);
        }

        if ($recursive) {
            // If recursive results are requested, we'll ask each group
            // for their groups, and merge the resulting collection.
            foreach ($groups as $group) {
                // We need to validate that we haven't already queried
                // for this groups members so we don't allow
                // infinite recursion in case of circular
                // group dependencies in LDAP.
                if (!in_array($group->getDistinguishedName(), $visited)) {
                    $visited[] = $group->getDistinguishedName();

                    $members = $group->getGroups($fields, $recursive, $visited);

                    foreach ($members as $member) {
                        $visited[] = $member->getDistinguishedName();
                    }

                    $groups = $groups->merge($members);
                }
            }
        }

        return $groups;
    }

    /**
     * Returns the models groups names in a single dimension array.
     *
     * If a recursive option is given, groups of groups
     * are retrieved and then merged with
     * the resulting collection.
     *
     * @param bool $recursive
     *
     * @return array
     */
    public function getGroupNames($recursive = false)
    {
        $fields = [$this->schema->commonName(), $this->schema->memberOf()];

        $names = $this->getGroups($fields, $recursive)->map(function (Group $group) {
            return $group->getCommonName();
        })->toArray();

        return array_unique($names);
    }

    /**
     * Determine if the current model is a member of the specified group(s).
     *
     * @param mixed $group
     * @param bool  $recursive
     *
     * @return bool
     */
    public function inGroup($group, $recursive = false)
    {
        $memberOf = $this->getGroups([], $recursive);

        if ($group instanceof Collection) {
            // If we've been given a collection then we'll convert
            // it to an array to normalize the value.
            $group = $group->toArray();
        }

        $groups = is_array($group) ? $group : [$group];

        foreach ($groups as $group) {
            // We need to iterate through each given group that the
            // model must be apart of, then go through the models
            // actual groups and perform validation.
            $exists = $memberOf->filter(function (Group $parent) use ($group) {
                return $this->validateGroup($group, $parent);
            })->count() !== 0;

            if (!$exists) {
                // If the current group isn't at all contained
                // in the memberOf collection, we'll
                // return false here.
                return false;
            }
        }

        return true;
    }

    /**
     * Validates if the specified group is the given parent instance.
     *
     * @param Group|string $group
     * @param Group        $parent
     *
     * @return bool
     */
    protected function validateGroup($group, Group $parent)
    {
        if ($group instanceof Group) {
            // We've been given a group instance, we'll compare their DNs.
            return $parent->getDn() === $group->getDn();
        }

        if (Utilities::explodeDn($group)) {
            // We've been given a DN, we'll compare it to the parents.
            return $parent->getDn() === $group;
        }

        if (!empty($group)) {
            // We've been given just a string, we'll
            // compare it to the parents name.
            return $parent->getCommonName() === $group;
        }

        return false;
    }
}
