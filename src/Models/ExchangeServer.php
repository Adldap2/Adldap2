<?php

namespace Adldap\Models;

use Adldap\Schemas\ActiveDirectory;

class ExchangeServer extends Entry
{
    /**
     * Returns the exchange servers serial number.
     *
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->getAttribute(ActiveDirectory::SERIAL_NUMBER, 0);
    }

    /**
     * Returns the exchange servers version number.
     *
     * @return string
     */
    public function getVersionNumber()
    {
        return $this->getAttribute(ActiveDirectory::VERSION_NUMBER, 0);
    }

    /**
     * Returns the exchange servers administrator display name.
     *
     * @return string
     */
    public function getAdminDisplayName()
    {
        return $this->getAttribute(ActiveDirectory::ADMIN_DISPLAY_NAME, 0);
    }

    /**
     * Returns the exchange servers message tracking enabled option.
     *
     * @return bool
     */
    public function getMessageTrackingEnabled()
    {
        return $this->convertStringToBool($this->getAttribute(ActiveDirectory::MESSAGE_TRACKING_ENABLED, 0));
    }
}
