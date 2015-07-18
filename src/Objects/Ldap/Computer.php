<?php

namespace Adldap\Objects\Ldap;

use Adldap\Objects\Traits\HasCriticalSystemObjectTrait;
use Adldap\Objects\Traits\HasDescriptionTrait;

class Computer extends Entry
{
    use HasCriticalSystemObjectTrait;

    use HasDescriptionTrait;

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
     * @return string
     */
    public function getAccountExpiry()
    {
        return $this->getAttribute('accountexpires', 0);
    }
}
