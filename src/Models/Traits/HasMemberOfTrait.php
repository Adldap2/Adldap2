<?php

namespace Adldap\Models\Traits;

use Adldap\Schemas\ActiveDirectory;

trait HasMemberOfTrait
{
    /**
     * Returns the array of group DNs the entry is a member of.
     *
     * https://msdn.microsoft.com/en-us/library/ms677099(v=vs.85).aspx
     *
     * @return array
     */
    public function getMemberOf()
    {
        $groups = [];

        $dns = $this->getAttribute(ActiveDirectory::MEMBER_OF);

        if(is_array($dns)) {
            unset($dns['count']);

            foreach($dns as $key => $dn) {
                $groups[] = $this->getAdldap()->search()->findByDn($dn);
            }
        }

        return $groups;
    }

    /**
     * Sets the models's group DN's the entry is a member of.
     *
     * @param array $groups
     *
     * @return $this
     */
    public function setMemberOf(array $groups)
    {
        return $this->setAttribute(ActiveDirectory::MEMBER_OF, $groups);
    }
}
