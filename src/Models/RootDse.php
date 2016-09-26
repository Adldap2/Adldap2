<?php

namespace Adldap\Models;

use DateTime;

class RootDse extends Model
{
    /**
     * Returns the hosts current time.
     *
     * @return string
     */
    public function getCurrentTime()
    {
        return $this->getFirstAttribute($this->schema->currentTime());
    }

    /**
     * Returns the hosts current time in the models date format.
     *
     * @return string
     */
    public function getCurrentTimeDate()
    {
        return (new DateTime())->setTimestamp($this->getCurrentTimeTimestamp())->format($this->dateFormat);
    }

    /**
     * Returns the hosts current time in unix timestamp format.
     *
     * @return int
     */
    public function getCurrentTimeTimestamp()
    {
        return DateTime::createFromFormat($this->timestampFormat, $this->getCurrentTime())->getTimestamp();
    }

    /**
     * Returns the hosts configuration naming context.
     *
     * @return string
     */
    public function getConfigurationNamingContext()
    {
        return $this->getFirstAttribute($this->schema->configurationNamingContext());
    }

    /**
     * Returns the hosts schema naming context.
     *
     * @return string
     */
    public function getSchemaNamingContext()
    {
        return $this->getFirstAttribute($this->schema->schemaNamingContext());
    }

    /**
     * Returns the hosts DNS name.
     *
     * @return string
     */
    public function getDnsHostName()
    {
        return $this->getFirstAttribute($this->schema->dnsHostName());
    }

    /**
     * Returns the current hosts server name.
     *
     * @return string
     */
    public function getServerName()
    {
        return $this->getFirstAttribute($this->schema->serverName());
    }
}
