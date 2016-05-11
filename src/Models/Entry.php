<?php

namespace Adldap\Models;

use Adldap\Utilities;
use DateTime;

class Entry extends AbstractModel
{
    /**
     * Returns true / false if the current model is writeable
     * by checking its instance type integer.
     *
     * @return bool
     */
    public function isWriteable()
    {
        return (int) $this->getInstanceType() === 4;
    }

    /**
     * Returns the model's name. An AD alias for the CN attribute.
     *
     * https://msdn.microsoft.com/en-us/library/ms675449(v=vs.85).aspx
     *
     * @return string
     */
    public function getName()
    {
        return $this->getAttribute($this->schema->name(), 0);
    }

    /**
     * Sets the model's name.
     *
     * @param string $name
     *
     * @return Entry
     */
    public function setName($name)
    {
        return $this->setAttribute($this->schema->name(), $name, 0);
    }

    /**
     * Returns the model's samaccountname.
     *
     * https://msdn.microsoft.com/en-us/library/ms679635(v=vs.85).aspx
     *
     * @return string
     */
    public function getAccountName()
    {
        return $this->getAttribute($this->schema->accountName(), 0);
    }

    /**
     * Sets the model's samaccountname.
     *
     * @param string $accountName
     *
     * @return AbstractModel
     */
    public function setAccountName($accountName)
    {
        return $this->setAttribute($this->schema->accountName(), $accountName, 0);
    }

    /**
     * Returns the model's samaccounttype.
     *
     * https://msdn.microsoft.com/en-us/library/ms679637(v=vs.85).aspx
     *
     * @return string
     */
    public function getAccountType()
    {
        return $this->getAttribute($this->schema->accountType(), 0);
    }

    /**
     * Returns the model's `whenCreated` time.
     *
     * https://msdn.microsoft.com/en-us/library/ms680924(v=vs.85).aspx
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getAttribute($this->schema->createdAt(), 0);
    }

    /**
     * Returns the created at time in a mysql formatted date.
     *
     * @return string
     */
    public function getCreatedAtDate()
    {
        return (new DateTime())->setTimestamp($this->getCreatedAtTimestamp())->format($this->dateFormat);
    }

    /**
     * Returns the created at time in a unix timestamp format.
     *
     * @return float
     */
    public function getCreatedAtTimestamp()
    {
        return DateTime::createFromFormat('YmdHis.0Z', $this->getCreatedAt())->getTimestamp();
    }

    /**
     * Returns the model's `whenChanged` time.
     *
     * https://msdn.microsoft.com/en-us/library/ms680921(v=vs.85).aspx
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getAttribute($this->schema->updatedAt(), 0);
    }

    /**
     * Returns the updated at time in a mysql formatted date.
     *
     * @return string
     */
    public function getUpdatedAtDate()
    {
        return (new DateTime())->setTimestamp($this->getUpdatedAtTimestamp())->format($this->dateFormat);
    }

    /**
     * Returns the updated at time in a unix timestamp format.
     *
     * @return float
     */
    public function getUpdatedAtTimestamp()
    {
        return DateTime::createFromFormat($this->timestampFormat, $this->getUpdatedAt())->getTimestamp();
    }

    /**
     * Returns the Container of the current Model.
     *
     * https://msdn.microsoft.com/en-us/library/ms679012(v=vs.85).aspx
     *
     * @return Container|Entry|bool
     */
    public function getObjectClass()
    {
        return $this->query->findByDn($this->getObjectCategoryDn());
    }

    /**
     * Returns the CN of the model's object category.
     *
     * @return null|string
     */
    public function getObjectCategory()
    {
        $category = $this->getObjectCategoryArray();

        if (is_array($category) && array_key_exists(0, $category)) {
            return $category[0];
        }
    }

    /**
     * Returns the model's object category DN in an exploded array.
     *
     * @return array|false
     */
    public function getObjectCategoryArray()
    {
        return Utilities::explodeDn($this->getObjectCategoryDn());
    }

    /**
     * Returns the model's object category DN string.
     *
     * @return null|string
     */
    public function getObjectCategoryDn()
    {
        return $this->getAttribute($this->schema->objectCategory(), 0);
    }

    /**
     * Returns the model's object SID.
     *
     * https://msdn.microsoft.com/en-us/library/ms679024(v=vs.85).aspx
     *
     * @return string
     */
    public function getObjectSid()
    {
        return $this->getAttribute($this->schema->objectSid(), 0);
    }

    /**
     * Returns the model's primary group ID.
     *
     * https://msdn.microsoft.com/en-us/library/ms679375(v=vs.85).aspx
     *
     * @return string
     */
    public function getPrimaryGroupId()
    {
        return $this->getAttribute($this->schema->primaryGroupId(), 0);
    }

    /**
     * Returns the model's instance type.
     *
     * https://msdn.microsoft.com/en-us/library/ms676204(v=vs.85).aspx
     *
     * @return int
     */
    public function getInstanceType()
    {
        return $this->getAttribute($this->schema->instanceType(), 0);
    }

    /**
     * Returns the model's GUID.
     *
     * @return string
     */
    public function getGuid()
    {
        return Utilities::binaryGuidToString($this->getAttribute($this->schema->objectGuid(), 0));
    }

    /**
     * Returns the model's SID.
     *
     * @return string
     */
    public function getSid()
    {
        return Utilities::binarySidToString($this->getAttribute($this->schema->objectSid(), 0));
    }

    /**
     * Returns the model's max password age.
     *
     * @return string
     */
    public function getMaxPasswordAge()
    {
        return $this->getAttribute($this->schema->maxPasswordAge(), 0);
    }
}
