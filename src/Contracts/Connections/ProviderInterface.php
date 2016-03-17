<?php

namespace Adldap\Contracts\Connections;

use Adldap\Connections\Configuration;
use Adldap\Contracts\Auth\GuardInterface;
use Adldap\Contracts\Schemas\SchemaInterface;

interface ProviderInterface
{
    /**
     * Constructor.
     *
     * @param ConnectionInterface  $connection
     * @param Configuration|array  $configuration
     * @param SchemaInterface|null $schema
     */
    public function __construct(ConnectionInterface $connection, $configuration = [], SchemaInterface $schema = null);

    /**
     * Destructor.
     *
     * Closes the current LDAP connection if it exists.
     */
    public function __destruct();

    /**
     * Returns the current connection instance.
     *
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * Returns the current configuration instance.
     *
     * @return Configuration
     */
    public function getConfiguration();

    /**
     * Returns the current Guard instance.
     *
     * @return \Adldap\Auth\Guard
     */
    public function getGuard();

    /**
     * Returns a new default Guard instance.
     *
     * @param ConnectionInterface $connection
     * @param Configuration       $configuration
     *
     * @return \Adldap\Auth\Guard
     */
    public function getDefaultGuard(ConnectionInterface $connection, Configuration $configuration);

    /**
     * Sets the current connection.
     *
     * @param ConnectionInterface $connection
     */
    public function setConnection(ConnectionInterface $connection);

    /**
     * Sets the current configuration.
     *
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration);

    /**
     * Sets the current LDAP attribute schema.
     *
     * @param SchemaInterface $schema
     */
    public function setSchema(SchemaInterface $schema);

    /**
     * Returns the current LDAP attribute schema.
     *
     * @return SchemaInterface
     */
    public function getSchema();

    /**
     * Sets the current Guard instance.
     *
     * @param GuardInterface $guard
     */
    public function setGuard(GuardInterface $guard);

    /**
     * Returns the root DSE entry on the currently connected server.
     *
     * @return \Adldap\Models\Entry|bool
     */
    public function getRootDse();

    /**
     * Returns a new Model factory instance.
     *
     * @return \Adldap\Models\Factory
     */
    public function make();

    /**
     * Returns a new Search factory instance.
     *
     * @return \Adldap\Search\Factory
     */
    public function search();

    /**
     * Connects and Binds to the Domain Controller.
     *
     * If no username or password is specified, then the
     * configured administrator credentials are used.
     *
     * @param string|null $username
     * @param string|null $password
     *
     * @throws \Adldap\Exceptions\ConnectionException
     * @throws \Adldap\Exceptions\Auth\BindException
     *
     * @return void
     */
    public function connect($username = null, $password = null);

    /**
     * Returns a new Auth Guard instance.
     *
     * @throws \Adldap\Exceptions\ConnectionException
     *
     * @return \Adldap\Auth\Guard
     */
    public function auth();
}
