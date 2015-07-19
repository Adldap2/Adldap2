<?php

namespace Adldap\Objects\Traits;

use Adldap\Schemas\ActiveDirectory;

trait HasDescriptionTrait
{
    /**
     * Returns the entry's description.
     *
     * https://msdn.microsoft.com/en-us/library/ms675492(v=vs.85).aspx
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getAttribute(ActiveDirectory::DESCRIPTION, 0);
    }
}
