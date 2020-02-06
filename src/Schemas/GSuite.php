<?php

namespace Adldap\Schemas;

class GSuite extends Schema
{
    /**
     * {@inheritdoc}
     */
    public function accountName()
    {
        return 'uid';
    }

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
    public function filterEnabled()
    {
        return sprintf('(!(%s=*))', $this->lockoutTime());
    }

    /**
     * {@inheritdoc}
     */
    public function filterDisabled()
    {
        return sprintf('(%s=*)', $this->lockoutTime());
    }

    /**
     * {@inheritdoc}
     */
    public function lockoutTime()
    {
        return 'pwdAccountLockedTime';
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategory()
    {
        return 'objectcategory';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClass()
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
        return 'organizationalunit';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassPerson()
    {
        return 'person';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassUser()
    {
        return 'person';
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

    /**
     * {@inheritdoc}
     */
    public function department()
    {
        return 'departmentnumber';
    }

    /**
     * {@inheritdoc}
     */
    public function employeeId()
    {
        return 'uidnumber';
    }
    
    /**
     * {@inheritdoc}
     */
    public function primaryGroupId()
    {
        return 'gidnumber';
    }
}
