<?php

namespace Adldap\Connections;

use Adldap\Auth\Guard;
use Adldap\Contracts\Auth\GuardInterface;
use Adldap\Contracts\Connections\ConnectionInterface;
use Adldap\Contracts\Connections\ProviderInterface;
use Adldap\Contracts\Schemas\SchemaInterface;
use Adldap\Models\Factory as ModelFactory;
use Adldap\Schemas\Schema;
use Adldap\Search\Factory as SearchFactory;
use InvalidArgumentException;

class Provider implements ProviderInterface
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
     * @var SchemaInterface
     */
    protected $schema;

    /**
     * @var GuardInterface
     */
    protected $guard;

    /**
     * {@inheritdoc}
     */
    public function __construct($configuration = [], ConnectionInterface $connection = null, SchemaInterface $schema = null)
    {
        $this->setConfiguration($configuration);
        $this->setConnection($connection);
        $this->setSchema($schema);
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
    public function setConnection(ConnectionInterface $connection = null)
    {
        $this->connection = $connection ?: new Ldap();

        // Prepare the connection.
        $this->prepareConnection();

        // Instantiate the LDAP connection.
        $this->connection->connect($this->configuration->getDomainControllers(), $this->configuration->getPort());
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration($configuration = [])
    {
        if (is_array($configuration)) {
            // Construct a configuration instance if an array is given.
            $configuration = new Configuration($configuration);
        } elseif (!$configuration instanceof Configuration) {
            $class = Configuration::class;

            throw new InvalidArgumentException("Configuration must be either an array or instance of $class");
        }

        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setSchema(SchemaInterface $schema = null)
    {
        $this->schema = $schema ?: Schema::get();
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return $this->schema;
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
    public function getRootDse()
    {
        return $this->search()->getRootDse();
    }

    /**
     * {@inheritdoc}
     */
    public function make()
    {
        return new ModelFactory($this->search()->getQuery(), $this->schema);
    }

    /**
     * {@inheritdoc}
     */
    public function search()
    {
        return new SearchFactory($this->connection, $this->schema, $this->configuration->getBaseDn());
    }

    /**
     * {@inheritdoc}
     */
    public function auth()
    {
        return $this->getGuard();
    }

    /**
     * {@inheritdoc}
     */
    public function connect($username = null, $password = null)
    {
        // Get the default guard instance.
        $guard = $this->getGuard();

        if (is_null($username) && is_null($password)) {
            // If both the username and password are null, we'll connect to the server
            // using the configured administrator username and password.
            $guard->bindAsAdministrator();
        } else {
            // Bind to the server with the specified username and password otherwise.
            $guard->bind($username, $password);
        }
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

        $this->connection->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
        $this->connection->setOption(LDAP_OPT_REFERRALS, $this->configuration->getFollowReferrals());
    }
}
