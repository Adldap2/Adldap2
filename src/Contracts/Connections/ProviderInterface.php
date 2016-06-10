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
      * @param Configuration|array       $configuration
      * @param ConnectionInterface|null  $connection
      * @param SchemaInterface|null      $schema
      */
     public function __construct($configuration, ConnectionInterface $connection, SchemaInterface $schema = null);

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
    public function setConnection(ConnectionInterface $connection = null);

    /**
     * Sets the current configuration.
     *
     * @param Configuration|array $configuration
     */
    public function setConfiguration($configuration = []);

    /**
     * Sets the current LDAP attribute schema.
     *
     * @param SchemaInterface|null $schema
     */
    public function setSchema(SchemaInterface $schema = null);

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
     * @deprecated since v6.0.9
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
     * Returns a new Auth Guard instance.
     *
     * @return \Adldap\Auth\Guard
     */
    public function auth();

    /**
     * Connects and Binds to the Domain Controller.
     *
     * If no username or password is specified, then the
     * configured administrator credentials are used.
     *
     * @param string|null $username
     * @param string|null $password
     *
     * @throws \Adldap\Exceptions\Auth\BindException
     *
     * @return ProviderInterface
     */
    public function connect($username = null, $password = null);
}
