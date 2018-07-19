<?php

namespace Adldap;

/**
 * Class AdldapError
 *
 * Detailed information class for errors returned by getDetailedError()
 * @package Adldap
 */
class AdldapError
{
    /**
     * @var int the error code from ldap_errno
     */
    private $errorCode = null;
    /**
     * @var string the error message from ldap_error
     */
    private $errorMessage = null;
    /**
     * @var string the diagnostic message when retrieved after an ldap_error
     */
    private $diagnosticMessage = null;

    /**
     * AdldapError constructor.
     * @param $errorCode
     * @param $errorMessage
     * @param $diagnosticMessage
     */
    public function __construct($errorCode, $errorMessage, $diagnosticMessage)
    {
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->diagnosticMessage;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
    /**
     * @return string
     */
    public function getDiagnosticMessage()
    {
        return $this->diagnosticMessage;
    }
}