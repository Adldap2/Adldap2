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
    public function getRemoteUserInput()
    {
        return filter_input(INPUT_SERVER, 'REMOTE_USER');
    }

    /**
     * {@inheritdoc}
     */
    public function getKerberosAuthInput()
    {
        return filter_input(INPUT_SERVER, 'KRB5CCNAME');
    }

    /**
     * {@inheritdoc}
     */
    public function attempt($username, $password, $bindAsUser = false)
    {
        if (trim($username) == '') {
            // Check for an empty username.
            throw new UsernameRequiredException('A username must be specified.');
        }

        if (trim($password) == '') {
            // Check for an empty password.
            throw new PasswordRequiredException('A password must be specified.');
        }

        try {
            if ($this->configuration->getUseSSO()) {
                // If SSO is enabled, we'll try binding over kerberos
                $remoteUser = $this->getRemoteUserInput();
                $kerberos = $this->getKerberosAuthInput();

                // If the remote user input equals the username we're
                // trying to authenticate, we'll perform the bind.
                if ($remoteUser == $username) {
                    $this->bindUsingKerberos($kerberos);
                }
            } else {
                // Looks like SSO isn't enabled, we'll bind regularly instead.
                $this->bindUsingCredentials($username, $password);
            }
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
    public function bindUsingCredentials($username, $password)
    {
        if (empty($username)) {
            // Allow binding with null username.
            $username = null;
        } else {
            // If the username isn't empty, we'll append the configured
            // account suffix to bind to the LDAP server.
            $username .= $this->configuration->getAccountSuffix();
        }

        if (empty($password)) {
            // Allow binding with null password.
            $password = null;
        }

        if ($this->connection->bind($username, $password) === false) {
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
        $adminUsername = $this->configuration->getAdminUsername();
        $adminPassword = $this->configuration->getAdminPassword();

        $this->bindUsingCredentials($adminUsername, $adminPassword);
    }

    /**
     * {@inheritdoc}
     */
    public function bindUsingKerberos($kerberosCredentials)
    {
        $key = 'KRB5CCNAME=';

        putenv($key.$kerberosCredentials);

        if ($this->connection->bind(null, null, true) === false) {
            $error = $this->connection->getLastError();

            $message = "Bind to Active Directory failed. AD said: $error";

            throw new BindException($message);
        }
    }
}
