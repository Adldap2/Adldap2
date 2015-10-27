<?php

namespace Adldap;

use Adldap\Connections\Manager;
use Adldap\Exceptions\InvalidArgumentException;
use Adldap\Connections\Configuration;
use Adldap\Connections\ConnectionInterface;
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

        // Create a new LDAP Connection instance if one isn't set.
        if (!$connection instanceof ConnectionInterface) {
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
    public function connect($username = null, $password = null)
    {
        $manager = new Manager($this->connection, $this->configuration);

        $manager->connect($username, $password);

        return $manager;
    }
}
