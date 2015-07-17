<?php

namespace Adldap\Objects\Traits;

trait HasDescriptionTrait
{
    /**
     * Returns the entry's description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getAttribute('description', 0);
    }
}
