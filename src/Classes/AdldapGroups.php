<?php

namespace Adldap\Classes;

use Adldap\Objects\Group;
use Adldap\Adldap;

/**
 * Ldap Group management.
 *
 * Class AdldapGroups
 */
class AdldapGroups extends AbstractAdldapQueryable
{
    /**
     * The groups object category string.
     *
     * @var string
     */
    public $objectCategory = 'group';

    /**
     * The groups object class string.
     *
     * @var string
     */
    public $objectClass = 'group';

    /**
     * Returns a complete list of the groups in AD based on a SAM Account Type.
     *
     * @param int   $sAMAaccountType The account type to return
     * @param array $select          The fields you want to retrieve for each
     * @param bool  $sorted          Whether to sort the results
     *
     * @return array|bool
     */
    public function search($sAMAaccountType = Adldap::ADLDAP_SECURITY_GLOBAL_GROUP, $select = [], $sorted = true)
    {
        $search = $this->adldap->search()
            ->select($select)
            ->where('objectCategory', '=', 'group');

        if ($sAMAaccountType !== null) {
            $search->where('samaccounttype', '=', $sAMAaccountType);
        }

        if ($sorted) {
            $search->sortBy('samaccountname', 'asc');
        }

        return $search->get();
    }

    /**
     * Add a group to a group.
     *
     * @param string $parent The parent group name
     * @param string $child  The child group name
     *
     * @return bool
     */
    public function addGroup($parent, $child)
    {
        // Find the parent group's dn
        $parentDn = $this->dn($parent);

        $childDn = $this->dn($child);

        if ($parentDn && $childDn) {
            $add['member'] = $childDn;

            // Add the child to the parent group and return the result
            return $this->connection->modAdd($parentDn, $add);
        }

        return false;
    }

    /**
     * Add a user to a group.
     *
     * @param string $groupName The group to add the user to
     * @param string $username  The user to add to the group
     *
     * @return bool
     */
    public function addUser($groupName, $username)
    {
        $groupDn = $this->dn($groupName);

        $userDn = $this->adldap->user()->dn($username);

        if ($groupDn && $userDn) {
            $add['member'] = $userDn;

            return $this->connection->modAdd($groupDn, $add);
        }

        return false;
    }

    /**
     * Add a contact to a group.
     *
     * @param string $groupName The group to add the contact to
     * @param string $contactDn The DN of the contact to add
     *
     * @return bool
     */
    public function addContact($groupName, $contactDn)
    {
        $groupDn = $this->dn($groupName);

        if ($groupDn && $contactDn) {
            $add = [];
            $add['member'] = $contactDn;

            return $this->connection->modAdd($groupDn, $add);
        }

        return false;
    }

    /**
     * Create a group.
     *
     * @param array $attributes Default attributes of the group
     *
     * @return bool|string
     */
    public function create(array $attributes)
    {
        $group = new Group($attributes);

        $group->validateRequired();

        // Reset the container by reversing the current container
        $group->setAttribute('container', array_reverse($group->getAttribute('container')));

        $add['cn'] = $group->getAttribute('group_name');
        $add['samaccountname'] = $group->getAttribute('group_name');
        $add['objectClass'] = 'Group';
        $add['description'] = $group->getAttribute('description');

        $container = 'OU='.implode(',OU=', $group->getAttribute('container'));

        $dn = 'CN='.$add['cn'].', '.$container.','.$this->adldap->getBaseDn();

        return $this->connection->add($dn, $add);
    }

    /**
     * Rename a group.
     *
     * @param string $groupName The group to rename
     * @param string $newName   The new name to give the group
     * @param array  $container
     *
     * @return bool
     */
    public function rename($groupName, $newName, $container)
    {
        $groupDn = $this->dn($groupName);

        if ($groupDn) {
            $newRDN = 'CN='.$newName;

            // Determine the container
            $container = array_reverse($container);
            $container = 'OU='.implode(', OU=', $container);

            $dn = $container.', '.$this->adldap->getBaseDn();

            return $this->connection->rename($groupDn, $newRDN, $dn, true);
        }

        return false;
    }

    /**
     * Remove a group from a group.
     *
     * @param string $parentName The parent group name
     * @param string $childName  The child group name
     *
     * @return bool
     */
    public function removeGroup($parentName, $childName)
    {
        $parentDn = $this->dn($parentName);

        $childDn = $this->dn($childName);

        if (is_string($parentDn) && is_string($childDn)) {
            $del = [];
            $del['member'] = $childDn;

            return $this->connection->modDelete($parentDn, $del);
        }

        return false;
    }

    /**
     * Remove a user from a group.
     *
     * @param string $groupName The group to remove a user from
     * @param string $username  The AD user to remove from the group
     *
     * @return bool
     */
    public function removeUser($groupName, $username)
    {
        $groupDn = $this->dn($groupName);

        $userDn = $this->adldap->user()->dn($username);

        if (is_string($groupDn) && is_string($userDn)) {
            $del = [];
            $del['member'] = $userDn;

            return $this->connection->modDelete($groupDn, $del);
        }

        return false;
    }

    /**
     * Remove a contact from a group.
     *
     * @param string $group       The group to remove the contact from
     * @param string $contactName The contact to remove
     *
     * @return bool
     */
    public function removeContact($group, $contactName)
    {
        // Find the parent dn
        $groupDn = $this->dn($group);

        $contactDn = $this->adldap->contact()->dn($contactName);

        if (is_string($groupDn) && is_string($contactDn)) {
            $del = [];
            $del['member'] = $contactDn;

            return $this->connection->modDelete($groupDn, $del);
        }

        return false;
    }

    /**
     * Return a list of members in a group.
     *
     * @param string $group  The group to query
     * @param array  $fields The fields to retrieve for each member
     *
     * @return array|bool
     */
    public function members($group, $fields = [])
    {
        $group = $this->find($group);

        if (is_array($group) && array_key_exists('member', $group)) {
            $members = [];

            foreach ($group['member'] as $member) {
                $members[] = $this->adldap->search()
                    ->setDn($member)
                    ->select($fields)
                    ->where('objectClass', '=', 'user')
                    ->where('objectClass', '=', 'person')
                    ->first();
            }

            return $members;
        }

        return false;
    }

    /**
     * Return a complete list of "groups in groups".
     *
     * @param string $groupName The group to get the list from
     *
     * @return array|bool
     */
    public function recursiveGroups($groupName)
    {
        $groups = [];

        $info = $this->find($groupName);

        if (is_array($info) && array_key_exists('cn', $info)) {
            $groups[] = $info['cn'];

            if (array_key_exists('memberof', $info)) {
                if (is_array($info['memberof'])) {
                    foreach ($info['memberof'] as $group) {
                        $explodedDn = $this->connection->explodeDn($group);

                        $groups = array_merge($groups, $this->recursiveGroups($explodedDn[0]));
                    }
                }
            }
        }

        return $groups;
    }

    /**
     * Returns a complete list of security groups in AD.
     *
     * @param bool   $includeDescription Whether to return a description
     * @param string $search             Search parameters
     * @param bool   $sorted             Whether to sort the results
     *
     * @return array|bool
     */
    public function allSecurity($includeDescription = false, $search = '*', $sorted = true)
    {
        return $this->search(Adldap::ADLDAP_SECURITY_GLOBAL_GROUP, $includeDescription, $search, $sorted);
    }

    /**
     * Returns a complete list of distribution lists in AD.
     *
     * @param bool   $includeDescription Whether to return a description
     * @param string $search             Search parameters
     * @param bool   $sorted             Whether to sort the results
     *
     * @return array|bool
     */
    public function allDistribution($includeDescription = false, $search = '*', $sorted = true)
    {
        return $this->search(Adldap::ADLDAP_DISTRIBUTION_GROUP, $includeDescription, $search, $sorted);
    }

    /**
     * Coping with AD not returning the primary group
     * http://support.microsoft.com/?kbid=321360.
     *
     * This is a re-write based on code submitted by Bruce which prevents the
     * need to search each security group to find the true primary group
     *
     * @param string $groupId Group ID
     * @param string $userId  User's Object SID
     *
     * @return bool
     */
    public function getPrimaryGroup($groupId, $userId)
    {
        $this->adldap->utilities()->validateNotNull('Group ID', $groupId);
        $this->adldap->utilities()->validateNotNull('User ID', $userId);

        $groupId = substr_replace($userId, pack('V', $groupId), strlen($userId) - 4, 4);

        $sid = $this->adldap->utilities()->getTextSID($groupId);

        $result = $this->adldap->search()
                ->where('objectsid', '=', $sid)
                ->first();

        if (is_array($result) && array_key_exists('dn', $result)) {
            return $result['dn'];
        }

        return false;
    }
}
