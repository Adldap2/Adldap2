<?php

namespace Adldap;

use Adldap\Connections\Configuration;
use Adldap\Connections\ConnectionInterface;
use Adldap\Exceptions\AdldapException;
use Adldap\Exceptions\InvalidArgumentException;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Contracts\Adldap as AdldapContract;

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
     * {@inheritDoc}
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
        // supplied configuration object
        if ($autoConnect) {
            // Set the beginning protocol options on the connection
            // if they're set in the configuration
            if ($this->configuration->getUseSSL()) {
                $this->connection->useSSL();
            } elseif ($this->configuration->getUseTLS()) {
                $this->connection->useTLS();
            }

            // If we've set SSO to true, we'll make sure we check if
            // SSO is supported, and if so we'll bind it to
            // the current LDAP connection.
            if ($this->configuration->getUseSSO()) {
                if ($this->connection->isSaslSupported()) {
                    $this->connection->useSSO();
                }
            }

            // Looks like we're all set. Let's try and connect
            $this->connect();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function __destruct()
    {
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->close();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getConnection()
    {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritDoc}
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritDoc}
     */
    public function getRemoteUserInput()
    {
        return filter_input(INPUT_SERVER, 'REMOTE_USER');
    }

    /**
     * {@inheritDoc}
     */
    public function getKerberosAuthInput()
    {
        return filter_input(INPUT_SERVER, 'KRB5CCNAME');
    }

    /**
     * {@inheritDoc}
     */
    public function groups()
    {
        return new Classes\Groups($this);
    }

    /**
     * {@inheritDoc}
     */
    public function users()
    {
        return new Classes\Users($this);
    }

    /**
     * {@inheritDoc}
     */
    public function containers()
    {
        return new Classes\Containers($this);
    }

    /**
     * {@inheritDoc}
     */
    public function contacts()
    {
        return new Classes\Contacts($this);
    }

    /**
     * {@inheritDoc}
     */
    public function exchange()
    {
        return new Classes\Exchange($this);
    }

    /**
     * {@inheritDoc}
     */
    public function computers()
    {
        return new Classes\Computers($this);
    }

    /**
     * {@inheritDoc}
     */
    public function printers()
    {
        return new Classes\Printers($this);
    }

    /**
     * {@inheritDoc}
     */
    public function ous()
    {
        return new Classes\OrganizationalUnits($this);
    }

    /**
     * {@inheritDoc}
     */
    public function search()
    {
        return new Classes\Search($this);
    }

    /**
     * {@inheritDoc}
     */
    public function connect()
    {
        // Retrieve the controllers from the configuration
        $controllers = $this->configuration->getDomainControllers();

        // Select a random domain controller
        $domainController = $controllers[array_rand($controllers)];

        // Get the LDAP port
        $port = $this->configuration->getPort();

        // Create the LDAP connection
        $this->connection->connect($domainController, $port);

        // Set the LDAP options
        $this->connection->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
        $this->connection->setOption(LDAP_OPT_REFERRALS, $this->configuration->getFollowReferrals());

        // Authenticate to the server
        return $this->authenticate($this->configuration->getAdminUsername(), $this->configuration->getAdminPassword(), true);
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate($username, $password, $preventRebind = false)
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
                // Looks like SSO isn't enabled, we'll bind regularly instead
                $auth = $this->bindUsingCredentials($username, $password);
            }
        } catch (AdldapException $e) {
            if ($preventRebind === true) {
                // Binding failed and we're not allowed
                // to rebind, we'll return false
                return $auth;
            }
        }

        // If we're allowed to rebind, we'll rebind as administrator
        if ($preventRebind === false) {
            $adminUsername = $this->configuration->getAdminUsername();
            $adminPassword = $this->configuration->getAdminPassword();

            $this->bindUsingCredentials($adminUsername, $adminPassword);

            if (!$this->connection->isBound()) {
                throw new AdldapException('Rebind to Active Directory failed. AD said: '.$this->connection->getLastError());
            }
        }

        return $auth;
    }

    /**
     * {@inheritDoc}
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
    private function bindUsingKerberos($kerberosCredentials)
    {
        $key = 'KRB5CCNAME=';

        putenv($key.$kerberosCredentials);

        if (!$this->connection->bind(null, null, true)) {
            $message = 'Bind to Active Directory failed. AD said: '.$this->connection->getLastError();

            throw new AdldapException($message);
        }

        return true;
    }

    /**
     * Binds to the current connection using the
     * inserted credentials.
     *
     * @param string $username
     * @param string $password
     *
     * @returns bool
     *
     * @throws AdldapException
     */
    private function bindUsingCredentials($username, $password)
    {
        // Allow binding with null credentials
        if (empty($username)) {
            $username = null;
        } else {
            $username .= $this->configuration->getAccountSuffix();
        }

        if (empty($password)) {
            $password = null;
        }

        if (!$this->connection->bind($username, $password)) {
            $error = $this->connection->getLastError();

            if ($this->connection->isUsingSSL() && !$this->connection->isUsingTLS()) {
                $message = 'Bind to Active Directory failed. Either the LDAPs connection failed or the login credentials are incorrect. AD said: '.$error;
            } else {
                $message = 'Bind to Active Directory failed. Check the login credentials and/or server details. AD said: '.$error;
            }

            throw new AdldapException($message);
        }

        return true;
    }
}
