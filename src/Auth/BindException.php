<?php

namespace Adldap\Auth;

use Throwable;
use Adldap\Utilities;
use Adldap\AdldapException;
use Adldap\Connections\DetailedError;

/**
 * Class BindException
 *
 * Thrown when binding to an LDAP connection fails.
 *
 * @package Adldap\Auth
 */
class BindException extends AdldapException
{
    /**
     * {@inheritDoc}
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (Utilities::isUsingSELinux()) {
            $message .= " | Check SELinux Enforcement settings.";
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * The detailed LDAP error.
     *
     * @var DetailedError
     */
    protected $detailedError;

    /**
     * Sets the detailed error.
     *
     * @param DetailedError|null $error
     *
     * @return $this
     */
    public function setDetailedError(DetailedError $error = null)
    {
        $this->detailedError = $error;

        return $this;
    }

    /**
     * Returns the detailed error.
     *
     * @return DetailedError|null
     */
    public function getDetailedError()
    {
        return $this->detailedError;
    }
}
