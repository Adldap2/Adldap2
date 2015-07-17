<?php

namespace Adldap\Objects\Traits;

trait HasCriticalSystemObjectTrait
{
    /**
     * Returns true / false if the entry is a critical system object.
     *
     * @return bool
     */
    public function getIsCriticalSystemObject()
    {
        $bool =  $this->getAttribute('iscriticalsystemobject', 0);

        if($bool === 'FALSE') {
            return false;
        } else {
            return true;
        }
    }
}
