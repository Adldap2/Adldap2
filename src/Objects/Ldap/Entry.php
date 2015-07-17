<?php

namespace Adldap\Objects\Ldap;

use Adldap\Objects\AbstractObject;

class Entry extends AbstractObject
{
    /**
     * Returns the entry's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getAttribute('name', 0);
    }

    /**
     * Returns the entry's common name.
     *
     * @return mixed
     */
    public function getCommonName()
    {
        return $this->getAttribute('cn', 0);
    }

    /**
     * Returns the entry's samaccountname.
     *
     * @return mixed
     */
    public function getAccountName()
    {
        return $this->getAttribute('samaccountname', 0);
    }

    /**
     * Returns the entry's samaccounttype.
     *
     * @return string
     */
    public function getAccountType()
    {
        return $this->getAttribute('samaccounttype', 0);
    }

    /**
     * Returns the entry's `when created` time.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getAttribute('whencreated', 0);
    }

    /**
     * Returns the entry's `when changed` time.
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getAttribute('whenchanged', 0);
    }

    /**
     * Returns the entry's distinguished name string.
     *
     * @return string
     */
    public function getDistinguishedName()
    {
        return $this->getAttribute('distinguishedname', 0);
    }

    /**
     * Returns the entry's object class.
     *
     * @return mixed
     */
    public function getObjectClass()
    {
        return $this->getAttribute('objectclass', 0);
    }

    /**
     * Returns the entry's primary group ID.
     *
     * @return string
     */
    public function getPrimaryGroupId()
    {
        return $this->getAttribute('primarygroupid', 0);
    }

    /**
     * Returns the entry's object SID.
     *
     * @return string
     */
    public function getObjectSid()
    {
        return $this->getAttribute('objectsid', 0);
    }

    /**
     * Returns the entry's instance type.
     *
     * https://msdn.microsoft.com/en-us/library/ms676204(v=vs.85).aspx
     *
     * @return int
     */
    public function getInstanceType()
    {
        return $this->getAttribute('instancetype', 0);
    }

    /**
     * Converts the inserted string boolean to a PHP boolean.
     *
     * @param string $bool
     *
     * @return null|bool
     */
    protected function convertStringToBool($bool)
    {
        $bool = strtoupper($bool);

        if($bool === 'FALSE') {
            return false;
        } else if($bool === 'TRUE') {
            return true;
        } else {
            return null;
        }
    }
}
