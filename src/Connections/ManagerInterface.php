<?php

namespace Adldap\Connections;

use Adldap\Auth\GuardInterface;

interface ManagerInterface
{
    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection
     * @param Configuration       $configuration
     */
    public function __construct(ConnectionInterface $connection, Configuration $configuration);

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
     * Sets the current Guard instance.
     *
     * @param GuardInterface $guard
     */
    public function setGuard(GuardInterface $guard);

    /**
     * Get the RootDSE properties from a domain controller.
     *
     * @return array|bool
     */
    public function getRootDse();

    /**
     * Returns a new Groups instance.
     *
     * @return \Adldap\Scopes\Groups
     */
    public function groups();

    /**
     * Returns a new Users instance.
     *
     * @return \Adldap\Scopes\Users
     */
    public function users();

    /**
     * Returns a new Folders instance.
     *
     * @return \Adldap\Scopes\Containers
     */
    public function containers();

    /**
     * Returns a new Contacts instance.
     *
     * @return \Adldap\Scopes\Contacts
     */
    public function contacts();

    /**
     * Returns a new Exchange instance.
     *
     * @return \Adldap\Scopes\ExchangeServers
     */
    public function exchangeServers();

    /**
     * Returns a new Computers instance.
     *
     * @return \Adldap\Scopes\Computers
     */
    public function computers();

    /**
     * Returns a new Printers instance.
     *
     * @return \Adldap\Scopes\Printers
     */
    public function printers();

    /**
     * Returns a new OrganizationalUnits instance.
     *
     * @return \Adldap\Scopes\OrganizationalUnits
     */
    public function ous();

    /**
     * Returns a new Search instance.
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
