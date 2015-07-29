<?php

namespace Adldap\Models\Traits;

use Adldap\Schemas\ActiveDirectory;

trait HasCriticalSystemObjectTrait
{
    /**
     * Returns true / false if the entry is a critical system object.
     *
     * @return null|bool
     */
    public function isCriticalSystemObject()
    {
        $attribute = $this->getAttribute(ActiveDirectory::IS_CRITICAL_SYSTEM_OBJECT, 0);

        return $this->convertStringToBool($attribute);
    }
}
