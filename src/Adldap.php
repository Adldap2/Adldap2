<?php

namespace Adldap;

use Adldap\Connections\Manager;
use Adldap\Contracts\AdldapInterface;
use Adldap\Contracts\Connections\ManagerInterface;
use Adldap\Contracts\Connections\ProviderInterface;

class Adldap implements AdldapInterface
{
    /**
     * Stores the current Manager instance.
     *
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerInterface $manager = null)
    {
        $this->setManager($manager ?: new Manager());
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function setManager(ManagerInterface $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addProvider($name, ProviderInterface $provider)
    {
        return $this->manager->add($name, $provider);
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider($name)
    {
        return $this->manager->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultProvider()
    {
        return $this->manager->getDefault();
    }

    /**
     * {@inheritdoc}
     */
    public function connect($connection, $username = null, $password = null)
    {
        return $this->manager->get($connection)->connect($username, $password);
    }

    /**
     * Calls non-existent methods on the default provider instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->getDefaultProvider(), $method], $parameters);
    }
}
