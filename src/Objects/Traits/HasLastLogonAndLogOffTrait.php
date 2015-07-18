<?php

namespace Adldap\Objects\Traits;

trait HasLastLogonAndLogOffTrait
{
    /**
     * Returns the entry's last log off date.
     *
     * https://msdn.microsoft.com/en-us/library/ms676822(v=vs.85).aspx
     *
     * @return string
     */
    public function getLastLogOff()
    {
        return $this->getAttribute('lastlogoff', 0);
    }

    /**
     * Returns the entry's last log on date.
     *
     * https://msdn.microsoft.com/en-us/library/ms676823(v=vs.85).aspx
     *
     * @return string
     */
    public function getLastLogon()
    {
        return $this->getAttribute('lastlogon', 0);
    }

    /**
     * Returns the entry's last log on timestamp.
     *
     * https://msdn.microsoft.com/en-us/library/ms676824(v=vs.85).aspx
     *
     * @return string
     */
    public function getLastLogonTimestamp()
    {
        return $this->getAttribute('lastlogontimestamp', 0);
    }
}
