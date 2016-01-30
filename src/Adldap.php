<?php

namespace Adldap;

use Adldap\Connections\Configuration;
use Adldap\Connections\Ldap;
use Adldap\Connections\Manager;
use Adldap\Contracts\AdldapInterface;
use Adldap\Contracts\Connections\ConnectionInterface;
use Adldap\Contracts\Connections\ManagerInterface;
use Adldap\Contracts\Schemas\SchemaInterface;
use Adldap\Exceptions\InvalidArgumentException;
use Adldap\Schemas\Schema;

class Adldap implements AdldapInterface
{
    /**
     * Stores the current ldap connection instance.
     *
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * Stores the current configuration instance.
     *
     * @var Configuration
     */
    protected $configuration;

    /**
     * Stores the current LDAP attribute schema.
     *
     * @var SchemaInterface
     */
    protected $schema;

    /**
     * Stores the current manager instance.
     *
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function __construct($configuration = [], $connection = null, SchemaInterface $schema = null)
    {
        // Set the configuration.
        $this->setConfiguration($configuration);

        if (!$connection instanceof ConnectionInterface) {
            // Get the default LDAP Connection instance if
            // one hasn't been instantiated yet.
            $connection = $this->getDefaultConnection();
        }

        if (!$schema instanceof SchemaInterface) {
            // Create a new LDAP Schema instance if
            // one hasn't been instantiated yet.
            $schema = Schema::get();
        }

        // Set the connection.
        $this->setConnection($connection);

        // Set the schema.
        $this->setSchema($schema);
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
    public function setConfiguration($configuration = [])
    {
        if (is_array($configuration)) {
            // If we've been given an array, we'll create
            // a new Configuration instance.
            $configuration = new Configuration($configuration);
        } elseif (!$configuration instanceof Configuration) {
            // Otherwise, if the Configuration isn't a Configuration
            // object, we'll throw an exception.
            $message = sprintf("Configuration must either be an array or an instance of %s", Configuration::class);

            throw new InvalidArgumentException($message);
        }

        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setSchema(SchemaInterface $schema)
    {
        $this->schema = $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        if (!$this->manager instanceof ManagerInterface) {
            $this->setManager($this->getDefaultManager());
        }

        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultManager()
    {
        return new Manager($this->connection, $this->configuration, $this->schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultConnection()
    {
        return new Ldap();
    }

    /**
     * {@inheritdoc}
     */
    public function setManager(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function connect($username = null, $password = null)
    {
        $manager = $this->getManager();

        $manager->connect($username, $password);

        return $manager;
    }
}
