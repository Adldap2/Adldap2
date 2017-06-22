<?php

namespace Adldap\Schemas;

class OpenLDAP extends ActiveDirectory
{
    /**
     * {@inheritdoc}
     */
    public function distinguishedName()
    {
        return 'dn';
    }

    /**
     * {@inheritdoc}
     */
    public function distinguishedNameSubKey()
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategory()
    {
        return 'objectclass';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassGroup()
    {
        return 'groupofnames';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassOu()
    {
        return 'groupofuniquenames';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassPerson()
    {
        return 'inetorgperson';
    }

    /**
     * {@inheritdoc}
     */
    public function objectGuid()
    {
        return 'entryuuid';
    }

    /**
     * {@inheritdoc}
     */
    public function objectGuidRequiresConversion()
    {
        return false;
    }
}
