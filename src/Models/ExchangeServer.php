<?php

namespace Adldap\Models;

class ExchangeServer extends Entry
{
    /**
     * Returns the exchange servers serial number.
     *
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->getAttribute($this->schema->serialNumber(), 0);
    }

    /**
     * Returns the exchange servers version number.
     *
     * @return string
     */
    public function getVersionNumber()
    {
        return $this->getAttribute($this->schema->versionNumber(), 0);
    }

    /**
     * Returns the exchange servers administrator display name.
     *
     * @return string
     */
    public function getAdminDisplayName()
    {
        return $this->getAttribute($this->schema->adminDisplayName(), 0);
    }

    /**
     * Returns the exchange servers message tracking enabled option.
     *
     * @return bool
     */
    public function getMessageTrackingEnabled()
    {
        return $this->convertStringToBool($this->getAttribute($this->schema->messageTrackingEnabled(), 0));
    }

    /**
     * Returns a list of Storage Groups in Exchange for a given mail server.
     *
     * @return bool|array
     */
    public function getStorageGroups()
    {
        return $this
            ->query
            ->setDn($this->getDn())
            ->whereEquals($this->schema->objectCategory(), $this->schema->objectCategoryExchangeStorageGroup())
            ->get();
    }

    /**
     * Returns a list of Databases within any given storage group in Exchange for a given mail server.
     *
     * @param string $storageGroup The full DN of an Storage Group.
     *
     * @return bool|array
     */
    public function getStorageGroupDatabases($storageGroup)
    {
        return $this
            ->query
            ->setDn($storageGroup)
            ->whereEquals($this->schema->objectCategory(), $this->schema->objectCategoryExchangePrivateMdb())
            ->get();
    }
}
