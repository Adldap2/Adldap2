<?php

namespace Adldap\Classes;

use Adldap\Models\Group;
use Adldap\Models\Entry;
use Adldap\Models\User;
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

        $user = $this->adldap->users()->find($user);

        if($group instanceof Group && $user instanceof User) {
            $sid = Utilities::binarySidToText($group->getSid());

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
