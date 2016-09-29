<?php

namespace Adldap\Auth;

use Adldap\Connections\Configuration;
use Adldap\Contracts\Auth\GuardInterface;
use Adldap\Contracts\Connections\ConnectionInterface;
use Adldap\Exceptions\Auth\BindException;
use Adldap\Exceptions\Auth\PasswordRequiredException;
use Adldap\Exceptions\Auth\UsernameRequiredException;

class Guard implements GuardInterface
{
    /**
     * LDAP bind errors and their hex codes.
     */
    const ERROR_LDAP_NO_SUCH_OBJECT = '525';
    const ERROR_LOGON_FAILURE = '52e';
    const ERROR_ACCOUNT_RESTRICTION = '52f';
    const ERROR_USER_NOT_FOUND = '525';
    const ERROR_INVALID_LOGON_HOURS = '530';
    const ERROR_INVALID_WORKSTATION = '531';
    const ERROR_PASSWORD_EXPIRED = '532';
    const ERROR_ACCOUNT_DISABLED = '533';
    const ERROR_TOO_MANY_CONTEXT_IDS = '568';
    const ERROR_ACCOUNT_EXPIRED = '701';
    const ERROR_PASSWORD_MUST_CHANGE = '773';
    const ERROR_ACCOUNT_LOCKED_OUT = '775';

    /**
     * LDAP bind error message map.
     *
     * @var array
     */
    public $errors = [
        self::ERROR_LDAP_NO_SUCH_OBJECT => 'User does not exist.',
        self::ERROR_LOGON_FAILURE => 'The username is valid, however the password is invalid.',
        self::ERROR_ACCOUNT_RESTRICTION => 'Account restrictions are preventing this user from signing in.',
        self::ERROR_USER_NOT_FOUND => 'The username is invalid.',
        self::ERROR_INVALID_LOGON_HOURS => 'User logon time restriction violation.',
        self::ERROR_INVALID_WORKSTATION => 'User is not aloud to login to this computer.',
        self::ERROR_PASSWORD_EXPIRED => 'User password has expired.',
        self::ERROR_ACCOUNT_DISABLED => 'User account is disabled.',
        self::ERROR_TOO_MANY_CONTEXT_IDS => 'The user security context accumulated too many security identifiers.',
        self::ERROR_ACCOUNT_EXPIRED => 'User account has expired.',
        self::ERROR_PASSWORD_MUST_CHANGE => 'User password must be changed.',
        self::ERROR_ACCOUNT_LOCKED_OUT => 'User account is locked out.',
    ];

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * {@inheritdoc}
     */
    public function __construct(ConnectionInterface $connection, Configuration $configuration)
    {
        $this->connection = $connection;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function attempt($username, $password, $bindAsUser = false)
    {
        $this->validateCredentials($username, $password);

        try {
            $this->bind($username, $password);
        } catch (BindException $e) {
            // We'll catch the BindException here to return false
            // to allow developers to use a simple if / else
            // using the authenticate method.
            return false;
        }

        // If we're not allowed to bind as the user,
        // we'll rebind as administrator.
        if ($bindAsUser === false) {
            // We won't catch any BindException here so
            // developers can catch rebind failures.
            $this->bindAsAdministrator();
        }

        // No bind exceptions, authentication passed.
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function bind($username, $password, $prefix = null, $suffix = null)
    {
        // We'll allow binding with a null username and password
        // if their empty. This will allow us to anonymously
        // bind to our servers if needed.
        $username = $username ?: null;
        $password = $password ?: null;

        if ($username) {
            // If the username isn't empty, we'll append the configured
            // account prefix and suffix to bind to the LDAP server.
            $prefix = is_null($prefix) ? $this->configuration->getAccountPrefix() : $prefix;
            $suffix = is_null($suffix) ? $this->configuration->getAccountSuffix() : $suffix;

            $username = $prefix.$username.$suffix;
        }

        // We'll mute any exceptions / warnings here. All we need to know
        // is if binding failed and we'll throw our own exception.
        if (!@$this->connection->bind($username, $password)) {
            throw new BindException($this->connection->getLastError(), $this->connection->errNo());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bindAsAdministrator()
    {
        $credentials = $this->configuration->getAdminCredentials();

        list($username, $password, $suffix) = array_pad($credentials, 3, null);

        // Use the user account suffix if no administrator account suffix is given.
        $suffix = $suffix ?: $this->configuration->getAccountSuffix();

        $this->bind($username, $password, '', $suffix);
    }

    /**
     * Validates the specified username and password from being empty.
     *
     * @param string $username
     * @param string $password
     *
     * @throws PasswordRequiredException
     * @throws UsernameRequiredException
     */
    protected function validateCredentials($username, $password)
    {
        if (empty($username)) {
            // Check for an empty username.
            throw new UsernameRequiredException('A username must be specified.');
        }

        if (empty($password)) {
            // Check for an empty password.
            throw new PasswordRequiredException('A password must be specified.');
        }
    }
}
