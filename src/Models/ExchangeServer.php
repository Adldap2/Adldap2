<?php

namespace Adldap\Models;

use Adldap\Schemas\Schema;

class ExchangeServer extends Entry
{
    /**
     * Returns the exchange servers serial number.
     *
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->getAttribute(Schema::get()->serialNumber(), 0);
    }

    /**
     * Returns the exchange servers version number.
     *
     * @return string
     */
    public function getVersionNumber()
    {
        return $this->getAttribute(Schema::get()->versionNumber(), 0);
    }

    /**
     * Returns the exchange servers administrator display name.
     *
     * @return string
     */
    public function getAdminDisplayName()
    {
        return $this->getAttribute(Schema::get()->adminDisplayName(), 0);
    }

    /**
     * Returns the exchange servers message tracking enabled option.
     *
     * @return bool
     */
    public function getMessageTrackingEnabled()
    {
        return $this->convertStringToBool($this->getAttribute(Schema::get()->messageTrackingEnabled(), 0));
    }
}
