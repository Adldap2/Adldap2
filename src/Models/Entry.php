<?php

namespace Adldap\Models;

use Adldap\Classes\Utilities;
use Adldap\Schemas\ActiveDirectory;

class Entry extends AbstractModel
{
    /**
     * Returns the entry's name. An AD alias for the CN attribute.
     *
     * https://msdn.microsoft.com/en-us/library/ms675449(v=vs.85).aspx
     *
     * @return string
     */
    public function getName()
    {
        return $this->getAttribute(ActiveDirectory::NAME, 0);
    }

    /**
     * Sets the entry's name.
     *
     * @param string $name
     *
     * @return Entry
     */
    public function setName($name)
    {
        return $this->setAttribute(ActiveDirectory::NAME, $name, 0);
    }

    /**
     * Returns the entry's common name.
     *
     * https://msdn.microsoft.com/en-us/library/ms675449(v=vs.85).aspx
     *
     * @return string
     */
    public function getCommonName()
    {
        return $this->getAttribute(ActiveDirectory::COMMON_NAME, 0);
    }

    /**
     * Sets the entry's common name.
     *
     * @param string $name
     *
     * @return Entry
     */
    public function setCommonName($name)
    {
        return $this->setAttribute(ActiveDirectory::COMMON_NAME, $name, 0);
    }

    /**
     * Returns the entry's samaccountname.
     *
     * https://msdn.microsoft.com/en-us/library/ms679635(v=vs.85).aspx
     *
     * @return string
     */
    public function getAccountName()
    {
        return $this->getAttribute(ActiveDirectory::ACCOUNT_NAME, 0);
    }

    /**
     * Returns the entry's samaccounttype.
     *
     * https://msdn.microsoft.com/en-us/library/ms679637(v=vs.85).aspx
     *
     * @return string
     */
    public function getAccountType()
    {
        return $this->getAttribute(ActiveDirectory::ACCOUNT_TYPE, 0);
    }

    /**
     * Returns the entry's `when created` time.
     *
     * https://msdn.microsoft.com/en-us/library/ms680924(v=vs.85).aspx
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getAttribute(ActiveDirectory::CREATED_AT, 0);
    }

    /**
     * Returns the entry's `when changed` time.
     *
     * https://msdn.microsoft.com/en-us/library/ms680921(v=vs.85).aspx
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getAttribute(ActiveDirectory::UPDATED_AT, 0);
    }

    /**
     * Returns the entry's object class array.
     *
     * https://msdn.microsoft.com/en-us/library/ms679012(v=vs.85).aspx
     *
     * @return array
     */
    public function getObjectClass()
    {
        return $this->getAttribute(ActiveDirectory::OBJECT_CLASS);
    }

    /**
     * Returns the CN of the entry's object category.
     *
     * @return null|string
     */
    public function getObjectCategory()
    {
        $category = $this->getObjectCategoryArray();

        if(is_array($category) && array_key_exists(0, $category)) {
            return $category[0];
        }

        return null;
    }

    /**
     * Returns the entry's object category DN in an exploded array.
     *
     * @return array
     */
    public function getObjectCategoryArray()
    {
        return Utilities::explodeDn($this->getObjectCategoryDn());
    }

    /**
     * Returns the entry's object category DN string.
     *
     * @return array
     */
    public function getObjectCategoryDn()
    {
        return $this->getAttribute(ActiveDirectory::OBJECT_CATEGORY, 0);
    }

    /**
     * Returns the entry's object SID.
     *
     * https://msdn.microsoft.com/en-us/library/ms679024(v=vs.85).aspx
     *
     * @return string
     */
    public function getObjectSid()
    {
        return $this->getAttribute(ActiveDirectory::OBJECT_SID, 0);
    }

    /**
     * Returns the entry's primary group ID.
     *
     * https://msdn.microsoft.com/en-us/library/ms679375(v=vs.85).aspx
     *
     * @return string
     */
    public function getPrimaryGroupId()
    {
        return $this->getAttribute(ActiveDirectory::PRIMARY_GROUP_ID, 0);
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
        return $this->getAttribute(ActiveDirectory::INSTANCE_TYPE, 0);
    }

    /**
     * Returns the entry's GUID.
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->getAttribute(ActiveDirectory::OBJECT_GUID, 0);
    }

    /**
     * Returns the entry's SID.
     *
     * @return string
     */
    public function getSid()
    {
        return $this->getAttribute(ActiveDirectory::OBJECT_SID, 0);
    }

    /**
     * Returns the entry's max password age.
     *
     * @return string
     */
    public function getMaxPasswordAge()
    {
        return $this->getAttribute(ActiveDirectory::MAX_PASSWORD_AGE, 0);
    }
}
