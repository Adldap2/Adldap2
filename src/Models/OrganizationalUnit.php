<?php

namespace Adldap\Models;

/**
 * Class OrganizationalUnit
 *
 * Represents an LDAP organizational unit.
 *
 * @package Adldap\Models
 */
class OrganizationalUnit extends Entry
{
    use Concerns\HasDescription;

    /**
     * Retrieves the organization units OU attribute.
     *
     * @return string
     */
    public function getOu()
    {
        return $this->getFirstAttribute($this->schema->organizationalUnitShort());
    }

    /**
     * {@inheritdoc}
     */
    protected function getCreatableDn()
    {
        return $this->getDnBuilder()->addOU($this->getOu());
    }
}
