<?php

namespace Adldap\Contracts;

use Adldap\Connections\Configuration;
use Adldap\Connections\ConnectionInterface;
use Adldap\Connections\ManagerInterface;

interface Adldap
{
    /**
     * Constructor.
     *
     * Tries to bind to the AD domain over LDAP or LDAPs
     *
     * @param array|Configuration $configuration The Adldap configuration options array
     * @param ConnectionInterface $connection    The connection you'd like to use
     *
     * @throws \Adldap\Exceptions\AdldapException
     * @throws \InvalidArgumentException
     */
    public function __construct($configuration, $connection = null);

    /**
     * Get the active LDAP Connection.
     *
     * @return bool|ConnectionInterface
     */
    public function getConnection();

    /**
     * Sets the connection property.
     *
     * @param ConnectionInterface $connection
     */
    public function setConnection(ConnectionInterface $connection);

    /**
     * Returns the configuration object.
     *
     * @return Configuration
     */
    public function getConfiguration();

    /**
     * Sets the configuration property.
     *
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration);

    /**
     * Returns the current manager instance.
     *
     * @return ManagerInterface
     */
    public function getManager();

    /**
     * Returns a new instance of the default connection Manager.
     *
     * @return ManagerInterface
     */
    public function getDefaultManager();

    /**
     * Sets the connection manager.
     *
     * @param ManagerInterface $manager
     */
    public function setManager(ManagerInterface $manager);

    /**
     * Connects and binds to the configured LDAP server.
     *
     * Returns a new connection Manager instance on success.
     *
     * @param string|null $username
     * @param string|null $password
     *
     * @return \Adldap\Connections\Manager
     *
     * @throws \Adldap\Exceptions\ConnectionException
     * @throws \Adldap\Exceptions\Auth\BindException
     */
    public function connect($username = null, $password = null);
}
