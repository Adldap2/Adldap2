<?php

namespace Adldap\Classes;

use Adldap\Objects\Ldap\Group;
use Adldap\Objects\Ldap\Entry;
use Adldap\Objects\Ldap\User;
use Adldap\Schemas\ActiveDirectory;

class Groups extends AbstractQueryable
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
     * Coping with AD not returning the primary group.
     *
     * http://support.microsoft.com/?kbid=321360.
     *
     * @param string $group
     * @param string $user
     *
     * @return bool
     */
    public function getPrimaryGroup($group, $user)
    {
        $group = $this->find($group);

        $user = $this->adldap->user()->find($user);

        if($group instanceof Group && $user instanceof User) {
            $sid = $this->adldap->utilities()->getTextSID($group->getSid());

            $result = $this->adldap->search()
                    ->where(ActiveDirectory::OBJECT_SID, '=', $sid)
                    ->first();

            if($result instanceof Entry) {
                return $result->getDn();
            }
        }

        return false;
    }
}
