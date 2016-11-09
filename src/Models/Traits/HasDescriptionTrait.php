<?php

namespace Adldap\Models\Traits;

trait HasDescriptionTrait
{
    use ModelTrait;

    /**
     * Returns the models's description.
     *
     * https://msdn.microsoft.com/en-us/library/ms675492(v=vs.85).aspx
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getFirstAttribute($this->getSchema()->description());
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
        return $this->setFirstAttribute($this->getSchema()->description(), $description);
    }
}
