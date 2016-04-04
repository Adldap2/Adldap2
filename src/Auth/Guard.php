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
        if (empty($username)) {
            // Allow binding with null username.
            $username = null;
        } else {
            // If the username isn't empty, we'll append the configured
            // account prefix and suffix to bind to the LDAP server.
            if (is_null($prefix)) {
                $prefix = $this->configuration->getAccountPrefix();
            }

            if (is_null($suffix)) {
                $suffix = $this->configuration->getAccountSuffix();
            }

            $username = $prefix.$username.$suffix;
        }

        if (empty($password)) {
            // Allow binding with null password.
            $password = null;
        }

        if (!@$this->connection->bind($username, $password)) {
            $error = $this->connection->getLastError();

            if ($this->connection->isUsingSSL() && $this->connection->isUsingTLS() === false) {
                $message = 'Bind to Active Directory failed. Either the LDAP SSL connection failed or the login credentials are incorrect. AD said: '.$error;
            } else {
                $message = 'Bind to Active Directory failed. Check the login credentials and/or server details. AD said: '.$error;
            }

            throw new BindException($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bindAsAdministrator()
    {
        $username = $this->configuration->getAdminUsername();
        $password = $this->configuration->getAdminPassword();
        $suffix = $this->configuration->getAdminAccountSuffix();

        if (empty($suffix)) {
            // Use the user account suffix if no administrator account suffix is given.
            $suffix = $this->configuration->getAccountSuffix();
        }

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
