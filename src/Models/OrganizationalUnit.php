<?php

namespace Adldap\Models;

use Adldap\Schemas\ActiveDirectory;

class OrganizationalUnit extends AbstractModel
{
    /**
     * Retrieves the organization units OU attribute.
     *
     * @return string
     */
    public function getOu()
    {
        return $this->getAttribute(ActiveDirectory::ORGANIZATIONAL_UNIT_SHORT, 0);
    }
}
