<?php

namespace Adldap\Models\Traits;

use Adldap\Schemas\ActiveDirectory;

trait HasDescriptionTrait
{
    /**
     * Returns the models's description.
     *
     * https://msdn.microsoft.com/en-us/library/ms675492(v=vs.85).aspx
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getAttribute(ActiveDirectory::DESCRIPTION, 0);
    }

    /**
     * Sets the models's description.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setAttribute(ActiveDirectory::DESCRIPTION, $description, 0);
    }
}
