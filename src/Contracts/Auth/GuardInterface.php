<?php

namespace Adldap\Contracts\Auth;

use Adldap\Connections\Configuration;
use Adldap\Contracts\Connections\ConnectionInterface;

interface GuardInterface
{
    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection
     * @param Configuration       $configuration
     */
    public function __construct(ConnectionInterface $connection, Configuration $configuration);

    /**
     * Returns the filtered REMOTE_USER server variable.
     *
     * @return string
     */
    public function getRemoteUserInput();

    /**
     * Returns the filtered KRB5CCNAME server variable.
     *
     * @return mixed
     */
    public function getKerberosAuthInput();

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
    public function attempt($username, $password, $bindAsUser = false);

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

    /**
     * Binds to the current LDAP server using the
     * configuration administrator credentials.
     *
     * @throws \Adldap\Exceptions\Auth\BindException
     */
    public function bindAsAdministrator();

    /**
     * Binds to the current connection using kerberos.
     *
     * @param string $kerberosCredentials
     *
     * @returns void
     *
     * @throws \Adldap\Exceptions\Auth\BindException
     */
    public function bindUsingKerberos($kerberosCredentials);
}
