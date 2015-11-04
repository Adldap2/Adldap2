<?php

namespace Adldap\Models\Traits;

use Adldap\Schemas\Schema;

trait HasCriticalSystemObjectTrait
{
    /**
     * Returns true / false if the entry is a critical system object.
     *
     * @return null|bool
     */
    public function isCriticalSystemObject()
    {
        $attribute = $this->getAttribute(Schema::get()->isCriticalSystemObject(), 0);

        return $this->convertStringToBool($attribute);
    }
}
