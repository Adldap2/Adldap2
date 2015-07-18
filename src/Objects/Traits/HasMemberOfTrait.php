<?php

namespace Adldap\Objects\Traits;

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
        return $this->getAttribute('memberof');
    }
}
