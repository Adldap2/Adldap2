<?php

namespace Adldap\Models;

use DateTime;

class RootDse extends AbstractModel
{
    /**
     * Returns the hosts current time.
     *
     * @return string
     */
    public function getCurrentTime()
    {
        return $this->getAttribute($this->schema->currentTime(), 0);
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
        return $this->getAttribute($this->schema->configurationNamingContext(), 0);
    }

    /**
     * Returns the hosts schema naming context.
     *
     * @return string
     */
    public function getSchemaNamingContext()
    {
        return $this->getAttribute($this->schema->schemaNamingContext(), 0);
    }

    /**
     * Returns the hosts DNS name.
     *
     * @return string
     */
    public function getDnsHostName()
    {
        return $this->getAttribute($this->schema->dnsHostName(), 0);
    }

    /**
     * Returns the current hosts server name.
     *
     * @return string
     */
    public function getServerName()
    {
        return $this->getAttribute($this->schema->serverName(), 0);
    }
}
