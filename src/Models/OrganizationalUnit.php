<?php

namespace Adldap\Models;

class OrganizationalUnit extends AbstractModel
{
    /**
     * Retrieves the organization units OU attribute.
     *
     * @return string
     */
    public function getOu()
    {
        return $this->getAttribute('ou', 0);
    }
}
