<?php

namespace Adldap;

use Adldap\Exceptions\AdldapException;
use Adldap\Contracts\AdldapInterface;
use Adldap\Contracts\Connections\ProviderInterface;

class Adldap implements AdldapInterface
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
    public function __construct(array $providers = [])
    {
        foreach ($providers as $name => $provider) {
            $this->addProvider($name, $provider);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addProvider($name, ProviderInterface $provider)
    {
        $this->providers[$name] = $provider;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider($name)
    {
        if (array_key_exists($name, $this->providers)) {
            return $this->providers[$name];
        }

        throw new AdldapException("The connection provider '$name' does not exist.");
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultProvider($name = 'default')
    {
        if ($this->getProvider($name) instanceof ProviderInterface) {
            $this->default = $name;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultProvider()
    {
        return $this->getProvider($this->default);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProvider($name)
    {
        unset($this->providers[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function connect($name, $username = null, $password = null)
    {
        return $this->getProvider($name)->connect($username, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->getDefaultProvider(), $method], $parameters);
    }
}
