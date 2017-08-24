<?php

namespace Adldap\Models;

use Adldap\Models\Concerns\HasDescription;
use Adldap\Models\Concerns\HasCriticalSystemObject;

class Container extends Entry
{
    use HasDescription, HasCriticalSystemObject;

    /**
     * Returns the containers system flags integer.
     *
     * https://msdn.microsoft.com/en-us/library/ms680022(v=vs.85).aspx
     *
     * @return string
     */
    public function getSystemFlags()
    {
        return $this->getFirstAttribute($this->schema->systemFlags());
    }
}
