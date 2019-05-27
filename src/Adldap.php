<?php

namespace Adldap;

use Adldap\Configuration\DomainConfiguration;
use Adldap\Connections\ConnectionInterface;
use Adldap\Connections\Ldap;
use Adldap\Connections\Provider;
use Adldap\Connections\ProviderContainer;
use Adldap\Connections\ProviderContainerException;
use Adldap\Connections\ProviderInterface;
use Adldap\Events\DispatchesEvents;
use Adldap\Log\EventLogger;
use Adldap\Log\LogsInformation;
use InvalidArgumentException;

class Adldap implements AdldapInterface
{
    use DispatchesEvents, LogsInformation;

    /**
     * The ProviderContainer instance.
     *
     * @var ProviderContainer
     */
    protected $providerContainer;

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
     * The events to register listeners for during initialization.
     *
     * @var array
     */
    protected $listen = [
        'Adldap\Auth\Events\*',
        'Adldap\Query\Events\*',
        'Adldap\Models\Events\*',
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $providers = [])
    {
        $this->providerContainer = ProviderContainer::getNewInstance();

        foreach ($providers as $name => $config) {
            $this->addProvider($config, $name);
        }

        if ($default = key($providers)) {
            $this->setDefaultProvider($default);
        }

        $this->initEventLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function addProvider($config, $name = 'default', ConnectionInterface $connection = null)
    {
        if ($this->isValidConfig($config)) {
            $config = new Provider($config, $connection ?? new Ldap($name));
        }

        if ($config instanceof ProviderInterface) {
            $this->providerContainer->add($config, $name);

            return $this;
        }

        throw new InvalidArgumentException(
            "You must provide a configuration array or an instance of Adldap\Connections\ProviderInterface."
        );
    }

    /**
     * Determines if the given config is valid.
     *
     * @param mixed $config
     *
     * @return bool
     */
    protected function isValidConfig($config)
    {
        return is_array($config) || $config instanceof DomainConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviders()
    {
        return $this->providerContainer->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider($name)
    {
        try {
            return $this->providerContainer->get($name);
        } catch (ProviderContainerException $exception) {
            throw new AdldapException("The connection provider '$name' does not exist.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultProvider($name = 'default')
    {
        $this->providerContainer->setDefault($name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultProvider()
    {
        try {
            return $this->providerContainer->getDefault();
        } catch (ProviderContainerException $exception) {
            throw new AdldapException("No default connection provider exist.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeProvider($name)
    {
        $this->providerContainer->remove($name);

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
        $provider = $this->getDefaultProvider();

        if (!$provider->getConnection()->isBound()) {
            // We'll make sure we have a bound connection before
            // allowing dynamic calls on the default provider.
            $provider->connect();
        }

        return call_user_func_array([$provider, $method], $parameters);
    }

    /**
     * Initializes the event logger.
     *
     * @return void
     */
    public function initEventLogger()
    {
        $dispatcher = static::getEventDispatcher();

        $logger = $this->newEventLogger();

        // We will go through each of our event wildcards and register their listener.
        foreach ($this->listen as $event) {
            $dispatcher->listen($event, function ($eventName, $events) use ($logger) {
                foreach ($events as $event) {
                    $logger->log($event);
                }
            });
        }
    }

    /**
     * Returns a new event logger instance.
     *
     * @return EventLogger
     */
    protected function newEventLogger()
    {
        return new EventLogger(static::getLogger());
    }
}
