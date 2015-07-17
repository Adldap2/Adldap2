<?php

namespace Adldap\Objects\Traits;

trait HasCriticalSystemObjectTrait
{
    /**
     * Returns true / false if the entry is a critical system object.
     *
     * @return null|bool
     */
    public function getIsCriticalSystemObject()
    {
        $attribute = $this->getAttribute('iscriticalsystemobject', 0);

        return $this->convertStringToBool($attribute);
    }
}
