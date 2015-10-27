<?php

namespace Adldap\Contracts;

use Adldap\Connections\Configuration;
use Adldap\Connections\ConnectionInterface;

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
