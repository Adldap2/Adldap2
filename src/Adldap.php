<?php

namespace Adldap;

use Adldap\Connections\Configuration;
use Adldap\Connections\ConnectionInterface;
use Adldap\Contracts\Adldap as AdldapContract;
use Adldap\Exceptions\AdldapException;
use Adldap\Exceptions\InvalidArgumentException;
use Adldap\Schemas\ActiveDirectory;

class Adldap implements AdldapContract
{
    /**
     * Holds the current ldap connection.
     *
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * Holds the current configuration instance.
     *
     * @var Configuration
     */
    protected $configuration;

    /**
     * {@inheritdoc}
     */
    public function __construct($configuration, $connection = null, $autoConnect = true)
    {
        if (is_array($configuration)) {
            // If we've been given an array, we'll create
            // a new Configuration instance.
            $configuration = new Configuration($configuration);
        } elseif (!$configuration instanceof Configuration) {
            // Otherwise, if the Configuration isn't a Configuration
            // object, we'll throw an exception.
            $message = 'Configuration must either be an array or an instance of Adldap\Connections\Configuration';

            throw new InvalidArgumentException($message);
        }

        // Set the configuration
        $this->setConfiguration($configuration);

        // Create a new LDAP Connection if one isn't set
        if (!$connection) {
            $connection = new Connections\Ldap();
        }

        // Set the connection
        $this->setConnection($connection);

        // If we dev wants to connect automatically, we'll construct
        // a new Connection and try to connect using the
        // supplied configuration object.
        if ($autoConnect === true) {
            // Looks like we're all set. Let's try and connect.
            $this->connect();
        }
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
    public function getConnection()
    {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection;
        }

        return false;
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
    public function getConfiguration()
    {
        return $this->configuration;
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
    public function groups()
    {
        return new Classes\Groups($this);
    }

    /**
     * {@inheritdoc}
     */
    public function users()
    {
        return new Classes\Users($this);
    }

    /**
     * {@inheritdoc}
     */
    public function containers()
    {
        return new Classes\Containers($this);
    }

    /**
     * {@inheritdoc}
     */
    public function contacts()
    {
        return new Classes\Contacts($this);
    }

    /**
     * {@inheritdoc}
     */
    public function exchange()
    {
        return new Classes\Exchange($this);
    }

    /**
     * {@inheritdoc}
     */
    public function computers()
    {
        return new Classes\Computers($this);
    }

    /**
     * {@inheritdoc}
     */
    public function printers()
    {
        return new Classes\Printers($this);
    }

    /**
     * {@inheritdoc}
     */
    public function ous()
    {
        return new Classes\OrganizationalUnits($this);
    }

    /**
     * {@inheritdoc}
     */
    public function search()
    {
        return new Classes\Search($this);
    }

    /**
     * {@inheritdoc}
     */
    public function connect($username = null, $password = null)
    {
        // Set the beginning protocol options on the connection
        // if they're set in the configuration.
        if ($this->configuration->getUseSSL()) {
            $this->connection->useSSL();
        } elseif ($this->configuration->getUseTLS()) {
            $this->connection->useTLS();
        }

        // If we've set SSO to true, we'll make sure we check if
        // SSO is supported, and if so we'll bind it to
        // the current LDAP connection.
        if ($this->configuration->getUseSSO() && $this->connection->isSaslSupported()) {
            $this->connection->useSSO();
        }

        // Retrieve the controllers from the configuration.
        $controllers = $this->configuration->getDomainControllers();

        if (count($controllers) === 0) {
            // Make sure we have at least one domain controller.
            throw new AdldapException('You must specify at least one domain controller in your configuration.');
        }

        // Select a random domain controller.
        $controller = $controllers[array_rand($controllers)];

        // Set the controller selected in the configuration so devs
        // can retrieve the domain controller in use if needed.
        $this->configuration->setDomainControllerSelected($controller);

        // Get the LDAP port.
        $port = $this->configuration->getPort();

        // Create the LDAP connection.
        $this->connection->connect($controller, $port);

        // Set the LDAP options.
        $this->connection->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
        $this->connection->setOption(LDAP_OPT_REFERRALS, $this->configuration->getFollowReferrals());

        // If both the username and password are null, we'll connect to the server
        // using the configured administrator username and password.
        if (is_null($username) && is_null($password)) {
            return $this->bindAsAdministrator();
        }

        // Bind as the specified user.
        return $this->bindUsingCredentials($username, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($username, $password, $bindAsUser = false)
    {
        $auth = false;

        try {
            if ($this->configuration->getUseSSO()) {
                // If SSO is enabled, we'll try binding over kerberos
                $remoteUser = $this->getRemoteUserInput();
                $kerberos = $this->getKerberosAuthInput();

                // If the remote user input equals the username we're
                // trying to authenticate, we'll perform the bind
                if ($remoteUser == $username) {
                    $auth = $this->bindUsingKerberos($kerberos);
                }
            } else {
                $this->validateCredentials($username, $password);

                // Looks like SSO isn't enabled, we'll bind regularly instead
                $auth = $this->bindUsingCredentials($username, $password);
            }
        } catch (AdldapException $e) {
            if ($bindAsUser === true) {
                // Binding failed and we're not allowed
                // to rebind, we'll return false
                return $auth;
            }
        }

        // If we're not allowed to bind as the
        // user, we'll rebind as administrator.
        if ($bindAsUser === false) {
            $this->bindAsAdministrator();
        }

        return $auth;
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
     * Binds to the current connection using kerberos.
     *
     * @param string $kerberosCredentials
     *
     * @returns bool
     *
     * @throws AdldapException
     */
    protected function bindUsingKerberos($kerberosCredentials)
    {
        $key = 'KRB5CCNAME=';

        putenv($key.$kerberosCredentials);

        if ($this->connection->bind(null, null, true) === false) {
            $error = $this->connection->getLastError();

            $message = "Bind to Active Directory failed. AD said: $error";

            throw new AdldapException($message);
        }

        return true;
    }

    /**
     * Binds to the current connection using the
     * inserted credentials.
     *
     * @param string      $username
     * @param string      $password
     * @param string|null $suffix
     *
     * @returns bool
     *
     * @throws AdldapException
     */
    protected function bindUsingCredentials($username, $password, $suffix = null)
    {
        if (empty($username)) {
            // Allow binding with null username.
            $username = null;
        } else {
            if (is_null($suffix)) {
                // If the suffix is null, we'll retrieve their
                // account suffix from the configuration.
                $suffix = $this->configuration->getAccountSuffix();
            }

            // If the username isn't empty, we'll append the configured
            // account suffix to bind to the LDAP server.
            $username .= $suffix;
        }

        if (empty($password)) {
            // Allow binding with null password.
            $password = null;
        }

        if ($this->connection->bind($username, $password) === false) {
            $error = $this->connection->getLastError();

            if ($this->connection->isUsingSSL() && $this->connection->isUsingTLS() === false) {
                $message = 'Bind to Active Directory failed. Either the LDAPs connection failed or the login credentials are incorrect. AD said: '.$error;
            } else {
                $message = 'Bind to Active Directory failed. Check the login credentials and/or server details. AD said: '.$error;
            }

            throw new AdldapException($message);
        }

        return true;
    }

    /**
     * Binds to the LDAP server as the configured administrator.
     *
     * @throws AdldapException
     *
     * @return bool
     */
    protected function bindAsAdministrator()
    {
        $adminUsername = $this->configuration->getAdminUsername();
        $adminPassword = $this->configuration->getAdminPassword();
        $adminSuffix = $this->configuration->getAdminAccountSuffix();

        if (empty($adminSuffix)) {
            // If the admin suffix is empty, we'll use the default account suffix.
            $adminSuffix = $this->configuration->getAccountSuffix();
        }

        $this->bindUsingCredentials($adminUsername, $adminPassword, $adminSuffix);

        if ($this->connection->isBound() === false) {
            $error = $this->connection->getLastError();

            throw new AdldapException("Rebind to Active Directory failed. AD said: $error");
        }

        return true;
    }

    /**
     * Validates the specified credentials from being empty.
     *
     * @param string $username
     * @param string $password
     *
     * @throws AdldapException
     */
    protected function validateCredentials($username, $password)
    {
        if (empty($username) || empty($password)) {
            throw new AdldapException('The username or password cannot be empty.');
        }
    }
}
