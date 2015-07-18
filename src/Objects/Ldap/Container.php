<?php

namespace Adldap\Objects\Ldap;

use Adldap\Objects\Traits\HasDescriptionTrait;
use Adldap\Objects\Traits\HasCriticalSystemObjectTrait;

class Container extends Entry
{
    use HasDescriptionTrait;

    use HasCriticalSystemObjectTrait;

    /**
     * Returns the containers system flags integer.
     *
     * @return string
     */
    public function getSystemFlags()
    {
        return $this->getAttribute('systemflags', 0);
    }
}
