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
     * @param bool                $autoConnect   Whether or not you want to connect on construct
     *
     * @throws \Adldap\Exceptions\AdldapException
     * @throws \InvalidArgumentException
     */
    public function __construct($configuration, $connection = null, $autoConnect = true);

    /**
     * Destructor.
     *
     * Closes the current LDAP connection if it exists.
     */
    public function __destruct();

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
     * @param string|null $username
     * @param string|null $password
     *
     * @throws \Adldap\Exceptions\AdldapException
     *
     * @return bool
     */
    public function connect($username = null, $password = null);

    /**
     * Authenticates a user using the specified credentials.
     *
     * @param string $username   The users AD username.
     * @param string $password   The users AD password.
     * @param bool   $bindAsUser Whether or not to bind as the user.
     *
     * @throws \Adldap\Exceptions\AdldapException
     *
     * @return bool
     */
    public function authenticate($username, $password, $bindAsUser = false);

    /**
     * Get the RootDSE properties from a domain controller.
     *
     * @return array|bool
     */
    public function getRootDse();
}
