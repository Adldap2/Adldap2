<?php

namespace Adldap\Connections;

use Adldap\Auth\Guard;
use Adldap\Auth\GuardInterface;
use Adldap\Scopes\Computers;
use Adldap\Scopes\Contacts;
use Adldap\Scopes\Containers;
use Adldap\Scopes\ExchangeServers;
use Adldap\Scopes\Groups;
use Adldap\Scopes\OrganizationalUnits;
use Adldap\Scopes\Printers;
use Adldap\Scopes\Users;
use Adldap\Exceptions\ConnectionException;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Search\Factory as SearchFactory;

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
     * @var GuardInterface
     */
    protected $guard;

    /**
     * {@inheritdoc}
     */
    public function __construct(ConnectionInterface $connection, Configuration $configuration)
    {
        $this->setConnection($connection);
        $this->setConfiguration($configuration);

        // Prepare the connection.
        $this->prepareConnection();
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        if ($this->connection instanceof ConnectionInterface && $this->connection->isBound()) {
            $this->connection->close();
        }
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
    public function getGuard()
    {
        if (!$this->guard instanceof GuardInterface) {
            $this->setGuard($this->getDefaultGuard($this->connection, $this->configuration));
        }

        return $this->guard;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultGuard(ConnectionInterface $connection, Configuration $configuration)
    {
        return new Guard($connection, $configuration);
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
    public function setGuard(GuardInterface $guard)
    {
        $this->guard = $guard;
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
    public function exchangeServers()
    {
        return new ExchangeServers($this);
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
        return new SearchFactory($this->connection, $this->configuration->getBaseDn());
    }

    /**
     * {@inheritdoc}
     */
    public function connect($username = null, $password = null)
    {
        $controllers = $this->configuration->getDomainControllers();

        $port = $this->configuration->getPort();

        // Connect to the LDAP server.
        if ($this->connection->connect($controllers, $port)) {
            $protocol = 3;
            $followReferrals = $this->configuration->getFollowReferrals();

            // Set the LDAP options.
            $this->connection->setOption(LDAP_OPT_PROTOCOL_VERSION, $protocol);
            $this->connection->setOption(LDAP_OPT_REFERRALS, $followReferrals);

            // Get the default guard instance.
            $guard = $this->getGuard();

            if (is_null($username) && is_null($password)) {
                // If both the username and password are null, we'll connect to the server
                // using the configured administrator username and password.
                $guard->bindAsAdministrator();
            } else {
                // Bind to the server with the specified username and password otherwise.
                $guard->bindUsingCredentials($username, $password);
            }
        } else {
            throw new ConnectionException('Unable to connect to LDAP server.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function auth()
    {
        // Make sure the connection we've been given
        // is bound before we try to binding to it.
        if (!$this->connection->isBound()) {
            throw new ConnectionException('No connection to an LDAP server is present.');
        }

        return $this->getGuard();
    }

    /**
     * Prepares the connection by setting configured parameters.
     *
     * @return void
     */
    protected function prepareConnection()
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
    }
}
