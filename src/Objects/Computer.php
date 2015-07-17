<?php

namespace Adldap\Objects;

class Computer extends AbstractObject
{
    /**
     * Returns the computers name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getAttribute('cn', 0);
    }

    /**
     * Returns the computers `when created` time.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getAttribute('whencreated', 0);
    }

    /**
     * Returns the computers `when changed` time.
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getAttribute('whenchanged', 0);
    }

    /**
     * Returns the computers last log off date.
     *
     * @return string
     */
    public function getLastLogOff()
    {
        return $this->getAttribute('lastlogoff', 0);
    }

    /**
     * Returns the computers last log on date.
     *
     * @return string
     */
    public function getLastLogon()
    {
        return $this->getAttribute('lastlogon', 0);
    }

    /**
     * Returns the computers last log on timestamp.
     *
     * @return string
     */
    public function getLastLogonTimestamp()
    {
        return $this->getAttribute('lastlogontimestamp', 0);
    }

    /**
     * Returns the computers operating system.
     *
     * @return string
     */
    public function getOperatingSystem()
    {
        return $this->getAttribute('operatingsystem', 0);
    }

    /**
     * Returns the computers operating system version.
     *
     * @return string
     */
    public function getOperatingSystemVersion()
    {
        return $this->getAttribute('operatingsystemversion', 0);
    }

    /**
     * Returns the computers operating system service pack.
     *
     * @return string
     */
    public function getOperatingSystemServicePack()
    {
        return $this->getAttribute('operatingsystemservicepack', 0);
    }

    /**
     * Returns the computers DNS host name.
     *
     * @return string
     */
    public function getDnsHostName()
    {
        return $this->getAttribute('dnshostname', 0);
    }

    /**
     * Returns the computers bad password time.
     *
     * @return string
     */
    public function getBadPasswordTime()
    {
        return $this->getAttribute('badpasswordtime', 0);
    }

    /**
     * Returns the computers account expiry date.
     *
     * @return mixed
     */
    public function getAccountExpiry()
    {
        return $this->getAttribute('accountexpires', 0);
    }

    /**
     * Returns the computers primary group ID.
     *
     * @return string
     */
    public function getPrimaryGroupId()
    {
        return $this->getAttribute('primarygroupid', 0);
    }

    /**
     * Returns the computers object SID.
     *
     * @return string
     */
    public function getObjectSid()
    {
        return $this->getAttribute('objectsid', 0);
    }

    /**
     * Returns the computers instance type.
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
     * Returns the computers distinguished name string.
     *
     * @return string
     */
    public function getDistinguishedName()
    {
        return $this->getAttribute('distinguishedname', 0);
    }
}
