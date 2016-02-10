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
     * @return ProviderInterface
     *
     * @throws \Adldap\Exceptions\AdldapException
     */
    public function get($name);

    /**
     * Removes a provider from the Manager.
     *
     * @param string $name
     *
     * @return ManagerInterface
     */
    public function remove($name);
}
