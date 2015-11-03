<?php

namespace Adldap\Models;

use Adldap\Schemas\Schema;

class OrganizationalUnit extends AbstractModel
{
    /**
     * Retrieves the organization units OU attribute.
     *
     * @return string
     */
    public function getOu()
    {
        return $this->getAttribute(Schema::get()->organizationalUnitShort(), 0);
    }
}
