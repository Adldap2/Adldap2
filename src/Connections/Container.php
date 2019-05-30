<?php

namespace Adldap\Connections;

class Container
{
    /**
     * Current instance of ProviderContainer.
     *
     * @var Container
     */
    private static $instance;

    /**
     * Providers in the container.
     *
     * @var array|Provider[]
     */
    private $providers = [];

    /**
     * The name of the default provider.
     *
     * @var string
     */
    private $default = 'default';

    /**
     * Get or set the current instance of ProviderContainer.
     *
     * @return Container
     */
    public static function getInstance()
    {
        return self::$instance ?? self::getNewInstance();
    }

    /**
     * Set and get a new instance of ProviderContainer.
     *
     * @return Container
     */
    public static function getNewInstance()
    {
        return self::$instance = new self();
    }

    /**
     * A new a Provider into the container.
     *
     * @param ProviderInterface $provider
     * @param string            $name
     *
     * @return $this
     */
    public function add(ProviderInterface $provider, string $name = null)
    {
        $this->providers[$name ?? $this->default] = $provider;

        return $this;
    }

    /**
     * Remove a Provider from the container.
     *
     * @param $name
     *
     * @return $this
     */
    public function remove($name)
    {
        if ($this->exists($name)) {
            unset($this->providers[$name]);
        }

        return $this;
    }

    /**
     * Return all of the Provider from the container.
     *
     * @return array|Provider[]
     */
    public function all()
    {
        return $this->providers;
    }

    /**
     * Get a Provider by name or return the default provider.
     *
     * @param string|null $name
     *
     * @throws ContainerException If the given provider does not exist.
     *
     * @return mixed
     */
    public function get(string $name = null)
    {
        $name = $name ?? $this->default;

        if ($this->exists($name)) {
            return $this->providers[$name];
        }

        throw new ContainerException("The connection provider '$name' does not exist.");
    }

    /**
     * Return the default Provider.
     *
     * @return Provider
     */
    public function getDefault()
    {
        return $this->get($this->default);
    }

    /**
     * Checks if the Provider exists.
     *
     * @param $name
     *
     * @return bool
     */
    public function exists($name): bool
    {
        return array_key_exists($name, $this->providers);
    }

    /**
     * Set the default provider name;.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setDefault($name = null)
    {
        $name = $name ?? $this->default;

        if ($this->get($name) instanceof ProviderInterface) {
            $this->default = $name;
        }

        return $this;
    }
}
