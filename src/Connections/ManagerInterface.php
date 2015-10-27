<?php

namespace Adldap\Connections;

interface ManagerInterface
{
    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection
     * @param Configuration $configuration
     */
    public function __construct(ConnectionInterface $connection, Configuration $configuration);

    /**
     * Destructor.
     *
     * Closes the current LDAP connection if it exists.
     */
    public function __destruct();

    /**
     * Returns the filtered REMOTE_USER server variable.
     *
     * @return mixed
     */
    public function getRemoteUserInput();

    /**
     * Returns the filtered KRB5CCNAME server variable.
     *
     * @return mixed
     */
    public function getKerberosAuthInput();

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
     * Get the RootDSE properties from a domain controller.
     *
     * @return array|bool
     */
    public function getRootDse();

    /**
     * Returns a new Groups instance.
     *
     * @return \Adldap\Classes\Groups
     */
    public function groups();

    /**
     * Returns a new Users instance.
     *
     * @return \Adldap\Classes\Users
     */
    public function users();

    /**
     * Returns a new Folders instance.
     *
     * @return \Adldap\Classes\Containers
     */
    public function containers();

    /**
     * Returns a new Contacts instance.
     *
     * @return \Adldap\Classes\Contacts
     */
    public function contacts();

    /**
     * Returns a new Exchange instance.
     *
     * @return \Adldap\Classes\Exchange
     */
    public function exchange();

    /**
     * Returns a new Computers instance.
     *
     * @return \Adldap\Classes\Computers
     */
    public function computers();

    /**
     * Returns a new Printers instance.
     *
     * @return \Adldap\Classes\Printers
     */
    public function printers();

    /**
     * Returns a new OrganizationalUnits instance.
     *
     * @return \Adldap\Classes\OrganizationalUnits
     */
    public function ous();

    /**
     * Returns a new Search instance.
     *
     * @return \Adldap\Classes\Search
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
     * Authenticates a user using the specified credentials.
     *
     * @param string $username   The users AD username.
     * @param string $password   The users AD password.
     * @param bool   $bindAsUser Whether or not to bind as the user.
     *
     * @throws \Adldap\Exceptions\Auth\BindException
     * @throws \Adldap\Exceptions\Auth\UsernameRequiredException
     * @throws \Adldap\Exceptions\Auth\PasswordRequiredException
     *
     * @return bool
     */
    public function authenticate($username, $password, $bindAsUser = false);

    /**
     * Binds to the current connection using the
     * inserted credentials.
     *
     * @param string $username
     * @param string $password
     *
     * @returns void
     *
     * @throws \Adldap\Exceptions\Auth\BindException
     */
    public function bindUsingCredentials($username, $password);
}