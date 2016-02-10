<?php

namespace Adldap\Connections;

use Adldap\Contracts\Connections\ManagerInterface;
use Adldap\Contracts\Connections\ProviderInterface;
use Adldap\Exceptions\AdldapException;

class Manager implements ManagerInterface
{
    /**
     * Stores the connection providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * {@inheritdoc}
     */
    public function add($name, ProviderInterface $provider)
    {
        $this->providers[$name] = $provider;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->providers)) {
            return $this->providers[$name];
        }

        throw new AdldapException("The connection '$name' does not exist.");
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        unset($this->providers[$name]);

        return $this;
    }
}
