<?php

namespace Adldap\Connections;

use Adldap\Exceptions\AdldapException;
use Adldap\Contracts\Connections\ManagerInterface;
use Adldap\Contracts\Connections\ProviderInterface;

class Manager implements ManagerInterface
{
    /**
     * The default provider name.
     *
     * @var string
     */
    protected $default = 'default';

    /**
     * The connection providers.
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

        throw new AdldapException("The connection provider '$name' does not exist.");
    }

    /**
     * {@inheritdoc}
     */
    public function setDefault($name = 'default')
    {
        if ($this->get($name) instanceof ProviderInterface) {
            $this->default = $name;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        return $this->get($this->default);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->providers;
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
