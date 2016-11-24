<?php

namespace Adldap;

use InvalidArgumentException;
use Adldap\Connections\Provider;
use Adldap\Schemas\SchemaInterface;
use Adldap\Connections\ProviderInterface;
use Adldap\Connections\ConnectionInterface;
use Adldap\Configuration\DomainConfiguration;

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
    public function addProvider($name, $provider = [], ConnectionInterface $connection = null, SchemaInterface $schema = null)
    {
        if (is_array($provider) || $provider instanceof DomainConfiguration) {
            $provider = new Provider($provider, $connection, $schema);
        }

        if ($provider instanceof ProviderInterface) {
            $this->providers[$name] = $provider;

            return $this;
        }

        throw new InvalidArgumentException(
            "You must provide a configuration array or an instance of Adldap\Connections\ProviderInterface."
        );
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
    public function connect($name = null, $username = null, $password = null)
    {
        $provider = $name ? $this->getProvider($name) : $this->getDefaultProvider();

        return $provider->connect($username, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->getDefaultProvider(), $method], $parameters);
    }
}
