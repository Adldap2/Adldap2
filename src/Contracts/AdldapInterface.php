<?php

namespace Adldap\Contracts;

use Adldap\Contracts\Connections\ProviderInterface;

interface AdldapInterface
{
    /**
     * Add a provider by the specified name.
     *
     * @param string            $name
     * @param ProviderInterface $provider
     *
     * @return AdldapInterface
     */
    public function addProvider($name, ProviderInterface $provider);

    /**
     * Returns all of the connection providers.
     *
     * @return array
     */
    public function getProviders();

    /**
     * Retrieves a Provider using it's specified name.
     *
     * @param string $name
     *
     * @throws \Adldap\Exceptions\AdldapException
     *
     * @return ProviderInterface
     */
    public function getProvider($name);

    /**
     * Sets the default provider.
     *
     * @param string $name
     *
     * @throws \Adldap\Exceptions\AdldapException
     */
    public function setDefaultProvider($name);

    /**
     * Retrieves the first provider.
     *
     * @throws \Adldap\Exceptions\AdldapException
     *
     * @return ProviderInterface
     */
    public function getDefaultProvider();

    /**
     * Removes a provider by the specified name.
     *
     * @param string $name
     *
     * @return AdldapInterface
     */
    public function removeProvider($name);

    /**
     * Connects to the specified provider.
     *
     * If no username and password is given, then providers
     * configured admin credentials are used.
     *
     * @param string $name
     * @param null   $username
     * @param null $password
     *
     * @return ProviderInterface
     */
    public function connect($name, $username = null, $password = null);

    /**
     * Call methods upon the default provider dynamically.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters);
}
