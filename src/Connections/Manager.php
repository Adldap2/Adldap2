<?php

namespace Adldap\Connections;

use Adldap\Classes\Computers;
use Adldap\Classes\Contacts;
use Adldap\Classes\Containers;
use Adldap\Classes\Exchange;
use Adldap\Classes\Groups;
use Adldap\Classes\OrganizationalUnits;
use Adldap\Classes\Printers;
use Adldap\Classes\Search;
use Adldap\Classes\Users;
use Adldap\Exceptions\Auth\BindException;
use Adldap\Exceptions\Auth\PasswordRequiredException;
use Adldap\Exceptions\Auth\UsernameRequiredException;
use Adldap\Exceptions\ConnectionException;

class Manager implements ManagerInterface
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
        $this->setConnection($connection);
        $this->setConfiguration($configuration);

        // Prepare the connection.
        $this->prepare();
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->close();
        }
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
    public function getRootDse()
    {
        return $this->search()
            ->setDn(null)
            ->read(true)
            ->whereHas(ActiveDirectory::OBJECT_CLASS)
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function groups()
    {
        return new Groups($this);
    }

    /**
     * {@inheritdoc}
     */
    public function users()
    {
        return new Users($this);
    }

    /**
     * {@inheritdoc}
     */
    public function containers()
    {
        return new Containers($this);
    }

    /**
     * {@inheritdoc}
     */
    public function contacts()
    {
        return new Contacts($this);
    }

    /**
     * {@inheritdoc}
     */
    public function exchange()
    {
        return new Exchange($this);
    }

    /**
     * {@inheritdoc}
     */
    public function computers()
    {
        return new Computers($this);
    }

    /**
     * {@inheritdoc}
     */
    public function printers()
    {
        return new Printers($this);
    }

    /**
     * {@inheritdoc}
     */
    public function ous()
    {
        return new OrganizationalUnits($this);
    }

    /**
     * {@inheritdoc}
     */
    public function search()
    {
        return new Search($this);
    }

    /**
     * {@inheritdoc}
     */
    public function connect($username = null, $password = null)
    {
        $controllers = $this->getConfiguration()->getDomainControllers();

        $port = $this->getConfiguration()->getPort();

        // Connect to the LDAP server.
        if ($this->getConnection()->connect($controllers, $port)) {
            $protocol = 3;
            $followReferrals = $this->getConfiguration()->getFollowReferrals();

            // Set the LDAP options.
            $this->getConnection()->setOption(LDAP_OPT_PROTOCOL_VERSION, $protocol);
            $this->getConnection()->setOption(LDAP_OPT_REFERRALS, $followReferrals);

            if (is_null($username) && is_null($password)) {
                // If both the username and password are null, we'll connect to the server
                // using the configured administrator username and password.
                $this->bindAsAdministrator();
            } else {
                // Bind to the server with the specified username and password otherwise.
                $this->bindUsingCredentials($username, $password);
            }
        } else {
            throw new ConnectionException('Unable to connect to LDAP server.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($username, $password, $bindAsUser = false)
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
            if ($this->getConfiguration()->getUseSSO()) {
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
            $username .= $this->getConfiguration()->getAccountSuffix();
        }

        if (empty($password)) {
            // Allow binding with null password.
            $password = null;
        }

        if ($this->getConnection()->bind($username, $password) === false) {
            $error = $this->getConnection()->getLastError();

            if ($this->getConnection()->isUsingSSL() && $this->getConnection()->isUsingTLS() === false) {
                $message = 'Bind to Active Directory failed. Either the LDAP SSL connection failed or the login credentials are incorrect. AD said: '.$error;
            } else {
                $message = 'Bind to Active Directory failed. Check the login credentials and/or server details. AD said: '.$error;
            }

            throw new BindException($message);
        }
    }

    /**
     * Prepares the connection by setting configured parameters.
     *
     * @return void
     */
    protected function prepare()
    {
        // Set the beginning protocol options on the connection
        // if they're set in the configuration.
        if ($this->getConfiguration()->getUseSSL()) {
            $this->getConnection()->useSSL();
        } elseif ($this->getConfiguration()->getUseTLS()) {
            $this->getConnection()->useTLS();
        }

        // If we've set SSO to true, we'll make sure we check if
        // SSO is supported, and if so we'll bind it to
        // the current LDAP connection.
        if ($this->getConfiguration()->getUseSSO() && $this->getConnection()->isSaslSupported()) {
            $this->getConnection()->useSSO();
        }
    }

    /**
     * Binds to the current LDAP server using the
     * configuration administrator credentials.
     *
     * @throws BindException
     */
    private function bindAsAdministrator()
    {
        $adminUsername = $this->getConfiguration()->getAdminUsername();
        $adminPassword = $this->getConfiguration()->getAdminPassword();

        $this->bindUsingCredentials($adminUsername, $adminPassword);
    }

    /**
     * Binds to the current connection using kerberos.
     *
     * @param string $kerberosCredentials
     *
     * @returns void
     *
     * @throws BindException
     */
    private function bindUsingKerberos($kerberosCredentials)
    {
        $key = 'KRB5CCNAME=';

        putenv($key.$kerberosCredentials);

        if ($this->getConnection()->bind(null, null, true) === false) {
            $error = $this->getConnection()->getLastError();

            $message = "Bind to Active Directory failed. AD said: $error";

            throw new BindException($message);
        }
    }
}
