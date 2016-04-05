<?php

namespace Adldap\Contracts\Connections;

interface ManagerInterface
{
    /**
     * Adds a provider to the Manager.
     *
     * @param string            $name
     * @param ProviderInterface $provider
     *
     * @return ManagerInterface
     */
    public function add($name, ProviderInterface $provider);

    /**
     * Retrieves a Provider from the Manager using it's specified name.
     *
     * @param string $name
     *
     * @throws \Adldap\Exceptions\AdldapException
     *
     * @return ProviderInterface
     */
    public function get($name);

    /**
     * Sets the default provider.
     *
     * @param string $name
     *
     * @throws \Adldap\Exceptions\AdldapException
     */
    public function setDefault($name);

    /**
     * Retrieves the first provider in the Manager.
     *
     * @throws \Adldap\Exceptions\AdldapException
     *
     * @return ProviderInterface
     */
    public function getDefault();

    /**
     * Returns all of the Manager providers.
     *
     * @return array
     */
    public function all();

    /**
     * Removes a provider from the Manager.
     *
     * @param string $name
     *
     * @return ManagerInterface
     */
    public function remove($name);
}
