<?php

namespace Adldap;

use Adldap\Connections\Configuration;
use Adldap\Connections\Manager;
use Adldap\Contracts\AdldapInterface;
use Adldap\Contracts\Connections\ConnectionInterface;
use Adldap\Contracts\Connections\ManagerInterface;
use Adldap\Exceptions\InvalidArgumentException;

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
     * Stores the current manager instance.
     *
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function __construct($configuration, $connection = null)
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

        // Set the configuration.
        $this->setConfiguration($configuration);

        if (!$connection instanceof ConnectionInterface) {
            // Create a new LDAP Connection instance if
            // one hasn't been instantiated yet.
            $connection = new Connections\Ldap();
        }

        // Set the connection.
        $this->setConnection($connection);
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
        return new Manager($this->connection, $this->configuration);
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
